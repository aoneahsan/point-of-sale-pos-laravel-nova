<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\StockAdjustment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Authorization policy for StockAdjustment model.
 *
 * Controls access to stock adjustment operations and approval workflows.
 */
class StockAdjustmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any stock adjustments.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'store-manager', 'inventory-manager']);
    }

    /**
     * Determine if the user can view the stock adjustment.
     */
    public function view(User $user, StockAdjustment $stockAdjustment): bool
    {
        // Super admin can view all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Store users can view adjustments for their store
        return $user->store_id === $stockAdjustment->store_id;
    }

    /**
     * Determine if the user can create stock adjustments.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'store-manager', 'inventory-manager']);
    }

    /**
     * Determine if the user can update the stock adjustment.
     */
    public function update(User $user, StockAdjustment $stockAdjustment): bool
    {
        // Approved adjustments cannot be updated
        if ($stockAdjustment->status === 'approved') {
            return false;
        }

        // Super admin can update all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Store users can update pending adjustments for their store
        return $user->hasAnyRole(['store-manager', 'inventory-manager']) &&
               $user->store_id === $stockAdjustment->store_id;
    }

    /**
     * Determine if the user can approve the stock adjustment.
     */
    public function approve(User $user, StockAdjustment $stockAdjustment): bool
    {
        // Only pending adjustments can be approved
        if ($stockAdjustment->status !== 'pending') {
            return false;
        }

        // Super admin can approve all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Only store managers can approve adjustments
        if ($user->hasRole('store-manager')) {
            return $user->store_id === $stockAdjustment->store_id;
        }

        return false;
    }

    /**
     * Determine if the user can delete the stock adjustment.
     */
    public function delete(User $user, StockAdjustment $stockAdjustment): bool
    {
        // Approved adjustments cannot be deleted
        if ($stockAdjustment->status === 'approved') {
            return false;
        }

        // Super admin can delete all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Only store managers can delete adjustments
        if ($user->hasRole('store-manager')) {
            return $user->store_id === $stockAdjustment->store_id;
        }

        return false;
    }

    /**
     * Determine if the user can restore the stock adjustment.
     */
    public function restore(User $user, StockAdjustment $stockAdjustment): bool
    {
        return $this->delete($user, $stockAdjustment);
    }

    /**
     * Determine if the user can permanently delete the stock adjustment.
     */
    public function forceDelete(User $user, StockAdjustment $stockAdjustment): bool
    {
        return $user->hasRole('super-admin');
    }
}
