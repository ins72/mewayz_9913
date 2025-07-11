<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; // For logging errors
use Illuminate\Validation\ValidationException; // For handling validation exceptions

class MewayzPartnershipController extends Controller
{
    public function sendVettingEmail(Request $request)
    {
        // Define your Elastic Email API key and recipient emails
        // IMPORTANT: It is strongly recommended to store your API key in your .env file
        // and access it using env('ELASTIC_EMAIL_API_KEY') for security.
        $apiKey = env('ELASTIC_EMAIL_API_KEY', 'YOUR_ELASTIC_EMAIL_API_KEY_HERE'); // Get from .env, with a fallback
        $toEmails = 'tmonnens@outlook.com'; // Semicolon separated recipients
        $fromEmail = 'no-reply@mewayz.com'; // Sender email for Elastic Email
        $fromName = 'Mewayz Vetting Form'; // Sender name for Elastic Email

        try {
            // 1. Validate the incoming request data
            // Changed 'skills' to 'skill' to match frontend's name="skill[]"
            // Added validation rules for partnershipType and monetary compensation fields
            $validatedData = $request->validate([
                'fullName' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'location' => 'required|string|max:255',
                'linkedin' => 'nullable|url|max:255',
                'confirmation' => 'required|in:yes,no',
                'roleInterest' => 'required|string',
                'experienceYears' => 'required|string',
                'skill' => 'nullable|array', // Corrected: 'skill' (singular) to match name="skill[]"
                'skill.*' => 'string', // Ensure each skill is a string
                'previousCompanies' => 'required|string',
                
                // New validation for Compensation & Commitment section
                'partnershipType' => 'required|in:equity,hybrid,monetary_business',
                'equityAcceptance' => 'required_if:partnershipType,equity,hybrid|string',
                'equityExpectation' => 'required_if:partnershipType,equity,hybrid|string',
                'upfrontFee' => 'nullable|numeric|min:0', // Only if monetary_business, but nullable if not.
                'monthlyFee' => 'nullable|numeric|min:0', // Only if monetary_business, but nullable if not.
                'businessDescription' => 'nullable|string', // Only if monetary_business, but nullable if not.
                
                'timeCommitment' => 'required|string',
                'platformToolExperience' => 'required|string',
                'creatorEntrepreneurExperience' => 'required|string',
                'mewayzGrowthVision' => 'required|string',
                'network' => 'required|string',
                'valueProposition' => 'required|string',
                'challenges' => 'nullable|string',
                'whyNow' => 'required|string',
                'financialSituation' => 'required|string',
                'availability' => 'required|string',
            ]);

            // 2. Generate the application summary report on the server-side.
            // This ensures the report is based on validated server-side data.
            $resultsReport = $this->generateResultsServerSide($validatedData); // Call a helper method to format the report

            $subject = 'Mewayz Partnership Application - ' . ($validatedData['fullName'] ?: 'Unknown Applicant');
            $bodyText = $resultsReport; // The content of the email

            // 3. Make a POST request to the Elastic Email API
            $response = Http::asForm()->post('https://api.elasticemail.com/v2/email/send', [
                'apikey' => $apiKey,
                'subject' => $subject,
                'from' => $fromEmail,
                'fromName' => $fromName,
                'to' => $toEmails,
                'bodyText' => $bodyText,
                'isTransactional' => true, // Mark as transactional if it's an important system email
            ]);

            // 4. Log the raw response body from Elastic Email for debugging
            $rawResponse = $response->body();
            Log::info('Elastic Email Raw Response: ' . $rawResponse);

            // 5. Check if the HTTP request itself was successful (e.g., 2xx status code)
            if ($response->successful()) {
                $isSuccess = false;
                try {
                    $jsonResponse = $response->json();
                    // Elastic Email V2 API often returns {'success': true/false} or just 'true'/'false' string
                    if (isset($jsonResponse['success']) && $jsonResponse['success'] === true) {
                        $isSuccess = true;
                    }
                } catch (\JsonException $e) {
                    // Not a JSON response, check for plain 'true' string
                    if (strcasecmp(trim($rawResponse), 'true') === 0) {
                        $isSuccess = true;
                    }
                }

                if ($isSuccess) {
                    // Return the generated report string as a JSON response
                    return response()->json(['report' => $resultsReport], 200);
                } else {
                    // Log specific error from Elastic Email if not 'true'
                    $errorMessage = 'Elastic Email API reported an issue: ' . $rawResponse;
                    Log::error($errorMessage);
                    return response()->json(['message' => 'Failed to send application email. Please check server logs for details.', 'report' => $resultsReport], 500);
                }
            } else {
                // Log HTTP status and body if the request itself failed (e.g., network error, 4xx/5xx from Elastic Email)
                $errorMessage = 'HTTP Error from Elastic Email API (' . $response->status() . '): ' . $response->body();
                Log::error($errorMessage);
                return response()->json(['message' => 'Failed to connect to email service. Please try again later.', 'report' => $resultsReport], 500);
            }
        } catch (ValidationException $e) {
            // Handle validation errors explicitly
            Log::error('Validation Error during form submission: ' . json_encode($e->errors()));
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422); // 422 Unprocessable Entity
        } catch (\Exception $e) {
            // Catch any unexpected exceptions during the process
            Log::error('Unexpected error during email submission: ' . $e->getMessage());
            return response()->json(['message' => 'An unexpected error occurred during submission. Please try again later.'], 500);
        }
    }

    /**
     * Helper method to generate the application report on the server-side.
     * This mirrors the JavaScript logic for consistency and server-side validation.
     *
     * @param array $data The submitted form data.
     * @return string The formatted report text.
     */
    private function generateResultsServerSide($data)
    {
        $score = 0;
        $flags = [];
        $strengths = [];

        // Ensure skill is an array, as it comes from skill[] in HTML
        $data['skill'] = isset($data['skill']) && is_array($data['skill']) ? $data['skill'] : [];

        // Scoring logic (mirroring frontend, but server-side validation is crucial)
        if (!isset($data['confirmation']) || $data['confirmation'] !== 'yes') {
            $flags[] = "❌ MAJOR RED FLAG: Doesn't understand this is equity partnership";
        }

        // Determine scoring based on partnership type
        if (isset($data['partnershipType'])) {
            if ($data['partnershipType'] === 'equity' || $data['partnershipType'] === 'hybrid') {
                if (isset($data['equityAcceptance']) && $data['equityAcceptance'] === 'no') {
                    $flags[] = "❌ DEAL BREAKER: Needs immediate payment (for equity/hybrid)";
                }

                if (isset($data['equityAcceptance'])) {
                    if ($data['equityAcceptance'] === 'yes-full') {
                        $score += 30;
                        $strengths[] = "✅ Fully comfortable with equity-only";
                    } elseif ($data['equityAcceptance'] === 'yes-conditions') {
                        $score += 10;
                        $flags[] = "⚠️ Some conditions on equity acceptance.";
                    } elseif ($data['equityAcceptance'] === 'hybrid') {
                        $score += 5;
                        $flags[] = "⚠️ Prefers hybrid compensation model.";
                    }
                }

                if (isset($data['equityExpectation'])) {
                    if ($data['equityExpectation'] === '25%+') {
                        $flags[] = "⚠️ High equity expectations (25%+ requested).";
                    } elseif ($data['equityExpectation'] === 'negotiable') {
                        $score += 15;
                        $strengths[] = "✅ Flexible on equity expectations.";
                    } elseif (in_array($data['equityExpectation'], ['1-5%', '5-10%', '10-15%', '15-25%'])) { // Include 15-25%
                        $score += 5;
                    }
                }
            } elseif ($data['partnershipType'] === 'monetary_business') {
                // Scoring for monetary compensation for business/portfolio
                $upfrontFeeProvided = isset($data['upfrontFee']) && $data['upfrontFee'] > 0;
                $monthlyFeeProvided = isset($data['monthlyFee']) && $data['monthlyFee'] > 0;
                $businessDescriptionProvided = isset($data['businessDescription']) && !empty(trim($data['businessDescription']));

                if (!$upfrontFeeProvided && !$monthlyFeeProvided) {
                    $flags[] = "❌ No monetary compensation details provided for business offer.";
                    $score -= 30;
                } else {
                    $strengths[] = "✅ Proposed monetary compensation for business/portfolio.";
                    if ($upfrontFeeProvided) $score += 10;
                    if ($monthlyFeeProvided) $score += 10;
                }
                if (!$businessDescriptionProvided) {
                    $flags[] = "⚠️ Business/Portfolio description not provided for monetary offer.";
                    $score -= 10;
                } else {
                    $strengths[] = "✅ Provided business/portfolio description.";
                }
            }
        }


        if (isset($data['financialSituation'])) {
            if ($data['financialSituation'] === 'stable') {
                $score += 25;
                $strengths[] = "✅ Financially stable, can work for equity";
            } elseif ($data['financialSituation'] === 'some-runway') {
                $score += 10;
                $flags[] = "⚠️ Has some runway, prefers hybrid.";
            } elseif ($data['financialSituation'] === 'need-income') {
                $flags[] = "⚠️ Warning: Needs immediate income.";
                $score -= 20;
            } elseif ($data['financialSituation'] === 'prefer-not-say') {
                $flags[] = "⚠️ Financial situation not disclosed.";
            }
        }

        if (isset($data['experienceYears'])) {
            if ($data['experienceYears'] === '10+') {
                $score += 20;
                $strengths[] = "✅ 10+ years of relevant experience.";
            } elseif ($data['experienceYears'] === '6-10') {
                $score += 15;
                $strengths[] = "✅ 6-10 years of relevant experience.";
            } elseif ($data['experienceYears'] === '3-5') {
                $score += 10;
                $strengths[] = "✅ 3-5 years of relevant experience.";
            } elseif ($data['experienceYears'] === '0-2') {
                $flags[] = "⚠️ Limited experience (0-2 years).";
            }
        }

        // Updated skill scoring
        if (!empty($data['skill']) && is_array($data['skill'])) {
            $skillScoreMap = [
                'product-strategy' => 5, 'business-development' => 5, 'partnerships' => 5, 'marketing' => 3,
                'tech' => 8, 'fundraising' => 7, 'ai' => 7, 'ecommerce' => 5, 'crm' => 5,
                'community' => 3, 'copywriting' => 4, 'seo' => 6, 'video-editing' => 4,
                'graphic-design' => 4, 'data-analysis' => 6, 'project-management' => 5
            ];
            foreach ($data['skill'] as $s) {
                $score += $skillScoreMap[$s] ?? 0;
            }
            if (count($data['skill']) >= 3) {
                $strengths[] = "✅ Diverse skill set: " . implode(', ', $data['skill']) . ".";
            }
        }


        // Check for critical text fields (using isset and empty for robustness)
        if (!isset($data['previousCompanies']) || empty(trim($data['previousCompanies']))) {
            $flags[] = "⚠️ Previous companies/achievements not provided.";
            $score -= 5;
        }
        if (!isset($data['valueProposition']) || empty(trim($data['valueProposition']))) {
            $flags[] = "⚠️ Value proposition not provided.";
            $score -= 5;
        }
        if (!isset($data['network']) || empty(trim($data['network']))) {
            $flags[] = "⚠️ Professional network description not provided.";
            $score -= 5;
        }
        if (!isset($data['whyNow']) || empty(trim($data['whyNow']))) {
            $flags[] = "⚠️ Reason for joining now not provided.";
            $score -= 5;
        }

        // New fields checks
        if (!isset($data['platformToolExperience']) || empty(trim($data['platformToolExperience']))) {
            $flags[] = "⚠️ Experience with Mewayz-like platform features not provided.";
            $score -= 5;
        } else {
            $strengths[] = "✅ Provided platform/tool experience.";
        }
        if (!isset($data['creatorEntrepreneurExperience']) || empty(trim($data['creatorEntrepreneurExperience']))) {
            $flags[] = "⚠️ Experience with Modern Creators/Online Entrepreneurs not provided.";
            $score -= 5;
        } else {
            $strengths[] = "✅ Provided creator/entrepreneur experience.";
        }
        if (!isset($data['mewayzGrowthVision']) || empty(trim($data['mewayzGrowthVision']))) {
            $flags[] = "⚠️ Strategic vision for Mewayz's growth not provided.";
            $score -= 5;
        } else {
            $strengths[] = "✅ Provided strategic vision for Mewayz.";
        }

        $score = max(0, $score); // Ensure score doesn't go below zero

        return $this->formatReport($data, $score, $flags, $strengths);
    }

    /**
     * Helper method to format the report text.
     *
     * @param array $data The submitted form data.
     * @param int $score The calculated score.
     * @param array $flags Array of flags/warnings.
     * @param array $strengths Array of strengths.
     * @return string The formatted report string.
     */
    private function formatReport($data, $score, $flags, $strengths)
    {
        $priority = 'LOW';
        if ($score >= 70) $priority = 'HIGH';
        else if ($score >= 40) $priority = 'MEDIUM';

        // Safely get skill text, ensuring it's an array for implode
        $skillsText = !empty($data['skill']) && is_array($data['skill']) ? implode(', ', $data['skill']) : 'None selected';

        // Get current time for the report
        $generatedTime = (new \DateTime())->format('Y-m-d H:i:s');

        // Collect compensation details based on type
        $compensationDetails = '';
        if (($data['partnershipType'] ?? '') === 'equity' || ($data['partnershipType'] ?? '') === 'hybrid') {
            $compensationDetails .= "Equity Acceptance: " . ($data['equityAcceptance'] ?? 'Not specified') . "\n";
            $compensationDetails .= "Equity Expectation: " . ($data['equityExpectation'] ?? 'Not specified') . "\n";
        } elseif (($data['partnershipType'] ?? '') === 'monetary_business') {
            $compensationDetails .= "Upfront Fee: " . (isset($data['upfrontFee']) && $data['upfrontFee'] > 0 ? '$' . $data['upfrontFee'] : 'Not specified') . "\n";
            $compensationDetails .= "Monthly Fee: " . (isset($data['monthlyFee']) && $data['monthlyFee'] > 0 ? '$' . $data['monthlyFee'] : 'Not specified') . "\n";
            $compensationDetails .= "Business/Portfolio Description: " . ($data['businessDescription'] ?? 'Not provided') . "\n";
        }


        return "
CANDIDATE ASSESSMENT REPORT 
============================

PRIORITY LEVEL: {$priority} (Score: {$score}/100)

BASIC INFO:
- Name: " . ($data['fullName'] ?? 'Not provided') . "
- Email: " . ($data['email'] ?? 'Not provided') . "
- Location: " . ($data['location'] ?? 'Not provided') . "
- LinkedIn: " . ($data['linkedin'] ?? 'Not provided') . "

ROLE & EXPERIENCE:
- Role Interest: " . ($data['roleInterest'] ?? 'Not specified') . "
- Experience: " . ($data['experienceYears'] ?? 'Not specified') . " years
- Skills: {$skillsText}

COMPENSATION EXPECTATIONS:
- Partnership Type: " . ($data['partnershipType'] ?? 'Not specified') . "
{$compensationDetails}
- Financial Situation: " . ($data['financialSituation'] ?? 'Not specified') . "
- Time Commitment: " . ($data['timeCommitment'] ?? 'Not specified') . "
- Availability: " . ($data['availability'] ?? 'Not specified') . "

STRATEGIC INSIGHTS:
- Platform/Tool Experience: " . ($data['platformToolExperience'] ?? 'Not provided') . "
- Creator/Entrepreneur Experience: " . ($data['creatorEntrepreneurExperience'] ?? 'Not provided') . "
- Mewayz Growth Vision: " . ($data['mewayzGrowthVision'] ?? 'Not provided') . "

RED FLAGS:
" . (count($flags) > 0 ? implode("\n", $flags) : 'None identified') . "

STRENGTHS:
" . (count($strengths) > 0 ? implode("\n", $strengths) : 'Limited strengths identified') . "

PREVIOUS COMPANIES/ACHIEVEMENTS:
" . ($data['previousCompanies'] ?? 'Not provided') . "

VALUE PROPOSITION:
" . ($data['valueProposition'] ?? 'Not provided') . "

NETWORK:
" . ($data['network'] ?? 'Not provided') . "

CHALLENGES PERSPECTIVE:
" . ($data['challenges'] ?? 'Not provided') . "

WHY NOW:
" . ($data['whyNow'] ?? 'Not provided') . "

RECOMMENDATION:
" . ($score >= 70 ? '🟢 PROCEED - Strong candidate, schedule interview' :
    ($score >= 40 ? '🟡 MAYBE - Decent candidate, ask follow-up questions' :
    '🔴 SKIP - Not a good fit, politely decline')) . "

============================
Generated: {$generatedTime}
        ";
    }
}
