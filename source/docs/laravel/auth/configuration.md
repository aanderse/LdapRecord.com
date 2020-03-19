---
title: Authentication Configuration
description: Configuring the LDAP authentication provider
extends: _layouts.laravel-documentation
section: content
---

# Configuration

- [Plain Authentication](#plain)
- [Synchronized Database Authentication](#database)
- [Authentication Rules](#rules)

All LDAP authentication configuration is done inside of your `config/auth.php` file.

Let's walk through configuring both LDAP authentication mechanisms.

## Plain Authentication {#plain}

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

> In the scenario of having multiple LDAP connections, it may be helpful to namespace the LDAP models
> you create with the desired connection. For example: <br/>
> ```text
> App\Ldap\DomainAlpha\User
> ```
> This will allow you to segregate scopes, rules and other classes to their relating connection.

### Driver

The `driver` option must be `ldap` as this is what indicates to Laravel the proper authentication driver to use.

### Model

The `model` option must be the class name of your [LdapRecord model](/docs/models). This model will be used
for fetching users from your directory.

### Rules

The `rules` option must be an array of class names of [authentication rules](/docs/laravel/auth/configuration#rules).

## Synchronized Database Authentication {#database}

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

The `database => model` key is the class name of the [Eloquent model](https://laravel.com/docs/eloquent) that will be
used for creating and retrieving LDAP users from your applications database.

> Be sure to add the required [trait and interface](/docs/laravel/auth/installation) to this model as shown in the installation guide.

### Database Password Sync

The `database.sync_passwords` option enables password synchronization. Password synchronization captures and hashes
the users password upon login if they pass LDAP authentication. This helps in situations where you may want to
provide a "back up" option in case your LDAP server is unreachable, as well as a way of determining if a
users password is valid without having to call to your LDAP server and validate it for you.

> If you do not define the `sync_passwords` key or have it set false, a user is always applied a
> random 16 character hashed password. This hashed password is only set once upon initial
> import so no needless updates are performed on user records upon login.

### Database Sync Attributes

The `sync_attributes` array defines a set of key-value pairs. The key of each array item is the column of your `users`
database table and the value is the name of the users LDAP attribute.

## Authentication Rules {#rules}

LDAP authentication rules give you the ability to allow or deny users from signing into your
application using a condition you would like to apply. These rules are executed **after**
a user successfully passes LDAP authentication against your configured server.

Think of them as a final authorization gate before they are allowed in.

> Authentication rules are never executed if a user fails LDAP authentication.

Let's create an LDAP rule that only allows members of our domain `Administrators` group.

To create an authentication rule, call the `make:ldap-rule` command:

```bash
php artisan make:ldap-rule OnlyAdministrators
```

A rule will then be created in your applications `app/Ldap/Rules` directory:

```php
<?php

namespace App\Ldap\Rules;

use LdapRecord\Laravel\Auth\Rule;

class OnlyAdministrators extends Rule
{
    /**
     * Check if the rule passes validation.
     *
     * @return bool
     */
    public function isValid()
    {
        //
    }
}
```

In the authentication rule, there are two properties made available to us.

- A `user` property that is the **LdapRecord** model of the authenticating user
- A `model` property that is the **Eloquent** model of the authenticating user

> The `model` property will be `null` if you are not using database synchronization.

Now, we will update the `isValid` method to check the LDAP users `groups` relationship to see if they are a member:

```php
<?php

namespace App\Ldap\Rules;

use LdapRecord\Laravel\Auth\Rule;
use LdapRecord\Models\ActiveDirectory\Group;

class OnlyAdministrators extends Rule
{
    public function isValid()
    {
        $administrators = Group::find('cn=Administrators,dc=local,dc=com');
    
        return $this->user->groups()->recursive()->exists($administrators);
    }
}
```

> We call the `recursive` method on the relationship to make sure that we load groups of
> groups in case the user is not an immediate member of the `Administrators` group.

Once we have our rule defined, we will add it into our authentication provider in the `config/auth.php` file:

```php
'providers' => [
    // ...
  
    'ldap' => [
        'driver' => 'ldap',
        'model' => LdapRecord\Models\ActiveDirectory\User::class,
        'rules' => [
            App\Ldap\Rules\OnlyAdministrators::class,
        ],
    ],
],
```

Now when you attempt to login to your application with a LDAP user that successfully passes
LDAP authentication, they will need to be a member of the `Administrators` group.

If you are caching your configuration, make sure you re-run `config:cache` to re-cache your modifications.
