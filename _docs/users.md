# Users

## Removing users

Any user can request the deletion of their account. This can be done via user settings.

### How is this working

When a user deletes their account, it is soft-deleted. Along with it, all user's posts, 
and created discussions are deleted in the same way. 

If there are any replies to a user's posts (other than discussions of which he or she is the author) 
we leave them in the database, but with the date in the `marked_as_deleted` field, so we can preserve
the structure of the discussion and distinguish which post should be given special treatment 
when we display the thread.

We give user 7 days to change her/his mind. This is configurable via `Config\Forum::accountDeleteAfter` variable.

We send a reminder 3 and 1 day (days are declared via command call) before user's account is permanently deleted. 
In the message, we provide a link which can be used to restore the account.

After 7 days the account is removed permanently. Along with the user account we remove all the images, that were linked
to the removed threads and posts.

### How to remove users

If we're going to remove user, we should **always** use soft-delete option first. This is because, when the account
is soft-deleted, we are re-calculating all the stats for users, threads and categories.

Only after that we can remove the user from the database.

Removing related images automatically is not yet supported via model event. 
It's implemented only by `accounts:delete` command.

