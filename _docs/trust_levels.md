# Trust Levels

Trust levels are a way of granting different capabilities to different users based on their activity and engagement with the community. These are inspired by the [trust level system](https://blog.discourse.org/2018/06/understanding-discourse-trust-levels/) used by Discourse.

## Assigning Trust Levels

Trust levels are automatically assigned during a weekly scheduled task (Not implemented yet). The task can be ran manually from the cli with the following command:

```bash
php spark trust:update
```

This examines all users and assigns them a trust level which is stored in the `trust_level` column on the `users` table.

## Checking Trust Level

You can check whether a user can be trusted to perform a certain action by using the `canTrustTo()` method on the `User` entity. This method accepts a string as an argument and returns a boolean. For example, to check if a user can flag posts, you would use the following code:

```php
if ($user->canTrustTo('flag')) {
    //
}
```

A list of all available trust scopes can be found in the `TrustLevels` config class, along with their default trust level requirements. This can be changed by admins and is saved in the database via the `settings` library.
