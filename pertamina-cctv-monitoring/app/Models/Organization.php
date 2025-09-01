<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'description',
        'logo',
        'primary_color',
        'secondary_color',
        'settings',
        'features',
        'plan',
        'max_users',
        'max_cctvs',
        'is_active',
        'trial_ends_at',
        'subscription_ends_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'features' => 'array',
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($organization) {
            if (empty($organization->slug)) {
                $organization->slug = Str::slug($organization->name);
            }
        });
    }

    /**
     * Get the buildings for the organization
     */
    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class);
    }

    /**
     * Get the users for the organization
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the admins for the organization
     */
    public function admins(): HasMany
    {
        return $this->hasMany(Admin::class);
    }

    /**
     * Get the CCTVs for the organization
     */
    public function cctvs(): HasMany
    {
        return $this->hasMany(Cctv::class);
    }

    /**
     * Get the contacts for the organization
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * Get the primary admin for the organization
     */
    public function primaryAdmin(): HasOne
    {
        return $this->hasOne(Admin::class)->where('is_primary', true);
    }

    /**
     * Check if organization is on trial
     */
    public function isOnTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Check if organization subscription is active
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscription_ends_at && $this->subscription_ends_at->isFuture();
    }

    /**
     * Check if organization can add more users
     */
    public function canAddUser(): bool
    {
        return $this->users()->count() < $this->max_users;
    }

    /**
     * Check if organization can add more CCTVs
     */
    public function canAddCctv(): bool
    {
        return $this->cctvs()->count() < $this->max_cctvs;
    }

    /**
     * Get organization usage statistics
     */
    public function getUsageStats(): array
    {
        return [
            'users_count' => $this->users()->count(),
            'users_limit' => $this->max_users,
            'users_usage_percentage' => round(($this->users()->count() / $this->max_users) * 100, 1),
            'cctvs_count' => $this->cctvs()->count(),
            'cctvs_limit' => $this->max_cctvs,
            'cctvs_usage_percentage' => round(($this->cctvs()->count() / $this->max_cctvs) * 100, 1),
            'buildings_count' => $this->buildings()->count(),
            'is_on_trial' => $this->isOnTrial(),
            'has_active_subscription' => $this->hasActiveSubscription(),
        ];
    }

    /**
     * Get organization feature flags
     */
    public function hasFeature(string $feature): bool
    {
        $features = $this->features ?? [];
        
        return match($feature) {
            'ai_analytics' => in_array($this->plan, ['professional', 'enterprise']),
            'advanced_reporting' => in_array($this->plan, ['enterprise']),
            'api_access' => in_array($this->plan, ['professional', 'enterprise']),
            'custom_branding' => in_array($this->plan, ['enterprise']),
            'priority_support' => in_array($this->plan, ['enterprise']),
            default => in_array($feature, $features),
        };
    }

    /**
     * Get organization settings
     */
    public function getSetting(string $key, $default = null)
    {
        $settings = $this->settings ?? [];
        return $settings[$key] ?? $default;
    }

    /**
     * Set organization setting
     */
    public function setSetting(string $key, $value): void
    {
        $settings = $this->settings ?? [];
        $settings[$key] = $value;
        $this->update(['settings' => $settings]);
    }

    /**
     * Get organization theme colors
     */
    public function getThemeColors(): array
    {
        return [
            'primary' => $this->primary_color,
            'secondary' => $this->secondary_color,
            'primary_light' => $this->adjustBrightness($this->primary_color, 20),
            'primary_dark' => $this->adjustBrightness($this->primary_color, -20),
        ];
    }

    /**
     * Adjust color brightness
     */
    protected function adjustBrightness(string $hex, int $percent): string
    {
        $hex = str_replace('#', '', $hex);
        
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $r = max(0, min(255, $r + ($r * $percent / 100)));
        $g = max(0, min(255, $g + ($g * $percent / 100)));
        $b = max(0, min(255, $b + ($b * $percent / 100)));

        return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT) . 
               str_pad(dechex($g), 2, '0', STR_PAD_LEFT) . 
               str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
    }

    /**
     * Scope for active organizations
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for enterprise organizations
     */
    public function scopeEnterprise($query)
    {
        return $query->where('plan', 'enterprise');
    }

    /**
     * Scope for trial organizations
     */
    public function scopeOnTrial($query)
    {
        return $query->whereNotNull('trial_ends_at')->where('trial_ends_at', '>', now());
    }
}
