<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Authorization policy for Coupon model.
 *
 * Controls access to coupon code management and usage operations.
 */
class CouponPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any coupons.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'store-manager', 'cashier']);
    }

    /**
     * Determine if the user can view the coupon.
     */
    public function view(User $user, Coupon $coupon): bool
    {
        // Super admin can view all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Store users can view coupons for their store
        return $user->store_id === $coupon->store_id;
    }

    /**
     * Determine if the user can create coupons.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'store-manager']);
    }

    /**
     * Determine if the user can update the coupon.
     */
    public function update(User $user, Coupon $coupon): bool
    {
        // Super admin can update all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Store managers can update coupons for their store
        if ($user->hasRole('store-manager')) {
            return $user->store_id === $coupon->store_id;
        }

        return false;
    }

    /**
     * Determine if the user can delete the coupon.
     */
    public function delete(User $user, Coupon $coupon): bool
    {
        // Super admin can delete all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Store managers can delete coupons for their store
        if ($user->hasRole('store-manager')) {
            return $user->store_id === $coupon->store_id;
        }

        return false;
    }

    /**
     * Determine if the user can restore the coupon.
     */
    public function restore(User $user, Coupon $coupon): bool
    {
        return $this->delete($user, $coupon);
    }

    /**
     * Determine if the user can permanently delete the coupon.
     */
    public function forceDelete(User $user, Coupon $coupon): bool
    {
        return $user->hasRole('super-admin');
    }

    /**
     * Determine if the user can apply/validate the coupon.
     */
    public function apply(User $user, Coupon $coupon): bool
    {
        // Cashiers and above can apply coupons
        return $user->hasAnyRole(['super-admin', 'store-manager', 'cashier']) &&
               ($user->hasRole('super-admin') || $user->store_id === $coupon->store_id);
    }
}
