<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Organization;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class MultiTenantMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Determine organization from domain, subdomain, or header
        $organization = $this->resolveOrganization($request);
        
        if ($organization) {
            // Set organization context
            $this->setOrganizationContext($organization);
            
            // Apply organization-specific configurations
            $this->applyOrganizationConfig($organization);
            
            // Share organization data with views
            view()->share('currentOrganization', $organization);
        }
        
        return $next($request);
    }

    /**
     * Resolve organization from request
     */
    protected function resolveOrganization(Request $request): ?Organization
    {
        // Try to get from cache first
        $cacheKey = 'organization_' . $request->getHost();
        $organization = Cache::remember($cacheKey, 300, function () use ($request) {
            // Method 1: Domain-based resolution
            $organization = $this->resolveByDomain($request);
            
            if ($organization) {
                return $organization;
            }
            
            // Method 2: Subdomain-based resolution
            $organization = $this->resolveBySubdomain($request);
            
            if ($organization) {
                return $organization;
            }
            
            // Method 3: Header-based resolution (for API calls)
            $organization = $this->resolveByHeader($request);
            
            if ($organization) {
                return $organization;
            }
            
            // Method 4: Default organization (for development)
            return $this->getDefaultOrganization();
        });
        
        return $organization;
    }

    /**
     * Resolve organization by domain
     */
    protected function resolveByDomain(Request $request): ?Organization
    {
        $host = $request->getHost();
        
        return Organization::where('domain', $host)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Resolve organization by subdomain
     */
    protected function resolveBySubdomain(Request $request): ?Organization
    {
        $host = $request->getHost();
        $parts = explode('.', $host);
        
        if (count($parts) >= 3) {
            $subdomain = $parts[0];
            
            return Organization::where('slug', $subdomain)
                ->where('is_active', true)
                ->first();
        }
        
        return null;
    }

    /**
     * Resolve organization by header
     */
    protected function resolveByHeader(Request $request): ?Organization
    {
        $organizationId = $request->header('X-Organization-ID');
        
        if ($organizationId) {
            return Organization::where('id', $organizationId)
                ->where('is_active', true)
                ->first();
        }
        
        return null;
    }

    /**
     * Get default organization (for development)
     */
    protected function getDefaultOrganization(): ?Organization
    {
        return Organization::active()->first();
    }

    /**
     * Set organization context
     */
    protected function setOrganizationContext(Organization $organization): void
    {
        // Set organization ID in config
        Config::set('app.current_organization_id', $organization->id);
        
        // Set organization slug in config
        Config::set('app.current_organization_slug', $organization->slug);
        
        // Set organization plan in config
        Config::set('app.current_organization_plan', $organization->plan);
        
        // Store in session for easy access
        session(['current_organization' => $organization->toArray()]);
    }

    /**
     * Apply organization-specific configurations
     */
    protected function applyOrganizationConfig(Organization $organization): void
    {
        // Apply custom settings
        $settings = $organization->settings ?? [];
        
        foreach ($settings as $key => $value) {
            Config::set("organization.{$key}", $value);
        }
        
        // Apply feature flags
        $features = $organization->features ?? [];
        
        foreach ($features as $feature => $enabled) {
            Config::set("features.{$feature}", $enabled);
        }
        
        // Apply theme colors
        $themeColors = $organization->getThemeColors();
        Config::set('organization.theme_colors', $themeColors);
        
        // Apply plan-specific configurations
        $this->applyPlanConfig($organization->plan);
    }

    /**
     * Apply plan-specific configurations
     */
    protected function applyPlanConfig(string $plan): void
    {
        $configs = match($plan) {
            'basic' => [
                'ai_analytics.enabled' => false,
                'advanced_reporting.enabled' => false,
                'api_access.enabled' => false,
                'custom_branding.enabled' => false,
                'priority_support.enabled' => false,
            ],
            'professional' => [
                'ai_analytics.enabled' => true,
                'advanced_reporting.enabled' => false,
                'api_access.enabled' => true,
                'custom_branding.enabled' => false,
                'priority_support.enabled' => false,
            ],
            'enterprise' => [
                'ai_analytics.enabled' => true,
                'advanced_reporting.enabled' => true,
                'api_access.enabled' => true,
                'custom_branding.enabled' => true,
                'priority_support.enabled' => true,
            ],
            default => [
                'ai_analytics.enabled' => false,
                'advanced_reporting.enabled' => false,
                'api_access.enabled' => false,
                'custom_branding.enabled' => false,
                'priority_support.enabled' => false,
            ],
        };
        
        foreach ($configs as $key => $value) {
            Config::set($key, $value);
        }
    }
}
