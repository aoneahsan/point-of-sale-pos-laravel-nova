<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Discount;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Authorization policy for Discount model.
 *
 * Controls access to discount and promotion management operations.
 */
class DiscountPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any discounts.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'store-manager', 'cashier']);
    }

    /**
     * Determine if the user can view the discount.
     */
    public function view(User $user, Discount $discount): bool
    {
        // Super admin can view all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Store users can view discounts for their store
        return $user->store_id === $discount->store_id;
    }

    /**
     * Determine if the user can create discounts.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'store-manager']);
    }

    /**
     * Determine if the user can update the discount.
     */
    public function update(User $user, Discount $discount): bool
    {
        // Super admin can update all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Store managers can update discounts for their store
        if ($user->hasRole('store-manager')) {
            return $user->store_id === $discount->store_id;
        }

        return false;
    }

    /**
     * Determine if the user can delete the discount.
     */
    public function delete(User $user, Discount $discount): bool
    {
        // Super admin can delete all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Store managers can delete discounts for their store
        if ($user->hasRole('store-manager')) {
            return $user->store_id === $discount->store_id;
        }

        return false;
    }

    /**
     * Determine if the user can restore the discount.
     */
    public function restore(User $user, Discount $discount): bool
    {
        return $this->delete($user, $discount);
    }

    /**
     * Determine if the user can permanently delete the discount.
     */
    public function forceDelete(User $user, Discount $discount): bool
    {
        return $user->hasRole('super-admin');
    }

    /**
     * Determine if the user can apply the discount.
     */
    public function apply(User $user, Discount $discount): bool
    {
        // Cashiers and above can apply discounts
        return $user->hasAnyRole(['super-admin', 'store-manager', 'cashier']) &&
               ($user->hasRole('super-admin') || $user->store_id === $discount->store_id);
    }
}
