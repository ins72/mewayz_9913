<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Translation;
use App\Models\Currency;
use App\Models\TaxRate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class InternationalizationController extends Controller
{
    /**
     * Get available languages
     */
    public function getLanguages(Request $request): JsonResponse
    {
        $languages = Language::active()->ordered()->get();
        
        return response()->json([
            'success' => true,
            'data' => $languages->map(function ($language) {
                return [
                    'id' => $language->id,
                    'code' => $language->code,
                    'name' => $language->name,
                    'native_name' => $language->native_name,
                    'flag_icon' => $language->flag_icon,
                    'is_rtl' => $language->is_rtl,
                    'completion_percentage' => $language->getCompletionPercentage()
                ];
            })
        ]);
    }
    
    /**
     * Get translations for a language
     */
    public function getTranslations(Request $request, string $languageCode): JsonResponse
    {
        $namespace = $request->get('namespace', 'default');
        
        $translations = Cache::remember(
            "translations.{$languageCode}.{$namespace}",
            3600,
            function () use ($languageCode, $namespace) {
                return Translation::getTranslations($languageCode, $namespace);
            }
        );
        
        return response()->json([
            'success' => true,
            'data' => $translations
        ]);
    }
    
    /**
     * Update translations
     */
    public function updateTranslations(Request $request, string $languageCode): JsonResponse
    {
        $request->validate([
            'translations' => 'required|array',
            'namespace' => 'string'
        ]);
        
        $namespace = $request->get('namespace', 'default');
        
        $language = Language::where('code', $languageCode)->firstOrFail();
        
        foreach ($request->translations as $key => $value) {
            $language->setTranslation($key, $value, $namespace);
        }
        
        // Clear cache
        Cache::forget("translations.{$languageCode}.{$namespace}");
        
        return response()->json([
            'success' => true,
            'message' => 'Translations updated successfully'
        ]);
    }
    
    /**
     * Get missing translations
     */
    public function getMissingTranslations(Request $request, string $languageCode): JsonResponse
    {
        $language = Language::where('code', $languageCode)->firstOrFail();
        
        $missingKeys = $language->getMissingTranslations();
        
        return response()->json([
            'success' => true,
            'data' => [
                'missing_keys' => $missingKeys,
                'total_missing' => count($missingKeys),
                'completion_percentage' => $language->getCompletionPercentage()
            ]
        ]);
    }
    
    /**
     * Import translations
     */
    public function importTranslations(Request $request, string $languageCode): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:json,csv',
            'namespace' => 'string'
        ]);
        
        $namespace = $request->get('namespace', 'default');
        $file = $request->file('file');
        
        $translations = [];
        
        if ($file->getClientOriginalExtension() === 'json') {
            $content = file_get_contents($file->getRealPath());
            $data = json_decode($content, true);
            
            foreach ($data as $key => $value) {
                $translations[] = [
                    'key' => $key,
                    'value' => $value,
                    'metadata' => []
                ];
            }
        } else {
            // Handle CSV import
            $csvData = array_map('str_getcsv', file($file->getRealPath()));
            $headers = array_shift($csvData);
            
            foreach ($csvData as $row) {
                $translations[] = [
                    'key' => $row[0],
                    'value' => $row[1],
                    'metadata' => []
                ];
            }
        }
        
        Translation::importTranslations($translations, $languageCode, $namespace);
        
        // Clear cache
        Cache::forget("translations.{$languageCode}.{$namespace}");
        
        return response()->json([
            'success' => true,
            'message' => 'Translations imported successfully',
            'data' => [
                'imported_count' => count($translations)
            ]
        ]);
    }
    
    /**
     * Export translations
     */
    public function exportTranslations(Request $request, string $languageCode): JsonResponse
    {
        $namespace = $request->get('namespace', 'default');
        $format = $request->get('format', 'json');
        
        $translations = Translation::exportTranslations($languageCode, $namespace);
        
        $filename = "translations_{$languageCode}_{$namespace}.{$format}";
        
        if ($format === 'json') {
            $content = json_encode(
                collect($translations)->pluck('value', 'key')->toArray(),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            );
        } else {
            // CSV format
            $content = "key,value\n";
            foreach ($translations as $translation) {
                $content .= '"' . $translation['key'] . '","' . $translation['value'] . '"' . "\n";
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'filename' => $filename,
                'content' => $content,
                'mime_type' => $format === 'json' ? 'application/json' : 'text/csv'
            ]
        ]);
    }
    
    /**
     * Get available currencies
     */
    public function getCurrencies(Request $request): JsonResponse
    {
        $currencies = Currency::active()->orderBy('code')->get();
        
        return response()->json([
            'success' => true,
            'data' => $currencies->map(function ($currency) {
                return [
                    'id' => $currency->id,
                    'code' => $currency->code,
                    'name' => $currency->name,
                    'symbol' => $currency->symbol,
                    'exchange_rate' => $currency->exchange_rate,
                    'is_default' => $currency->is_default
                ];
            })
        ]);
    }
    
    /**
     * Get exchange rates
     */
    public function getExchangeRates(Request $request): JsonResponse
    {
        $from = $request->get('from', 'USD');
        $to = $request->get('to');
        
        if ($to) {
            $rate = Currency::getExchangeRate($from, $to);
            return response()->json([
                'success' => true,
                'data' => [
                    'from' => $from,
                    'to' => $to,
                    'rate' => $rate
                ]
            ]);
        }
        
        // Get all rates from the base currency
        $currencies = Currency::active()->get();
        $rates = [];
        
        foreach ($currencies as $currency) {
            $rates[$currency->code] = Currency::getExchangeRate($from, $currency->code);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'base' => $from,
                'rates' => $rates
            ]
        ]);
    }
    
    /**
     * Convert currency
     */
    public function convertCurrency(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'from' => 'required|string|size:3',
            'to' => 'required|string|size:3'
        ]);
        
        $amount = $request->amount;
        $from = $request->from;
        $to = $request->to;
        
        $rate = Currency::getExchangeRate($from, $to);
        $convertedAmount = $amount * $rate;
        
        return response()->json([
            'success' => true,
            'data' => [
                'original_amount' => $amount,
                'converted_amount' => round($convertedAmount, 2),
                'from_currency' => $from,
                'to_currency' => $to,
                'exchange_rate' => $rate
            ]
        ]);
    }
    
    /**
     * Get tax rates by country
     */
    public function getTaxRates(Request $request): JsonResponse
    {
        $countryCode = $request->get('country');
        $stateCode = $request->get('state');
        $type = $request->get('type', 'vat');
        
        $query = TaxRate::active()->effective();
        
        if ($countryCode) {
            $query->byCountry($countryCode);
        }
        
        if ($stateCode) {
            $query->byState($stateCode);
        }
        
        if ($type) {
            $query->byType($type);
        }
        
        $taxRates = $query->get();
        
        return response()->json([
            'success' => true,
            'data' => $taxRates->map(function ($taxRate) {
                return [
                    'id' => $taxRate->id,
                    'name' => $taxRate->name,
                    'country_code' => $taxRate->country_code,
                    'state_code' => $taxRate->state_code,
                    'rate' => $taxRate->rate,
                    'type' => $taxRate->type,
                    'effective_from' => $taxRate->effective_from,
                    'effective_to' => $taxRate->effective_to
                ];
            })
        ]);
    }
    
    /**
     * Calculate tax for amount
     */
    public function calculateTax(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'country_code' => 'required|string|size:2',
            'state_code' => 'nullable|string|max:10',
            'type' => 'string'
        ]);
        
        $amount = $request->amount;
        $countryCode = $request->country_code;
        $stateCode = $request->state_code;
        $type = $request->get('type', 'vat');
        
        $taxCalculation = TaxRate::calculateTax($amount, $countryCode, $stateCode, $type);
        
        return response()->json([
            'success' => true,
            'data' => $taxCalculation
        ]);
    }
    
    /**
     * Get localized formatting options
     */
    public function getLocalizationSettings(Request $request): JsonResponse
    {
        $languageCode = $request->get('language', 'en');
        $countryCode = $request->get('country', 'US');
        
        $language = Language::where('code', $languageCode)->first();
        $currency = Currency::getDefault();
        
        return response()->json([
            'success' => true,
            'data' => [
                'language' => [
                    'code' => $language->code ?? 'en',
                    'name' => $language->name ?? 'English',
                    'is_rtl' => $language->is_rtl ?? false
                ],
                'currency' => [
                    'code' => $currency->code ?? 'USD',
                    'symbol' => $currency->symbol ?? '$',
                    'name' => $currency->name ?? 'US Dollar'
                ],
                'date_format' => $this->getDateFormat($countryCode),
                'time_format' => $this->getTimeFormat($countryCode),
                'number_format' => $this->getNumberFormat($countryCode),
                'first_day_of_week' => $this->getFirstDayOfWeek($countryCode)
            ]
        ]);
    }
    
    /**
     * Get date format for country
     */
    private function getDateFormat(string $countryCode): string
    {
        $formats = [
            'US' => 'MM/DD/YYYY',
            'GB' => 'DD/MM/YYYY',
            'CA' => 'YYYY-MM-DD',
            'DE' => 'DD.MM.YYYY',
            'FR' => 'DD/MM/YYYY',
            'JP' => 'YYYY/MM/DD',
            'CN' => 'YYYY-MM-DD',
            'IN' => 'DD/MM/YYYY',
            'BR' => 'DD/MM/YYYY',
            'AU' => 'DD/MM/YYYY'
        ];
        
        return $formats[$countryCode] ?? 'YYYY-MM-DD';
    }
    
    /**
     * Get time format for country
     */
    private function getTimeFormat(string $countryCode): string
    {
        $formats12h = ['US', 'CA', 'AU', 'PH', 'SA'];
        
        return in_array($countryCode, $formats12h) ? 'h:mm A' : 'HH:mm';
    }
    
    /**
     * Get number format for country
     */
    private function getNumberFormat(string $countryCode): array
    {
        $formats = [
            'US' => ['decimal' => '.', 'thousands' => ','],
            'GB' => ['decimal' => '.', 'thousands' => ','],
            'DE' => ['decimal' => ',', 'thousands' => '.'],
            'FR' => ['decimal' => ',', 'thousands' => ' '],
            'IT' => ['decimal' => ',', 'thousands' => '.'],
            'ES' => ['decimal' => ',', 'thousands' => '.'],
            'BR' => ['decimal' => ',', 'thousands' => '.'],
            'IN' => ['decimal' => '.', 'thousands' => ',']
        ];
        
        return $formats[$countryCode] ?? ['decimal' => '.', 'thousands' => ','];
    }
    
    /**
     * Get first day of week for country
     */
    private function getFirstDayOfWeek(string $countryCode): int
    {
        $mondayFirst = [
            'GB', 'DE', 'FR', 'IT', 'ES', 'NL', 'BE', 'CH', 'AT', 'SE', 'NO', 'DK', 'FI',
            'PL', 'CZ', 'SK', 'HU', 'RO', 'BG', 'HR', 'SI', 'EE', 'LV', 'LT', 'RU', 'UA',
            'BY', 'KZ', 'UZ', 'TJ', 'KG', 'MN', 'CN', 'KR', 'TH', 'VN', 'ID', 'MY', 'SG',
            'PH', 'AU', 'NZ', 'BR', 'AR', 'CL', 'CO', 'PE', 'VE', 'UY', 'PY', 'BO', 'EC',
            'ZA', 'NG', 'KE', 'TZ', 'UG', 'ZM', 'ZW', 'BW', 'NA', 'MZ', 'MG', 'MW', 'RW',
            'BI', 'DJ', 'SO', 'ET', 'ER', 'SD', 'SS', 'CF', 'TD', 'CM', 'GQ', 'GA', 'CG',
            'CD', 'AO', 'ST', 'CV', 'GW', 'GN', 'SL', 'LR', 'CI', 'GH', 'TG', 'BJ', 'NE',
            'BF', 'ML', 'SN', 'GM', 'GY', 'SR', 'GF', 'FK', 'GS'
        ];
        
        return in_array($countryCode, $mondayFirst) ? 1 : 0; // 0 = Sunday, 1 = Monday
    }
}