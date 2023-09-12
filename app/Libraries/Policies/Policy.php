<?php

namespace App\Libraries\Policies;

use App\Entities\User;
use InvalidArgumentException;
use RuntimeException;

/**
 * Provides centralized autnorization based around policies
 * for the application.
 */
class Policy
{
    /**
     * Used within the controllers to determine if the current
     * user has the ability to perform the given action.
     *
     * Example:
     * if (! $this->policy->can('posts.create')) {
     *    return $this->policy->deny(403, 'You are not allowed to create posts.');
     * }
     */
    public function can(string $permission, ?User $user = null, ...$args): bool
    {
        // We must have a user....
        $user = $user ?? auth()->user();
        if (! $user) {
            return false;
        }

        $policy = $this->getPolicy($permission);
        $method = $this->determineMethod($permission);

        // If the method doesn't exist, then we'll just check
        // against the user's permissions.
        if (! method_exists($policy, $method)) {
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
        return $policy->$method($user, ...$args);
    }

    /**
     * Used to deny access to a resource. Redirects to the
     * appropriate error page.
     */
    public function deny(string $message, int $status = 403)
    {
        return redirect()->hxLocation(route_to('general-error'))
            ->with('error', $message)
            ->with('status', $status);
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
    private function getPolicy(string $permission): PolicyInterface
    {
        helper('inflector');
        $className = substr($permission, 0, strpos($permission, '.'));
        $className = ucfirst(singular($className)) . 'Policy';

        $policy = 'App\\Policies\\' . $className;

        if (! class_exists($policy, false)) {
            throw new InvalidArgumentException('Policy not found: '. $policy);
        }

        return new $policy();
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
