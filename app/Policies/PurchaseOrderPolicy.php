<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Authorization policy for PurchaseOrder model.
 *
 * Controls access to purchase order management and receiving operations.
 */
class PurchaseOrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any purchase orders.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'store-manager', 'inventory-manager']);
    }

    /**
     * Determine if the user can view the purchase order.
     */
    public function view(User $user, PurchaseOrder $purchaseOrder): bool
    {
        // Super admin can view all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Store users can view purchase orders for their store
        return $user->store_id === $purchaseOrder->store_id;
    }

    /**
     * Determine if the user can create purchase orders.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'store-manager', 'inventory-manager']);
    }

    /**
     * Determine if the user can update the purchase order.
     */
    public function update(User $user, PurchaseOrder $purchaseOrder): bool
    {
        // Received purchase orders cannot be updated
        if ($purchaseOrder->status === 'received') {
            return false;
        }

        // Super admin can update all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Store users can update purchase orders for their store
        return $user->hasAnyRole(['store-manager', 'inventory-manager']) &&
               $user->store_id === $purchaseOrder->store_id;
    }

    /**
     * Determine if the user can receive the purchase order.
     */
    public function receive(User $user, PurchaseOrder $purchaseOrder): bool
    {
        // Only pending/ordered purchase orders can be received
        if (!in_array($purchaseOrder->status, ['pending', 'ordered'], true)) {
            return false;
        }

        // Super admin can receive all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Store users can receive purchase orders for their store
        return $user->hasAnyRole(['store-manager', 'inventory-manager']) &&
               $user->store_id === $purchaseOrder->store_id;
    }

    /**
     * Determine if the user can delete the purchase order.
     */
    public function delete(User $user, PurchaseOrder $purchaseOrder): bool
    {
        // Received purchase orders cannot be deleted
        if ($purchaseOrder->status === 'received') {
            return false;
        }

        // Super admin can delete all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Only store managers can delete purchase orders
        if ($user->hasRole('store-manager')) {
            return $user->store_id === $purchaseOrder->store_id;
        }

        return false;
    }

    /**
     * Determine if the user can restore the purchase order.
     */
    public function restore(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $this->delete($user, $purchaseOrder);
    }

    /**
     * Determine if the user can permanently delete the purchase order.
     */
    public function forceDelete(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $user->hasRole('super-admin');
    }
}
