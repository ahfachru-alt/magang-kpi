<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CctvAnomaly extends Model
{
    use HasFactory;

    protected $fillable = [
        'cctv_id',
        'type',
        'severity',
        'description',
        'data',
        'detected_at',
        'resolved_at',
        'status',
        'resolution_notes',
        'resolved_by',
    ];

    protected $casts = [
        'data' => 'array',
        'detected_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the CCTV that owns the anomaly
     */
    public function cctv(): BelongsTo
    {
        return $this->belongsTo(Cctv::class);
    }

    /**
     * Get the admin who resolved the anomaly
     */
    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'resolved_by');
    }

    /**
     * Scope for active anomalies
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for resolved anomalies
     */
    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    /**
     * Scope for critical anomalies
     */
    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    /**
     * Scope for high severity anomalies
     */
    public function scopeHigh($query)
    {
        return $query->where('severity', 'high');
    }

    /**
     * Mark anomaly as resolved
     */
    public function markAsResolved($adminId, $notes = null)
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolved_by' => $adminId,
            'resolution_notes' => $notes,
        ]);
    }

    /**
     * Get severity color for UI
     */
    public function getSeverityColorAttribute()
    {
        return match($this->severity) {
            'critical' => 'red',
            'high' => 'orange',
            'medium' => 'yellow',
            'low' => 'blue',
            default => 'gray',
        };
    }

    /**
     * Get severity icon for UI
     */
    public function getSeverityIconAttribute()
    {
        return match($this->severity) {
            'critical' => 'exclamation-triangle',
            'high' => 'exclamation-circle',
            'medium' => 'exclamation',
            'low' => 'information-circle',
            default => 'question-mark-circle',
        };
    }

    /**
     * Get anomaly type label
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'response_time_anomaly' => 'Response Time Anomaly',
            'status_pattern_anomaly' => 'Status Pattern Anomaly',
            'usage_pattern_anomaly' => 'Usage Pattern Anomaly',
            'geographic_anomaly' => 'Geographic Anomaly',
            default => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }
}
