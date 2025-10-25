<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Authorization policy for Brand model.
 *
 * Controls access to brand management operations.
 */
class BrandPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any brands.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view brands
        return true;
    }

    /**
     * Determine if the user can view the brand.
     */
    public function view(User $user, Brand $brand): bool
    {
        // All authenticated users can view individual brands
        return true;
    }

    /**
     * Determine if the user can create brands.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'store-manager', 'inventory-manager']);
    }

    /**
     * Determine if the user can update the brand.
     */
    public function update(User $user, Brand $brand): bool
    {
        return $user->hasAnyRole(['super-admin', 'store-manager', 'inventory-manager']);
    }

    /**
     * Determine if the user can delete the brand.
     */
    public function delete(User $user, Brand $brand): bool
    {
        // Only super admin and store managers can delete brands
        return $user->hasAnyRole(['super-admin', 'store-manager']);
    }

    /**
     * Determine if the user can restore the brand.
     */
    public function restore(User $user, Brand $brand): bool
    {
        return $this->delete($user, $brand);
    }

    /**
     * Determine if the user can permanently delete the brand.
     */
    public function forceDelete(User $user, Brand $brand): bool
    {
        return $user->hasRole('super-admin');
    }
}
