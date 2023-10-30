# Emails

Emails can be sent via `queues`. We have a special task set for this occasion. 

We can also run the command manually:

    php spark queue:work emails

There is a `EmailSimpleMessage` job, which is responsible for creating an email message.
All we have to do is supply 3 variables. The email address to which the message will be sent, the subject and the message.

```php
$user = model(UserModel::class)->find(1);

return service('queue')->push('emails', 'email-simple-message', [
    'to'      => $user->email,
    'subject' => 'Your account will be removed soon',
    'message' => view('_emails/account_delete_reminder', [
        'user' => $user,
    ]),
]);
```

In the above example, `emails` is the name of the queue, and `email-simple-message` is a friendly job name.
