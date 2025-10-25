<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Authorization policy for User model.
 *
 * Controls access to user management operations based on roles and permissions.
 * Integrates with Laravel Nova for resource-level authorization.
 */
class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view-users') ||
               $user->hasRole('super-admin');
    }

    /**
     * Determine if the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Super admin can view all users
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Store managers can view users in their store
        if ($user->hasRole('store-manager')) {
            return $user->store_id === $model->store_id;
        }

        // Users can view their own profile
        return $user->id === $model->id;
    }

    /**
     * Determine if the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create-users') ||
               $user->hasRole('super-admin') ||
               $user->hasRole('store-manager');
    }

    /**
     * Determine if the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Super admin can update all users
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Store managers can update users in their store
        if ($user->hasRole('store-manager')) {
            return $user->store_id === $model->store_id;
        }

        // Users can update their own profile (limited fields)
        return $user->id === $model->id;
    }

    /**
     * Determine if the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Cannot delete self
        if ($user->id === $model->id) {
            return false;
        }

        // Super admin can delete users
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Store managers can delete users in their store (except super admins)
        if ($user->hasRole('store-manager')) {
            return $user->store_id === $model->store_id &&
                   !$model->hasRole('super-admin');
        }

        return false;
    }

    /**
     * Determine if the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $this->delete($user, $model);
    }

    /**
     * Determine if the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        // Only super admin can force delete
        return $user->hasRole('super-admin') &&
               $user->id !== $model->id;
    }

    /**
     * Determine if the user can attach any role to the model.
     */
    public function attachAnyRole(User $user, User $model): bool
    {
        // Only super admin can manage roles
        return $user->hasRole('super-admin');
    }

    /**
     * Determine if the user can attach any permission to the model.
     */
    public function attachAnyPermission(User $user, User $model): bool
    {
        // Only super admin can manage individual permissions
        return $user->hasRole('super-admin');
    }

    /**
     * Determine if the user can replicate the model.
     */
    public function replicate(User $user, User $model): bool
    {
        return $this->create($user);
    }

    /**
     * Determine if the user can add a note to the model.
     */
    public function addNote(User $user, User $model): bool
    {
        return $this->view($user, $model);
    }

    /**
     * Determine if the user can delete a note from the model.
     */
    public function deleteNote(User $user, User $model): bool
    {
        return $this->update($user, $model);
    }
}
