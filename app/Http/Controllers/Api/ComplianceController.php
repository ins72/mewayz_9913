<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ComplianceReport;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ComplianceController extends Controller
{
    /**
     * Get compliance reports
     */
    public function getReports(Request $request): JsonResponse
    {
        $workspace = $request->user()->workspaces()->first();
        
        $reports = ComplianceReport::where('workspace_id', $workspace->id)
            ->with('generator')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return response()->json([
            'success' => true,
            'data' => $reports->items(),
            'pagination' => [
                'current_page' => $reports->currentPage(),
                'per_page' => $reports->perPage(),
                'total' => $reports->total(),
                'last_page' => $reports->lastPage()
            ]
        ]);
    }
    
    /**
     * Get available report types
     */
    public function getReportTypes(Request $request): JsonResponse
    {
        $reportTypes = ComplianceReport::getAvailableReportTypes();
        
        return response()->json([
            'success' => true,
            'data' => $reportTypes
        ]);
    }
    
    /**
     * Create compliance report
     */
    public function createReport(Request $request): JsonResponse
    {
        $request->validate([
            'report_type' => 'required|in:gdpr,soc2,iso27001,pci_dss,hipaa,ccpa',
            'config' => 'required|array'
        ]);
        
        $workspace = $request->user()->workspaces()->first();
        
        $report = ComplianceReport::create([
            'workspace_id' => $workspace->id,
            'report_type' => $request->report_type,
            'status' => 'pending',
            'config' => $request->config
        ]);
        
        // Generate report asynchronously
        dispatch(function () use ($report) {
            $report->generateReport();
        });
        
        // Log the action
        AuditLog::logAction([
            'workspace_id' => $workspace->id,
            'action' => 'create',
            'resource_type' => 'compliance_report',
            'resource_id' => $report->id,
            'new_values' => $report->toArray()
        ]);
        
        return response()->json([
            'success' => true,
            'data' => $report,
            'message' => 'Compliance report generation started'
        ], 201);
    }
    
    /**
     * Get specific compliance report
     */
    public function getReport(Request $request, string $id): JsonResponse
    {
        $workspace = $request->user()->workspaces()->first();
        
        $report = ComplianceReport::where('id', $id)
            ->where('workspace_id', $workspace->id)
            ->with('generator')
            ->firstOrFail();
        
        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }
    
    /**
     * Delete compliance report
     */
    public function deleteReport(Request $request, string $id): JsonResponse
    {
        $workspace = $request->user()->workspaces()->first();
        
        $report = ComplianceReport::where('id', $id)
            ->where('workspace_id', $workspace->id)
            ->firstOrFail();
        
        $oldValues = $report->toArray();
        
        $report->delete();
        
        // Log the action
        AuditLog::logAction([
            'workspace_id' => $workspace->id,
            'action' => 'delete',
            'resource_type' => 'compliance_report',
            'resource_id' => $id,
            'old_values' => $oldValues
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Compliance report deleted successfully'
        ]);
    }
    
    /**
     * Get compliance dashboard
     */
    public function getDashboard(Request $request): JsonResponse
    {
        $workspace = $request->user()->workspaces()->first();
        
        $recentReports = ComplianceReport::where('workspace_id', $workspace->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        $reportsByType = ComplianceReport::where('workspace_id', $workspace->id)
            ->selectRaw('report_type, COUNT(*) as count')
            ->groupBy('report_type')
            ->get()
            ->pluck('count', 'report_type');
        
        $reportsByStatus = ComplianceReport::where('workspace_id', $workspace->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');
        
        // Get overall compliance score
        $completedReports = ComplianceReport::where('workspace_id', $workspace->id)
            ->where('status', 'completed')
            ->get();
        
        $totalScore = 0;
        $reportCount = 0;
        
        foreach ($completedReports as $report) {
            if (isset($report->findings['compliance_score'])) {
                $totalScore += $report->findings['compliance_score'];
                $reportCount++;
            }
        }
        
        $overallScore = $reportCount > 0 ? round($totalScore / $reportCount, 2) : 0;
        
        return response()->json([
            'success' => true,
            'data' => [
                'overall_compliance_score' => $overallScore,
                'total_reports' => ComplianceReport::where('workspace_id', $workspace->id)->count(),
                'pending_reports' => ComplianceReport::where('workspace_id', $workspace->id)->where('status', 'pending')->count(),
                'completed_reports' => ComplianceReport::where('workspace_id', $workspace->id)->where('status', 'completed')->count(),
                'failed_reports' => ComplianceReport::where('workspace_id', $workspace->id)->where('status', 'failed')->count(),
                'recent_reports' => $recentReports,
                'reports_by_type' => $reportsByType,
                'reports_by_status' => $reportsByStatus
            ]
        ]);
    }
    
    /**
     * Get compliance checklist
     */
    public function getChecklist(Request $request): JsonResponse
    {
        $workspace = $request->user()->workspaces()->first();
        $reportType = $request->get('type', 'gdpr');
        
        $checklist = $this->getComplianceChecklist($workspace, $reportType);
        
        return response()->json([
            'success' => true,
            'data' => $checklist
        ]);
    }
    
    /**
     * Update compliance settings
     */
    public function updateSettings(Request $request): JsonResponse
    {
        $workspace = $request->user()->workspaces()->first();
        
        $request->validate([
            'settings' => 'required|array'
        ]);
        
        $settings = $workspace->settings ?? [];
        $settings = array_merge($settings, $request->settings);
        
        $workspace->update(['settings' => $settings]);
        
        // Log the action
        AuditLog::logAction([
            'workspace_id' => $workspace->id,
            'action' => 'update',
            'resource_type' => 'compliance_settings',
            'resource_id' => $workspace->id,
            'new_values' => $request->settings
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Compliance settings updated successfully'
        ]);
    }
    
    /**
     * Get compliance checklist for report type
     */
    private function getComplianceChecklist($workspace, string $reportType): array
    {
        $settings = $workspace->settings ?? [];
        
        switch ($reportType) {
            case 'gdpr':
                return [
                    'privacy_policy' => [
                        'title' => 'Privacy Policy',
                        'description' => 'Comprehensive privacy policy describing data processing',
                        'completed' => $settings['privacy_policy'] ?? false,
                        'required' => true
                    ],
                    'consent_management' => [
                        'title' => 'Consent Management',
                        'description' => 'System for managing user consent',
                        'completed' => $settings['consent_management'] ?? false,
                        'required' => true
                    ],
                    'data_retention_policy' => [
                        'title' => 'Data Retention Policy',
                        'description' => 'Policy for data retention and deletion',
                        'completed' => $settings['data_retention_policy'] ?? false,
                        'required' => true
                    ],
                    'data_subject_rights' => [
                        'title' => 'Data Subject Rights',
                        'description' => 'Procedures for handling data subject requests',
                        'completed' => $settings['data_subject_rights'] ?? false,
                        'required' => true
                    ],
                    'breach_procedures' => [
                        'title' => 'Breach Procedures',
                        'description' => 'Data breach notification procedures',
                        'completed' => $settings['breach_procedures'] ?? false,
                        'required' => true
                    ]
                ];
                
            case 'soc2':
                return [
                    'access_controls' => [
                        'title' => 'Access Controls',
                        'description' => 'User access control systems',
                        'completed' => $settings['access_controls'] ?? false,
                        'required' => true
                    ],
                    'network_security' => [
                        'title' => 'Network Security',
                        'description' => 'Network security measures',
                        'completed' => $settings['network_security'] ?? false,
                        'required' => true
                    ],
                    'data_encryption' => [
                        'title' => 'Data Encryption',
                        'description' => 'Data encryption at rest and in transit',
                        'completed' => $settings['data_encryption'] ?? false,
                        'required' => true
                    ],
                    'backup_procedures' => [
                        'title' => 'Backup Procedures',
                        'description' => 'Regular backup and recovery procedures',
                        'completed' => $settings['backup_procedures'] ?? false,
                        'required' => true
                    ],
                    'disaster_recovery' => [
                        'title' => 'Disaster Recovery',
                        'description' => 'Disaster recovery plan',
                        'completed' => $settings['disaster_recovery'] ?? false,
                        'required' => true
                    ]
                ];
                
            case 'iso27001':
                return [
                    'security_policy' => [
                        'title' => 'Security Policy',
                        'description' => 'Information security policy',
                        'completed' => $settings['security_policy'] ?? false,
                        'required' => true
                    ],
                    'risk_assessment' => [
                        'title' => 'Risk Assessment',
                        'description' => 'Regular risk assessment procedures',
                        'completed' => $settings['risk_assessment'] ?? false,
                        'required' => true
                    ],
                    'asset_inventory' => [
                        'title' => 'Asset Inventory',
                        'description' => 'Inventory of information assets',
                        'completed' => $settings['asset_inventory'] ?? false,
                        'required' => true
                    ],
                    'access_control_policy' => [
                        'title' => 'Access Control Policy',
                        'description' => 'Access control policy and procedures',
                        'completed' => $settings['access_control_policy'] ?? false,
                        'required' => true
                    ],
                    'incident_management' => [
                        'title' => 'Incident Management',
                        'description' => 'Security incident management procedures',
                        'completed' => $settings['incident_management'] ?? false,
                        'required' => true
                    ]
                ];
                
            case 'pci_dss':
                return [
                    'firewall_configuration' => [
                        'title' => 'Firewall Configuration',
                        'description' => 'Firewall and network security configuration',
                        'completed' => $settings['firewall_configuration'] ?? false,
                        'required' => true
                    ],
                    'cardholder_data_protection' => [
                        'title' => 'Cardholder Data Protection',
                        'description' => 'Protection of stored cardholder data',
                        'completed' => $settings['cardholder_data_protection'] ?? false,
                        'required' => true
                    ],
                    'encryption_in_transit' => [
                        'title' => 'Encryption in Transit',
                        'description' => 'Encryption of cardholder data transmission',
                        'completed' => $settings['encryption_in_transit'] ?? false,
                        'required' => true
                    ],
                    'vulnerability_scanning' => [
                        'title' => 'Vulnerability Scanning',
                        'description' => 'Regular vulnerability scanning',
                        'completed' => $settings['vulnerability_scanning'] ?? false,
                        'required' => true
                    ],
                    'patch_management' => [
                        'title' => 'Patch Management',
                        'description' => 'Security patch management procedures',
                        'completed' => $settings['patch_management'] ?? false,
                        'required' => true
                    ]
                ];
                
            case 'hipaa':
                return [
                    'hipaa_privacy_policy' => [
                        'title' => 'HIPAA Privacy Policy',
                        'description' => 'HIPAA-compliant privacy policy',
                        'completed' => $settings['hipaa_privacy_policy'] ?? false,
                        'required' => true
                    ],
                    'administrative_safeguards' => [
                        'title' => 'Administrative Safeguards',
                        'description' => 'Administrative safeguards for PHI',
                        'completed' => $settings['administrative_safeguards'] ?? false,
                        'required' => true
                    ],
                    'physical_safeguards' => [
                        'title' => 'Physical Safeguards',
                        'description' => 'Physical safeguards for PHI',
                        'completed' => $settings['physical_safeguards'] ?? false,
                        'required' => true
                    ],
                    'technical_safeguards' => [
                        'title' => 'Technical Safeguards',
                        'description' => 'Technical safeguards for PHI',
                        'completed' => $settings['technical_safeguards'] ?? false,
                        'required' => true
                    ],
                    'breach_notification_procedures' => [
                        'title' => 'Breach Notification Procedures',
                        'description' => 'HIPAA breach notification procedures',
                        'completed' => $settings['breach_notification_procedures'] ?? false,
                        'required' => true
                    ]
                ];
                
            case 'ccpa':
                return [
                    'ccpa_right_to_know' => [
                        'title' => 'Right to Know',
                        'description' => 'Procedures for right to know requests',
                        'completed' => $settings['ccpa_right_to_know'] ?? false,
                        'required' => true
                    ],
                    'ccpa_right_to_delete' => [
                        'title' => 'Right to Delete',
                        'description' => 'Procedures for right to delete requests',
                        'completed' => $settings['ccpa_right_to_delete'] ?? false,
                        'required' => true
                    ],
                    'ccpa_right_to_opt_out' => [
                        'title' => 'Right to Opt Out',
                        'description' => 'Procedures for opt-out requests',
                        'completed' => $settings['ccpa_right_to_opt_out'] ?? false,
                        'required' => true
                    ],
                    'data_disclosure_policy' => [
                        'title' => 'Data Disclosure Policy',
                        'description' => 'Policy for data disclosure and sharing',
                        'completed' => $settings['data_disclosure_policy'] ?? false,
                        'required' => true
                    ],
                    'opt_out_mechanism' => [
                        'title' => 'Opt-Out Mechanism',
                        'description' => 'Mechanism for consumers to opt out',
                        'completed' => $settings['opt_out_mechanism'] ?? false,
                        'required' => true
                    ]
                ];
                
            default:
                return [];
        }
    }
}