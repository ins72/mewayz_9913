<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ComplianceReport extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'workspace_id',
        'report_type',
        'status',
        'config',
        'findings',
        'report_url',
        'generated_at',
        'generated_by'
    ];
    
    protected $casts = [
        'config' => 'array',
        'findings' => 'array',
        'generated_at' => 'datetime'
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }
    
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }
    
    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
    
    public function scopeByType($query, $type)
    {
        return $query->where('report_type', $type);
    }
    
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
    
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
    
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
    
    public static function getAvailableReportTypes(): array
    {
        return [
            'gdpr' => [
                'name' => 'GDPR Compliance Report',
                'description' => 'General Data Protection Regulation compliance assessment',
                'requirements' => ['data_mapping', 'privacy_policy', 'consent_management']
            ],
            'soc2' => [
                'name' => 'SOC 2 Type II Report',
                'description' => 'Service Organization Control 2 security assessment',
                'requirements' => ['security_controls', 'availability', 'confidentiality']
            ],
            'iso27001' => [
                'name' => 'ISO 27001 Compliance Report',
                'description' => 'Information Security Management System assessment',
                'requirements' => ['risk_assessment', 'security_policies', 'incident_management']
            ],
            'pci_dss' => [
                'name' => 'PCI DSS Compliance Report',
                'description' => 'Payment Card Industry Data Security Standard assessment',
                'requirements' => ['network_security', 'data_protection', 'vulnerability_management']
            ],
            'hipaa' => [
                'name' => 'HIPAA Compliance Report',
                'description' => 'Health Insurance Portability and Accountability Act assessment',
                'requirements' => ['privacy_rule', 'security_rule', 'breach_notification']
            ],
            'ccpa' => [
                'name' => 'CCPA Compliance Report',
                'description' => 'California Consumer Privacy Act assessment',
                'requirements' => ['consumer_rights', 'data_disclosure', 'opt_out_mechanisms']
            ]
        ];
    }
    
    public function generateReport(): void
    {
        $this->update(['status' => 'in_progress']);
        
        try {
            $findings = $this->runComplianceChecks();
            
            $this->update([
                'status' => 'completed',
                'findings' => $findings,
                'generated_at' => now(),
                'generated_by' => auth()->id()
            ]);
            
            $this->generateReportFile();
            
        } catch (\Exception $e) {
            $this->update([
                'status' => 'failed',
                'findings' => ['error' => $e->getMessage()]
            ]);
        }
    }
    
    private function runComplianceChecks(): array
    {
        $findings = [];
        
        switch ($this->report_type) {
            case 'gdpr':
                $findings = $this->runGDPRChecks();
                break;
            case 'soc2':
                $findings = $this->runSOC2Checks();
                break;
            case 'iso27001':
                $findings = $this->runISO27001Checks();
                break;
            case 'pci_dss':
                $findings = $this->runPCIDSSChecks();
                break;
            case 'hipaa':
                $findings = $this->runHIPAAChecks();
                break;
            case 'ccpa':
                $findings = $this->runCCPAChecks();
                break;
        }
        
        return $findings;
    }
    
    private function runGDPRChecks(): array
    {
        $findings = [];
        
        // Check for privacy policy
        $findings['privacy_policy'] = $this->workspace->settings['privacy_policy'] ?? false;
        
        // Check for consent management
        $findings['consent_management'] = $this->workspace->settings['consent_management'] ?? false;
        
        // Check for data retention policies
        $findings['data_retention'] = $this->workspace->settings['data_retention_policy'] ?? false;
        
        // Check for data subject rights
        $findings['data_subject_rights'] = $this->workspace->settings['data_subject_rights'] ?? false;
        
        // Check for data breach procedures
        $findings['breach_procedures'] = $this->workspace->settings['breach_procedures'] ?? false;
        
        // Calculate compliance score
        $passed = array_sum($findings);
        $total = count($findings);
        $findings['compliance_score'] = round(($passed / $total) * 100, 2);
        
        return $findings;
    }
    
    private function runSOC2Checks(): array
    {
        $findings = [];
        
        // Security controls
        $findings['access_controls'] = $this->checkAccessControls();
        $findings['network_security'] = $this->checkNetworkSecurity();
        $findings['data_encryption'] = $this->checkDataEncryption();
        
        // Availability
        $findings['backup_procedures'] = $this->checkBackupProcedures();
        $findings['disaster_recovery'] = $this->checkDisasterRecovery();
        
        // Confidentiality
        $findings['data_classification'] = $this->checkDataClassification();
        $findings['information_handling'] = $this->checkInformationHandling();
        
        // Calculate compliance score
        $passed = array_sum($findings);
        $total = count($findings);
        $findings['compliance_score'] = round(($passed / $total) * 100, 2);
        
        return $findings;
    }
    
    private function runISO27001Checks(): array
    {
        $findings = [];
        
        // Information security policies
        $findings['security_policy'] = $this->workspace->settings['security_policy'] ?? false;
        
        // Risk management
        $findings['risk_assessment'] = $this->workspace->settings['risk_assessment'] ?? false;
        
        // Asset management
        $findings['asset_inventory'] = $this->workspace->settings['asset_inventory'] ?? false;
        
        // Access control
        $findings['access_control_policy'] = $this->workspace->settings['access_control_policy'] ?? false;
        
        // Incident management
        $findings['incident_management'] = $this->workspace->settings['incident_management'] ?? false;
        
        // Calculate compliance score
        $passed = array_sum($findings);
        $total = count($findings);
        $findings['compliance_score'] = round(($passed / $total) * 100, 2);
        
        return $findings;
    }
    
    private function runPCIDSSChecks(): array
    {
        $findings = [];
        
        // Network security
        $findings['firewall_configuration'] = $this->checkFirewallConfiguration();
        $findings['network_segmentation'] = $this->checkNetworkSegmentation();
        
        // Data protection
        $findings['cardholder_data_protection'] = $this->checkCardholderDataProtection();
        $findings['encryption_in_transit'] = $this->checkEncryptionInTransit();
        
        // Vulnerability management
        $findings['vulnerability_scanning'] = $this->checkVulnerabilityScanning();
        $findings['patch_management'] = $this->checkPatchManagement();
        
        // Calculate compliance score
        $passed = array_sum($findings);
        $total = count($findings);
        $findings['compliance_score'] = round(($passed / $total) * 100, 2);
        
        return $findings;
    }
    
    private function runHIPAAChecks(): array
    {
        $findings = [];
        
        // Privacy rule
        $findings['privacy_policy'] = $this->workspace->settings['hipaa_privacy_policy'] ?? false;
        $findings['patient_rights'] = $this->workspace->settings['patient_rights'] ?? false;
        
        // Security rule
        $findings['administrative_safeguards'] = $this->workspace->settings['administrative_safeguards'] ?? false;
        $findings['physical_safeguards'] = $this->workspace->settings['physical_safeguards'] ?? false;
        $findings['technical_safeguards'] = $this->workspace->settings['technical_safeguards'] ?? false;
        
        // Breach notification
        $findings['breach_notification_procedures'] = $this->workspace->settings['breach_notification_procedures'] ?? false;
        
        // Calculate compliance score
        $passed = array_sum($findings);
        $total = count($findings);
        $findings['compliance_score'] = round(($passed / $total) * 100, 2);
        
        return $findings;
    }
    
    private function runCCPAChecks(): array
    {
        $findings = [];
        
        // Consumer rights
        $findings['right_to_know'] = $this->workspace->settings['ccpa_right_to_know'] ?? false;
        $findings['right_to_delete'] = $this->workspace->settings['ccpa_right_to_delete'] ?? false;
        $findings['right_to_opt_out'] = $this->workspace->settings['ccpa_right_to_opt_out'] ?? false;
        
        // Data disclosure
        $findings['data_disclosure_policy'] = $this->workspace->settings['data_disclosure_policy'] ?? false;
        
        // Opt-out mechanisms
        $findings['opt_out_mechanism'] = $this->workspace->settings['opt_out_mechanism'] ?? false;
        
        // Calculate compliance score
        $passed = array_sum($findings);
        $total = count($findings);
        $findings['compliance_score'] = round(($passed / $total) * 100, 2);
        
        return $findings;
    }
    
    private function generateReportFile(): void
    {
        // Generate PDF report
        $reportContent = $this->generateReportContent();
        $fileName = "compliance_report_{$this->report_type}_{$this->id}.pdf";
        
        // Save to storage and update report_url
        // This would typically use a PDF generation library
        $this->update(['report_url' => "/storage/reports/{$fileName}"]);
    }
    
    private function generateReportContent(): string
    {
        $reportTypes = self::getAvailableReportTypes();
        $reportInfo = $reportTypes[$this->report_type];
        
        $content = "# {$reportInfo['name']}\n\n";
        $content .= "**Workspace:** {$this->workspace->name}\n";
        $content .= "**Generated:** {$this->generated_at->format('Y-m-d H:i:s')}\n";
        $content .= "**Status:** {$this->status}\n\n";
        
        $content .= "## Findings\n\n";
        foreach ($this->findings as $key => $value) {
            $status = $value ? '✅ PASS' : '❌ FAIL';
            $content .= "- **{$key}:** {$status}\n";
        }
        
        return $content;
    }
    
    // Helper methods for checks
    private function checkAccessControls(): bool
    {
        return $this->workspace->settings['access_controls'] ?? false;
    }
    
    private function checkNetworkSecurity(): bool
    {
        return $this->workspace->settings['network_security'] ?? false;
    }
    
    private function checkDataEncryption(): bool
    {
        return $this->workspace->settings['data_encryption'] ?? false;
    }
    
    private function checkBackupProcedures(): bool
    {
        return $this->workspace->settings['backup_procedures'] ?? false;
    }
    
    private function checkDisasterRecovery(): bool
    {
        return $this->workspace->settings['disaster_recovery'] ?? false;
    }
    
    private function checkDataClassification(): bool
    {
        return $this->workspace->settings['data_classification'] ?? false;
    }
    
    private function checkInformationHandling(): bool
    {
        return $this->workspace->settings['information_handling'] ?? false;
    }
    
    private function checkFirewallConfiguration(): bool
    {
        return $this->workspace->settings['firewall_configuration'] ?? false;
    }
    
    private function checkNetworkSegmentation(): bool
    {
        return $this->workspace->settings['network_segmentation'] ?? false;
    }
    
    private function checkCardholderDataProtection(): bool
    {
        return $this->workspace->settings['cardholder_data_protection'] ?? false;
    }
    
    private function checkEncryptionInTransit(): bool
    {
        return $this->workspace->settings['encryption_in_transit'] ?? false;
    }
    
    private function checkVulnerabilityScanning(): bool
    {
        return $this->workspace->settings['vulnerability_scanning'] ?? false;
    }
    
    private function checkPatchManagement(): bool
    {
        return $this->workspace->settings['patch_management'] ?? false;
    }
}