<?php

declare(strict_types=1);

namespace App\Libraries\Policies;

use App\Entities\User;
use App\Managers\CategoryManager;

/**
 * Provides centralized autnorization based around policies
 * for the application.
 */
class Policy
{
    private array $policies = [];
    private User $user;

    /**
     * Used within the controllers to determine if the current
     * user has the ability to perform the given action.
     *
     * Example:
     * if (! $this->policy->can('posts.create')) {
     *    return $this->policy->deny('You are not allowed to create posts.', 403);
     * }
     */
    public function can(string $permission, ...$args): bool
    {
        // We must have a user....
        $user = $this->user ?? auth()->user();
        if ($user === null) {
            return false;
        }

        $policy = $this->getPolicy($permission);
        $method = $this->determineMethod($permission);

        // If the method doesn't exist, then we'll just check
        // against the user's permissions.
        if (! $policy || ! method_exists($policy, $method)) {
            return $user->can($permission);
        }

        // If the policy includes a `before` method, call it.
        // If it returns a boolean then return that value.
        if (method_exists($policy, 'before')) {
            $result = $policy->before($user, $permission);
            if (is_bool($result)) {
                return $result;
            }
        }

        // Call the policy method and return the result.
        return $policy->{$method}($user, ...$args);
    }

    /**
     * Used to deny access to a resource. Redirects to the
     * appropriate error page.
     */
    public function deny(string $message, int $status = 403)
    {
        if (service('request')->is('htmx')) {
            return redirect()
                ->with('message', $message)
                ->with('status', (string) $status)
                ->hxLocation(route_to('general-error'));
        }

        return redirect()
            ->with('message', $message)
            ->with('status', (string) $status)
            ->route('general-error');
    }

    /**
     * Check if current user should have access to the given category.
     */
    public function checkCategoryPermissions(int $categoryId)
    {
        if (($user = auth()->user()) !== null) {
            $this->withUser($user);
        }

        $permissions = manager(CategoryManager::class)->loadCategoryPermissions($categoryId);

        return $this->hasAny($permissions);
    }

    /**
     * Check if user has any of the permissions
     * or if the permissions are not set at all.
     */
    public function hasAny(array $permissions): bool
    {
        if ($permissions === []) {
            return true;
        }

        foreach ($permissions as $permission) {
            if ($this->can($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sets the policy to use for the current request.
     */
    public function withPolicy(PolicyInterface $policy): self
    {
        $this->policies[$policy::class] = $policy;

        return $this;
    }

    /**
     * Sets the user to use for the current request.
     */
    public function withUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Attempts to find the appropriate policy for the given permission string.
     * This is done by looking in `app/Policies` for a class that matches the
     * first section of the permission string, singularized and with the word
     * "Policy" appended to it. Within that method it would look for a method
     * named after the second section of the permission string.
     *
     * For example, a permission string of "posts.create" would look for a
     * class named "PostPolicy". Within that class it would look for a method
     * named "create".
     */
    protected function getPolicy(string $permission): ?PolicyInterface
    {
        helper('inflector');
        $className = substr($permission, 0, strpos($permission, '.'));
        $className = ucfirst((string) singular($className)) . 'Policy';

        $policy = '\\App\\Policies\\' . $className;

        if (empty($this->policies[$policy])) {
            if (! class_exists($policy)) {
                return null;
            }

            $this->policies[$policy] = new $policy();
        }

        return $this->policies[$policy];
    }

    /**
     * Determines the method name to call on the policy class. This is
     * based on the second section of the permission string.
     */
    private function determineMethod(string $permission): string
    {
        return substr($permission, strpos($permission, '.') + 1);
    }
}
