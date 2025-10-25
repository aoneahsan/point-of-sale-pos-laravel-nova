<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Authorization policy for Product model.
 *
 * Controls access to product management operations.
 */
class ProductPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any products.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view-products') ||
               $user->hasAnyRole(['super-admin', 'store-manager', 'inventory-manager', 'cashier']);
    }

    /**
     * Determine if the user can view the product.
     */
    public function view(User $user, Product $product): bool
    {
        // Super admin can view all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Store users can view products in their store
        return $user->store_id === $product->store_id;
    }

    /**
     * Determine if the user can create products.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create-products') ||
               $user->hasAnyRole(['super-admin', 'store-manager', 'inventory-manager']);
    }

    /**
     * Determine if the user can update the product.
     */
    public function update(User $user, Product $product): bool
    {
        // Super admin can update all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Store managers and inventory managers can update products in their store
        if ($user->hasAnyRole(['store-manager', 'inventory-manager'])) {
            return $user->store_id === $product->store_id;
        }

        return false;
    }

    /**
     * Determine if the user can delete the product.
     */
    public function delete(User $user, Product $product): bool
    {
        // Super admin can delete all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Only store managers can delete products in their store
        if ($user->hasRole('store-manager')) {
            return $user->store_id === $product->store_id;
        }

        return false;
    }

    /**
     * Determine if the user can restore the product.
     */
    public function restore(User $user, Product $product): bool
    {
        return $this->delete($user, $product);
    }

    /**
     * Determine if the user can permanently delete the product.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        // Only super admin can force delete
        return $user->hasRole('super-admin');
    }

    /**
     * Determine if the user can replicate the product.
     */
    public function replicate(User $user, Product $product): bool
    {
        return $this->create($user);
    }

    /**
     * Determine if the user can attach images to the product.
     */
    public function attachAnyProductImage(User $user, Product $product): bool
    {
        return $this->update($user, $product);
    }

    /**
     * Determine if the user can attach variants to the product.
     */
    public function attachAnyProductVariant(User $user, Product $product): bool
    {
        return $this->update($user, $product);
    }
}
