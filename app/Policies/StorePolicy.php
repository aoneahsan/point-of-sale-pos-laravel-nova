<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Store;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Authorization policy for Store model.
 *
 * Controls access to store management operations.
 */
class StorePolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any stores.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view stores list
        return true;
    }

    /**
     * Determine if the user can view the store.
     */
    public function view(User $user, Store $store): bool
    {
        // Super admin can view all stores
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Users can view their assigned store
        return $user->store_id === $store->id;
    }

    /**
     * Determine if the user can create stores.
     */
    public function create(User $user): bool
    {
        // Only super admin can create stores
        return $user->hasRole('super-admin');
    }

    /**
     * Determine if the user can update the store.
     */
    public function update(User $user, Store $store): bool
    {
        // Super admin can update all stores
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Store managers can update their own store (limited fields)
        if ($user->hasRole('store-manager')) {
            return $user->store_id === $store->id;
        }

        return false;
    }

    /**
     * Determine if the user can delete the store.
     */
    public function delete(User $user, Store $store): bool
    {
        // Only super admin can delete stores
        return $user->hasRole('super-admin');
    }

    /**
     * Determine if the user can restore the store.
     */
    public function restore(User $user, Store $store): bool
    {
        return $user->hasRole('super-admin');
    }

    /**
     * Determine if the user can permanently delete the store.
     */
    public function forceDelete(User $user, Store $store): bool
    {
        return $user->hasRole('super-admin');
    }

    /**
     * Determine if the user can manage store settings.
     */
    public function manageSettings(User $user, Store $store): bool
    {
        return $user->hasRole('super-admin') ||
               ($user->hasRole('store-manager') && $user->store_id === $store->id);
    }
}
