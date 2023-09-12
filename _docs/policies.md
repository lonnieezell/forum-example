# Authorization Policies

## Overview

Authorization policies are used to determine whether a user is allowed to perform a certain action on a resource. For example, a policy may allow a user to create a new post, but not delete an existing one. While [Shield](https://codeigniter4.github.io/shield/) provides a powerful permissions system, policies extend this allowing you to define more complex rules.

For example, a user may be allowed to edit a post, but only if they are the author of the post. This is not possible with Shield alone, but can be achieved with a policy that checks the user's ID against the post's author ID.

## Creating Policies

Policies are simple classes that extend the `App\Libraries\Policies\PolicyInterface` class. These work hand-in-hand with the permissions defined in `App\AuthGroups` and the names of the classes and methods are based on these permissions. For example, if you have a permission called `posts.create`, you would create a policy called `PostPolicy` with a method called `create()`. The name must be the singular version of the first segment of the permission. In the previous example, the class name is `PostPolicy` even though the permission's first segment is `posts`. The file is expected to be located within the `app/Policies` directory and have the appropriate namespace, `App\Policies\PostPolicy`.

Here are some examples:

    - `posts.create` => `PostPolicy::create()`
    - `threads.edit` => `ThreadPolicy::edit()`
    - `users.delete` => `UserPolicy::delete()`

The policy class would look something like:

```php
namespace App\Policies;

use App\Libraries\Policies\PolicyInterface;

class PostPolicy extends PolicyInterface
{
    // policy methods here...
}
```

## Policy Methods

Policy methods are simple methods that return a boolean value. If the method returns `true`, the user is allowed to perform the action. If it returns `false`, the user is not allowed to perform the action. The method will receive the user object as the first parameter. Any additional parameters will be passed to the method when it is called. For example, if you have a permission called `posts.edit` and a policy method called `edit()`, the method will receive the user object as the first parameter and the post object as the second parameter.

```php
namespace App\Policies;

use App\Libraries\Policies\PolicyInterface;
use App\Entities\User;
use App\Entities\Post;

class PostPolicy extends PolicyInterface
{
    public function edit(User $user, Post $post): bool
    {
        return $user->can('posts.edit') || $user->id === $post->author_id;
    }
}
```

## Registering Policies

Policies are auto-matically discovered within the `app/Policies` directory. You do not need to register them anywhere.

## Using Policies

Policies are used in conjunction with the `can()` method on the controller. For example, if you have a permission called `posts.edit` and a policy method called `PostPolicy::edit()`, you can check if the user is allowed to edit a post from within your controller like so:

```php
if (! $this->policy->can('posts.edit', $post)) {
    // user is allowed NOT to edit the post
    return $this->policy->deny('You are not allowed to edit this post.');
}
```

By default, you can pass an HTTP status method to the `deny()` method as the second argument.

```php
if (! $this->policy->can('posts.edit', $post)) {
    // user is allowed NOT to edit the post
    return $this->policy->deny('You are not allowed to edit this post.', 403);
}
```

## Policy Filters

Policy filters are used to apply policies to multiple methods at once. For example, if you have a policy called `PostPolicy` and you want to apply it to all methods in the `Posts` controller, you can do so by adding a `before()` method to the policy. This can be used to apply blanket permission/denials to all methods in the controller. For example, if you want to allow access to all methods in the `PostPolicy` to superadmins, you can do so like this:

```php
namespace App\Policies;

use App\Entities\User;
use App\Libraries\Policies\PolicyInterface;

class PostPolicy extends PolicyInterface
{
    public function before(User $user, string $permission): bool|null
    {
        if ($user->inGroup('superadmin', 'admin')) {
            return true;
        }

        return null;
    }
}
```
