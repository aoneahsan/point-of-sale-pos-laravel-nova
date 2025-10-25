<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Authorization policy for Category model.
 *
 * Controls access to product category management operations.
 */
class CategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any categories.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view categories
        return true;
    }

    /**
     * Determine if the user can view the category.
     */
    public function view(User $user, Category $category): bool
    {
        // All authenticated users can view individual categories
        return true;
    }

    /**
     * Determine if the user can create categories.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'store-manager', 'inventory-manager']);
    }

    /**
     * Determine if the user can update the category.
     */
    public function update(User $user, Category $category): bool
    {
        return $user->hasAnyRole(['super-admin', 'store-manager', 'inventory-manager']);
    }

    /**
     * Determine if the user can delete the category.
     */
    public function delete(User $user, Category $category): bool
    {
        // Only super admin and store managers can delete categories
        return $user->hasAnyRole(['super-admin', 'store-manager']);
    }

    /**
     * Determine if the user can restore the category.
     */
    public function restore(User $user, Category $category): bool
    {
        return $this->delete($user, $category);
    }

    /**
     * Determine if the user can permanently delete the category.
     */
    public function forceDelete(User $user, Category $category): bool
    {
        return $user->hasRole('super-admin');
    }
}
