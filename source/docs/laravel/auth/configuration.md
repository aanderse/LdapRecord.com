---
title: Authentication Configuration
description: Configuring the LDAP authentication provider
extends: _layouts.laravel-documentation
section: content
---

# Configuration

All LDAP authentication configuration is done inside of your `config/auth.php` file.

Let's walk through configuring both LDAP authentication mechanisms.

## Plain LDAP Authentication

To create a plain LDAP authentication provider, navigate to the `providers` array,
and paste the following `ldap` provider:

```php
'providers' => [
    // ...
    
    'ldap' => [
        'driver' => 'ldap',
        'model' => LdapRecord\Models\ActiveDirectory\User::class,
        'rules' => [],
    ],
],
```

If your application requires more than one LDAP connection, you must create a new provider for each connection.

This new provider must have its own unique `model` class set which must use your [alternate configured connection](/docs/models#connections)
using the `$connection` property.

### Driver

The `driver` option must be `ldap` as this is what indicates to Laravel the proper authentication driver to use.

### Model

The `model` option must be the class name of your [LdapRecord model](/docs/models). This model will be used
for fetching users from your directory.

### Rules

The `rules` option must be an array of class names of [authentication rules](/docs/laravel/auth/rules).

## Synchronized Database LDAP Authentication

To create a synchronized database LDAP authentication provider, navigate to the `providers` array,
and paste the following `ldap` provider:

> If your application is requiring two or more LDAP connections, you must create a new user provider for each connection.

```php
'providers' => [
    // ...
    
    'ldap' => [
        'driver' => 'ldap',
        'model' => LdapRecord\Models\ActiveDirectory\User::class,
        'rules' => [],
        'database' => [
            'model' => App\User::class,
            'sync_passwords' => false,
            'sync_attributes' => [
                'name' => 'cn',
                'email' => 'mail',
            ],
        ],
    ],
],
```

As you can see above, a `database` array is used to configure the association between your LDAP user and your Eloquent user.

### Database Model

The `database.model` key is the class name of the Eloquent model that will be used for creating and
retrieving LDAP users from your applications database.

### Database Password Sync

The `database.sync_passwords` option enables password synchronization. Password synchronization captures and hashes
the users password upon login if they pass LDAP authentication. This helps in situations where you may want to
provide a "back up" option in case your LDAP server is unreachable, as well as a way of determining if a
users password is valid without having to call to your LDAP server and validate it for you.

> If you do not define the `sync_passwords` key or have it set false, a user is always applied a
> random 16 character hashed password.

### Database Sync Attributes

The `sync_attributes` array defines a set of key-value pairs. The key of each array item is the column of your `users`
database table and the value is the name of the users LDAP attribute.
