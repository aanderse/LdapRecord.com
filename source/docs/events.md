---
title: Events
description: Listening to events in LdapRecord
extends: _layouts.documentation
section: content
---

# Events {#events}

LdapRecord events provide a method of listening for certain LDAP actions
that are called and execute tasks for that specific event.

The LdapRecord event dispatcher was actually derived from the
[Laravel Framework](https://github.com/laravel/framework) with
Broadcasting & Queuing omitted to remove extra dependencies
that would be required with implementing those features.

If you've utilized Laravel's events before, this will feel very familiar.

## Registering Listeners {#registering-listeners}

Before we get to registering listeners, it's crucial to know that events throughout
LdapRecord are fired irrespective of the current connection or provider in use.

This means that when using multiple LDAP connections, the same events will be fired.

This allows you to set listeners on events that occur for all LDAP connections you utilize.

If you are required to determine which events are fired from alternate connections, see [below](#determining-the-connection).

To register a listener on an event, retrieve the event dispatcher and call the `listen()` method:

```php
$dispatcher = \LdapRecord\Container::getEventDispatcher();

$dispatcher->listen(Binding::class, function (Binding $event) {
    // Do something with the Binding event information:
    
    $event->connection; // LdapRecord\Connections\Ldap instance
    $event->username; // 'jdoe@acme.org'
    $event->password; // 'super-secret'
});
```

The first argument is the event name you would like to listen for, and the
second is either a closure or class name that should handle the event:

Using a class:

> When using just a class name, the class must contain a public `handle()` method that will handle the event.

```php
$dispatcher = \LdapRecord\Container::getEventDispatcher();

$dispatcher->listen(Binding::class, MyApp\BindingEventHandler::class);
```

```php
namespace MyApp;

use LdapRecord\Auth\Events\Binding;

class BindingEventHandler
{
    public function handle(Binding $event)
    {
        // Handle the event...
    }
}
```

## Model Events {#model-events}

Model events are handled the same way as authentication events.

Simply call the event dispatcher `listen()` method with the model event you are wanting to listen for:

```php
$dispatcher = \LdapRecord\Container::getEventDispatcher();

$dispatcher->listen(Saving::class, function (Saving $event) {
    // Do something with the Saving event information:
    
    // Returns the model instance being saved eg. `LdapRecord\Models\Entry`
    $event->getModel();
});
```

## Wildcard Event Listeners {#wildcard-event-listeners}

You can register listeners using the `*` as a wildcard parameter to catch multiple events with the same listener.

Wildcard listeners will receive the event name as their first argument, and the entire event data array as their second argument:

```php
use LdapRecord\Container;

$dispatcher = Container::getEventDispatcher();

// Listen for all model events.
$dispatcher->listen('LdapRecord\Models\Events\*', function ($eventName, array $data) {
    echo $eventName; // Returns 'LdapRecord\Models\Events\Updating'
    var_dump($data); // Returns [0] => (object) LdapRecord\Models\Events\Updating;
});

$connection = Container::getDefaultConnection();

$user = $connection->query()->find('cn=User,dc=local,dc=com');

$user->company = 'New Company';

$user->save();
```

## Determining the Connection {#determining-the-connection}

If you're using multiple LDAP connections and you require the ability to determine which events belong
to a certain connection, you can do so by verifying the host of the LDAP connection.

Here's an example:

```php
use LdapRecord\Container;
use LdapRecord\Models\Events\Creating;

$dispatcher = Container::getEventDispatcher();

$dispatcher->listen(Creating::class, function ($event) {
    $connection = $event->model->getConnection();
    
    $host = $connection->getHost();
    
    echo $host; // Displays 'ldap://192.168.1.1:386'
});
```

Another example with auth events:

```php
use LdapRecord\Container;
use LdapRecord\Auth\Events\Binding;

$dispatcher = Container::getEventDispatcher();

$dispatcher->listen(Binding::class, function ($event) {
    $connection = $event->connection;
    
    $host = $connection->getHost();
    
    echo $host; // Displays 'ldap://192.168.1.1:386'
});
```

## List of Events {#list-of-events}

### Authentication Events

There are several events that are fired during initial and subsequent binds
to your configured LDAP server. Here is a list of all events that are fired:

### `LdapRecord\Auth\Events\Attempting`

When any authentication attempt is called via: `$connection->auth()->attempt()`.

### `LdapRecord\Auth\Events\Passed`

 When any authentication attempts pass via: `$connection->auth()->attempt()`.

### `LdapRecord\Auth\Events\Failed`

When any authentication attempts fail via:

- `$connection->auth()->attempt()`
- `$connection->auth()->bind()`

### `LdapRecord\Auth\Events\Binding`

When any LDAP bind attempts occur via:

- `$connection->auth()->attempt()`
- `$connection->auth()->bind()`

### `LdapRecord\Auth\Events\Bound`

When any LDAP bind attempts are successful via:

- `$connection->auth()->attempt()`
- `$connection->auth()->bind()`

### Model Events

There are several events that are fired during the creation, updating and deleting of all models.

Here is a list of all events that are fired:

### `LdapRecord\Models\Events\Saving`

When a model is in the process of being saved via: `$model->save()`.

### `LdapRecord\Models\Events\Saved`

When a model has been successfully saved via: `$model->save()`.

### `LdapRecord\Models\Events\Creating`

When a model is being created via: `$model->save()` *Or* `$model->create()`.

### `LdapRecord\Models\Events\Created`

When a model has been successfully created via: `$model->save()` *Or* `$model->create()`.

### `LdapRecord\Models\Events\Updating`

When a model is being updated via: `$model->save()` *Or* `$model->update()`.

### `LdapRecord\Models\Events\Updated`
 
When a model has been successfully updated via: `$model->save()` *Or* `$model->update()`.

### `LdapRecord\Models\Events\Deleting`

When a model is being deleted via: `$model->delete()`.

### `LdapRecord\Models\Events\Deleted`

When a model has been successfully deleted via: `$model->delete()`.