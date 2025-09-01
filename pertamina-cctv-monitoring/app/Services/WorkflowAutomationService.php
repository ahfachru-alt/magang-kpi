<?php

namespace App\Services;

use App\Models\Cctv;
use App\Models\CctvAnomaly;
use App\Models\Incident;
use App\Models\Workflow;
use App\Models\WorkflowExecution;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class WorkflowAutomationService
{
    protected $notificationService;
    protected $aiService;
    protected $monitoringService;

    public function __construct(
        NotificationService $notificationService,
        AiAnomalyDetectionService $aiService,
        CctvMonitoringService $monitoringService
    ) {
        $this->notificationService = $notificationService;
        $this->aiService = $aiService;
        $this->monitoringService = $monitoringService;
    }

    /**
     * Process incident and trigger automated workflows
     */
    public function processIncident(Incident $incident): array
    {
        try {
            $workflows = $this->getApplicableWorkflows($incident);
            $executions = [];

            foreach ($workflows as $workflow) {
                $execution = $this->executeWorkflow($workflow, $incident);
                $executions[] = $execution;
            }

            // Update incident status
            $this->updateIncidentStatus($incident, $executions);

            return [
                'success' => true,
                'incident_id' => $incident->id,
                'workflows_executed' => count($executions),
                'executions' => $executions,
            ];

        } catch (\Exception $e) {
            Log::error('Workflow automation failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get applicable workflows for an incident
     */
    protected function getApplicableWorkflows(Incident $incident): array
    {
        return Workflow::where('is_active', true)
            ->where('trigger_type', $incident->type)
            ->where('severity_level', '<=', $incident->severity_level)
            ->where(function ($query) use ($incident) {
                $query->where('organization_id', $incident->organization_id)
                      ->orWhereNull('organization_id');
            })
            ->get()
            ->filter(function ($workflow) use ($incident) {
                return $this->evaluateWorkflowConditions($workflow, $incident);
            })
            ->values()
            ->all();
    }

    /**
     * Evaluate workflow conditions
     */
    protected function evaluateWorkflowConditions(Workflow $workflow, Incident $incident): bool
    {
        $conditions = $workflow->conditions ?? [];

        foreach ($conditions as $condition) {
            if (!$this->evaluateCondition($condition, $incident)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Evaluate individual condition
     */
    protected function evaluateCondition(array $condition, Incident $incident): bool
    {
        $field = $condition['field'];
        $operator = $condition['operator'];
        $value = $condition['value'];

        $actualValue = $this->getIncidentValue($incident, $field);

        return match($operator) {
            'equals' => $actualValue == $value,
            'not_equals' => $actualValue != $value,
            'greater_than' => $actualValue > $value,
            'less_than' => $actualValue < $value,
            'contains' => str_contains($actualValue, $value),
            'not_contains' => !str_contains($actualValue, $value),
            'in' => in_array($actualValue, $value),
            'not_in' => !in_array($actualValue, $value),
            'is_null' => is_null($actualValue),
            'is_not_null' => !is_null($actualValue),
            default => false,
        };
    }

    /**
     * Get incident value for condition evaluation
     */
    protected function getIncidentValue(Incident $incident, string $field): mixed
    {
        return match($field) {
            'type' => $incident->type,
            'severity_level' => $incident->severity_level,
            'status' => $incident->status,
            'priority' => $incident->priority,
            'source_type' => $incident->source_type,
            'source_id' => $incident->source_id,
            'location' => $incident->location,
            'assigned_to' => $incident->assigned_to,
            'created_at' => $incident->created_at,
            'updated_at' => $incident->updated_at,
            default => $incident->getAttribute($field),
        };
    }

    /**
     * Execute workflow
     */
    protected function executeWorkflow(Workflow $workflow, Incident $incident): WorkflowExecution
    {
        $execution = WorkflowExecution::create([
            'workflow_id' => $workflow->id,
            'incident_id' => $incident->id,
            'status' => 'running',
            'started_at' => now(),
            'execution_data' => [
                'incident' => $incident->toArray(),
                'workflow' => $workflow->toArray(),
            ],
        ]);

        try {
            $steps = $workflow->steps ?? [];
            $results = [];

            foreach ($steps as $stepIndex => $step) {
                $stepResult = $this->executeWorkflowStep($step, $incident, $execution);
                $results[] = $stepResult;

                // Check if step failed and workflow should stop
                if (!$stepResult['success'] && ($step['continue_on_failure'] ?? false) === false) {
                    break;
                }
            }

            // Update execution status
            $execution->update([
                'status' => 'completed',
                'completed_at' => now(),
                'results' => $results,
                'execution_data' => array_merge($execution->execution_data, [
                    'results' => $results,
                    'completed_at' => now()->toISOString(),
                ]),
            ]);

        } catch (\Exception $e) {
            $execution->update([
                'status' => 'failed',
                'completed_at' => now(),
                'error_message' => $e->getMessage(),
                'execution_data' => array_merge($execution->execution_data, [
                    'error' => $e->getMessage(),
                    'failed_at' => now()->toISOString(),
                ]),
            ]);

            Log::error("Workflow execution failed: {$e->getMessage()}", [
                'workflow_id' => $workflow->id,
                'incident_id' => $incident->id,
                'execution_id' => $execution->id,
            ]);
        }

        return $execution;
    }

    /**
     * Execute individual workflow step
     */
    protected function executeWorkflowStep(array $step, Incident $incident, WorkflowExecution $execution): array
    {
        $stepType = $step['type'];
        $stepConfig = $step['config'] ?? [];

        try {
            $result = match($stepType) {
                'notification' => $this->executeNotificationStep($stepConfig, $incident),
                'status_update' => $this->executeStatusUpdateStep($stepConfig, $incident),
                'assignment' => $this->executeAssignmentStep($stepConfig, $incident),
                'escalation' => $this->executeEscalationStep($stepConfig, $incident),
                'integration' => $this->executeIntegrationStep($stepConfig, $incident),
                'ai_analysis' => $this->executeAiAnalysisStep($stepConfig, $incident),
                'maintenance_schedule' => $this->executeMaintenanceScheduleStep($stepConfig, $incident),
                'report_generation' => $this->executeReportGenerationStep($stepConfig, $incident),
                'custom_action' => $this->executeCustomActionStep($stepConfig, $incident),
                default => [
                    'success' => false,
                    'error' => "Unknown step type: {$stepType}",
                ],
            };

            return array_merge($result, [
                'step_type' => $stepType,
                'step_config' => $stepConfig,
                'executed_at' => now()->toISOString(),
            ]);

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'step_type' => $stepType,
                'step_config' => $stepConfig,
                'executed_at' => now()->toISOString(),
            ];
        }
    }

    /**
     * Execute notification step
     */
    protected function executeNotificationStep(array $config, Incident $incident): array
    {
        $recipients = $config['recipients'] ?? [];
        $message = $this->parseNotificationTemplate($config['message'] ?? '', $incident);
        $priority = $config['priority'] ?? 'normal';

        $notifications = [];

        foreach ($recipients as $recipient) {
            $notification = $this->notificationService->sendNotification(
                $recipient['type'],
                $recipient['id'],
                $config['title'] ?? 'Incident Notification',
                $message,
                $priority,
                [
                    'incident_id' => $incident->id,
                    'incident_type' => $incident->type,
                    'severity' => $incident->severity_level,
                ]
            );

            $notifications[] = $notification;
        }

        return [
            'success' => true,
            'notifications_sent' => count($notifications),
            'notifications' => $notifications,
        ];
    }

    /**
     * Execute status update step
     */
    protected function executeStatusUpdateStep(array $config, Incident $incident): array
    {
        $newStatus = $config['new_status'] ?? 'in_progress';
        $comment = $config['comment'] ?? 'Status updated by workflow automation';

        $incident->update([
            'status' => $newStatus,
            'last_status_change' => now(),
        ]);

        // Add status change log
        $incident->statusLogs()->create([
            'status' => $newStatus,
            'comment' => $comment,
            'changed_by' => 'system',
            'changed_at' => now(),
        ]);

        return [
            'success' => true,
            'new_status' => $newStatus,
            'comment' => $comment,
        ];
    }

    /**
     * Execute assignment step
     */
    protected function executeAssignmentStep(array $config, Incident $incident): array
    {
        $assignmentType = $config['assignment_type'] ?? 'auto';
        $assigneeId = $config['assignee_id'] ?? null;

        if ($assignmentType === 'auto') {
            $assigneeId = $this->findOptimalAssignee($incident);
        }

        if ($assigneeId) {
            $incident->update([
                'assigned_to' => $assigneeId,
                'assigned_at' => now(),
            ]);

            return [
                'success' => true,
                'assigned_to' => $assigneeId,
                'assignment_type' => $assignmentType,
            ];
        }

        return [
            'success' => false,
            'error' => 'No suitable assignee found',
        ];
    }

    /**
     * Execute escalation step
     */
    protected function executeEscalationStep(array $config, Incident $incident): array
    {
        $escalationLevel = $config['escalation_level'] ?? 1;
        $escalationReason = $config['reason'] ?? 'Automatic escalation by workflow';

        // Update incident priority
        $newPriority = $this->calculateEscalatedPriority($incident->priority, $escalationLevel);
        $incident->update(['priority' => $newPriority]);

        // Notify escalation recipients
        $escalationRecipients = $config['recipients'] ?? [];
        $this->notifyEscalation($incident, $escalationRecipients, $escalationReason);

        return [
            'success' => true,
            'escalation_level' => $escalationLevel,
            'new_priority' => $newPriority,
            'reason' => $escalationReason,
        ];
    }

    /**
     * Execute integration step
     */
    protected function executeIntegrationStep(array $config, Incident $incident): array
    {
        $integrationType = $config['integration_type'] ?? '';
        $integrationConfig = $config['config'] ?? [];

        return match($integrationType) {
            'slack' => $this->executeSlackIntegration($incident, $integrationConfig),
            'email' => $this->executeEmailIntegration($incident, $integrationConfig),
            'webhook' => $this->executeWebhookIntegration($incident, $integrationConfig),
            'ticket_system' => $this->executeTicketSystemIntegration($incident, $integrationConfig),
            default => [
                'success' => false,
                'error' => "Unknown integration type: {$integrationType}",
            ],
        };
    }

    /**
     * Execute AI analysis step
     */
    protected function executeAiAnalysisStep(array $config, Incident $incident): array
    {
        try {
            // Get related CCTV data
            $cctv = Cctv::find($incident->source_id);
            
            if (!$cctv) {
                return [
                    'success' => false,
                    'error' => 'Related CCTV not found',
                ];
            }

            // Run AI analysis
            $aiInsights = $this->aiService->analyzeCctvAnomalies($cctv);
            
            // Update incident with AI insights
            $incident->update([
                'ai_insights' => $aiInsights,
                'ai_analysis_at' => now(),
            ]);

            return [
                'success' => true,
                'ai_insights' => $aiInsights,
                'analysis_timestamp' => now()->toISOString(),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'AI analysis failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Execute maintenance schedule step
     */
    protected function executeMaintenanceScheduleStep(array $config, Incident $incident): array
    {
        $maintenanceType = $config['maintenance_type'] ?? 'preventive';
        $scheduledDate = $config['scheduled_date'] ?? now()->addDays(1);
        $duration = $config['duration'] ?? 60; // minutes

        // Create maintenance schedule
        $maintenance = $incident->maintenanceSchedules()->create([
            'type' => $maintenanceType,
            'scheduled_at' => $scheduledDate,
            'estimated_duration' => $duration,
            'reason' => $config['reason'] ?? 'Scheduled by workflow automation',
            'assigned_technician' => $config['technician_id'] ?? null,
            'status' => 'scheduled',
        ]);

        return [
            'success' => true,
            'maintenance_id' => $maintenance->id,
            'scheduled_at' => $scheduledDate->toISOString(),
            'duration' => $duration,
        ];
    }

    /**
     * Execute report generation step
     */
    protected function executeReportGenerationStep(array $config, Incident $incident): array
    {
        $reportType = $config['report_type'] ?? 'incident_summary';
        $recipients = $config['recipients'] ?? [];
        $format = $config['format'] ?? 'pdf';

        try {
            // Generate report
            $report = $this->generateIncidentReport($incident, $reportType, $format);
            
            // Send to recipients
            foreach ($recipients as $recipient) {
                $this->sendReportToRecipient($report, $recipient, $format);
            }

            return [
                'success' => true,
                'report_type' => $reportType,
                'format' => $format,
                'recipients_count' => count($recipients),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Report generation failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Execute custom action step
     */
    protected function executeCustomActionStep(array $config, Incident $incident): array
    {
        $action = $config['action'] ?? '';
        $parameters = $config['parameters'] ?? [];

        try {
            // Execute custom action (could be a webhook, API call, etc.)
            $result = $this->executeCustomAction($action, $parameters, $incident);

            return [
                'success' => true,
                'action' => $action,
                'result' => $result,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Custom action failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Parse notification template with incident data
     */
    protected function parseNotificationTemplate(string $template, Incident $incident): string
    {
        $replacements = [
            '{{incident_id}}' => $incident->id,
            '{{incident_type}}' => $incident->type,
            '{{severity}}' => $incident->severity_level,
            '{{status}}' => $incident->status,
            '{{priority}}' => $incident->priority,
            '{{location}}' => $incident->location,
            '{{description}}' => $incident->description,
            '{{created_at}}' => $incident->created_at->format('Y-m-d H:i:s'),
            '{{assigned_to}}' => $incident->assigned_to ?? 'Unassigned',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * Find optimal assignee for incident
     */
    protected function findOptimalAssignee(Incident $incident): ?int
    {
        // Simple assignment logic - could be enhanced with ML
        $availableAdmins = DB::table('admins')
            ->where('is_active', true)
            ->where('organization_id', $incident->organization_id)
            ->where('incident_count', '<', 10) // Max 10 incidents per admin
            ->orderBy('incident_count', 'asc')
            ->pluck('id')
            ->first();

        return $availableAdmins;
    }

    /**
     * Calculate escalated priority
     */
    protected function calculateEscalatedPriority(string $currentPriority, int $escalationLevel): string
    {
        $priorityLevels = ['low', 'medium', 'high', 'critical'];
        $currentIndex = array_search($currentPriority, $priorityLevels);
        
        if ($currentIndex === false) {
            return 'high';
        }

        $newIndex = min($currentIndex + $escalationLevel, count($priorityLevels) - 1);
        return $priorityLevels[$newIndex];
    }

    /**
     * Update incident status based on workflow executions
     */
    protected function updateIncidentStatus(Incident $incident, array $executions): void
    {
        $successfulExecutions = array_filter($executions, fn($e) => $e['status'] === 'completed');
        $failedExecutions = array_filter($executions, fn($e) => $e['status'] === 'failed');

        if (count($failedExecutions) > 0 && count($successfulExecutions) === 0) {
            $incident->update(['status' => 'workflow_failed']);
        } elseif (count($successfulExecutions) > 0) {
            $incident->update(['status' => 'workflow_processed']);
        }
    }

    /**
     * Generate incident report
     */
    protected function generateIncidentReport(Incident $incident, string $type, string $format): array
    {
        // Implementation would generate actual report
        return [
            'incident_id' => $incident->id,
            'type' => $type,
            'format' => $format,
            'generated_at' => now()->toISOString(),
            'content' => "Report content for incident {$incident->id}",
        ];
    }

    /**
     * Send report to recipient
     */
    protected function sendReportToRecipient(array $report, array $recipient, string $format): void
    {
        // Implementation would send report via email, API, etc.
        Log::info("Report sent to recipient", [
            'recipient' => $recipient,
            'report_type' => $report['type'],
            'format' => $format,
        ]);
    }

    /**
     * Execute custom action
     */
    protected function executeCustomAction(string $action, array $parameters, Incident $incident): mixed
    {
        // Implementation would execute custom actions
        return [
            'action' => $action,
            'parameters' => $parameters,
            'executed_at' => now()->toISOString(),
        ];
    }

    /**
     * Notify escalation
     */
    protected function notifyEscalation(Incident $incident, array $recipients, string $reason): void
    {
        foreach ($recipients as $recipient) {
            $this->notificationService->sendNotification(
                $recipient['type'],
                $recipient['id'],
                'Incident Escalated',
                "Incident {$incident->id} has been escalated. Reason: {$reason}",
                'high',
                [
                    'incident_id' => $incident->id,
                    'escalation_reason' => $reason,
                ]
            );
        }
    }
}