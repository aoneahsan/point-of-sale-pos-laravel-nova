<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Authorization policy for Customer model.
 *
 * Controls access to customer management operations.
 */
class CustomerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any customers.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view-customers') ||
               $user->hasAnyRole(['super-admin', 'store-manager', 'cashier']);
    }

    /**
     * Determine if the user can view the customer.
     */
    public function view(User $user, Customer $customer): bool
    {
        // Super admin can view all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Store users can view customers in their store
        return $user->store_id === $customer->store_id;
    }

    /**
     * Determine if the user can create customers.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create-customers') ||
               $user->hasAnyRole(['super-admin', 'store-manager', 'cashier']);
    }

    /**
     * Determine if the user can update the customer.
     */
    public function update(User $user, Customer $customer): bool
    {
        // Super admin can update all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Store users can update customers in their store
        return $user->store_id === $customer->store_id;
    }

    /**
     * Determine if the user can delete the customer.
     */
    public function delete(User $user, Customer $customer): bool
    {
        // Super admin can delete all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Only store managers can delete customers
        if ($user->hasRole('store-manager')) {
            return $user->store_id === $customer->store_id;
        }

        return false;
    }

    /**
     * Determine if the user can restore the customer.
     */
    public function restore(User $user, Customer $customer): bool
    {
        return $this->delete($user, $customer);
    }

    /**
     * Determine if the user can permanently delete the customer.
     */
    public function forceDelete(User $user, Customer $customer): bool
    {
        // Only super admin can force delete
        return $user->hasRole('super-admin');
    }

    /**
     * Determine if the user can manage customer loyalty points.
     */
    public function manageLoyaltyPoints(User $user, Customer $customer): bool
    {
        return $user->hasAnyRole(['super-admin', 'store-manager']) &&
               ($user->hasRole('super-admin') || $user->store_id === $customer->store_id);
    }

    /**
     * Determine if the user can manage customer store credit.
     */
    public function manageStoreCredit(User $user, Customer $customer): bool
    {
        return $user->hasAnyRole(['super-admin', 'store-manager']) &&
               ($user->hasRole('super-admin') || $user->store_id === $customer->store_id);
    }

    /**
     * Determine if the user can export customer data.
     */
    public function export(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'store-manager']);
    }
}
