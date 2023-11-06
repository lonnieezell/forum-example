# Alerts

The basic `Alerts` library allows us to display notifications about the results of actions performed by the user.

Since we use [htmx](https://htmx.org) in most places, we need to have a system that allows us to display inline 
notifications. But also be able to display the notification after redirect.

That's why this custom alerts library was created.

## How is this working

At the moment, alerts are built on top of the [Toast](https://daisyui.com/components/toast/) component.

Alert types are set based on the available classes in the Toast component, but we should
declare the type by omitting the "alert-" prefix. Thus, we have types: `success`, `error`, `info`.

We use `AlertsFilter` class to determine whether we need to display alerts inline 
(by using [hx-swap-oob](https://htmx.org/attributes/hx-swap-oob/)) or just by session's flash data 
(when using redirect).

### Setting alerts

Setting an alert is quite easy:

```php
alerts()->set('success', 'Message goes here');

alerts()->set('error', 'Message goes here');

alerts()->set('info', 'Message goes here');
```

We can also have many alerts with the same type:

```php
alerts()->set('success', 'Message 1');

alerts()->set('success', 'Message 2');
````

Also method chaining is available:

```php
alerts()->set('success', 'Message success')->set('error', 'Message error');
```

### Cleaning alerts

We can clean alerts by type:

```php
alerts()->clean('success');
```

Or clear all the alerts at once:

```php
alerts()->clean();
```

### Changing position of alerts

By default, alerts are displayed in the right bottom of the screen.
To change that we have to edit view file: `app/Views/_alerts/container`.

### Auto-hiding alerts

By default, each alert is hidden after 5 seconds. We can change that by
changing config variable: `Config\Forum::alertDisplayTime`.

Display time of individual alert can be changed on demand via 3rd (optional) parameter: 

```php
alerts()->set('success', 'This message will last 10 seconds', 10);
```
