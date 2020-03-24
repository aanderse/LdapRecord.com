---
title: Authentication Configuration
description: Configuring the LDAP authentication provider
extends: _layouts.laravel-documentation
section: content
---

# Configuration

- [Plain Authentication](#plain)
 - [Driver](#plain-driver)
 - [Model](#plain-model)
 - [Rules](#plain-rules)
- [Synchronized Database Authentication](#database)
 - [Database Model](#database-model)
 - [Database Password Column](#database-password-column)
 - [Database Password Sync](#database-password-sync)
 - [Database Sync Attributes](#database-sync-attributes)
- [Attribute Handlers](#attribute-handlers)
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

In the scenario of having multiple LDAP connections, it may be helpful to namespace the LDAP models
you create with the desired connection. For example:

```text
App\Ldap\DomainAlpha\User
```

This will allow you to segregate scopes, rules and other classes to their relating connection.

### Driver {#plain-driver}

The `driver` option must be `ldap` as this is what indicates to Laravel the proper authentication driver to use.

### Model {#plain-model}

The `model` option must be the class name of your [LdapRecord model](/docs/models). This model will be used
for fetching users from your directory.

### Rules {#plain-rules}

The `rules` option must be an array of class names of [authentication rules](#rules).

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

### Database Model {#database-model}

The `database => model` key is the class name of the [Eloquent model](https://laravel.com/docs/eloquent) that will be
used for creating and retrieving LDAP users from your applications database.

> Be sure to add the required [trait and interface](/docs/laravel/auth/installation) to this model as shown in the installation guide.

### Database Password Column {#database-password-column}

If your application uses a different password column than `password`, then you can configure
it using the `password_column` key inside of your providers configuration:

```php
'providers' => [
    // ...

    'ldap' => [
        // ...
        'database' => [
            // ...
            'password_column' => 'my_password_column',
        ],
    ],
],
```

You can also set the value to `null` if your database table does not have any password column at all:

```php
'providers' => [
    // ...

    'ldap' => [
        // ...
        'database' => [
            // ...
            'password_column' => null,
        ],
    ],
],
```

### Database Password Sync {#database-password-sync}

The `database => sync_passwords` option enables password synchronization. Password synchronization captures and hashes
the users password upon login if they pass LDAP authentication. This helps in situations where you may want to
provide a "back up" option in case your LDAP server is unreachable, as well as a way of determining if a
users password is valid without having to call to your LDAP server and validate it for you.

> If you do not define the `sync_passwords` key or have it set false, a user is always applied a
> random 16 character hashed password. This hashed password is only set once upon initial
> import or login so no needless updates are performed on user records.

### Database Sync Attributes {#database-sync-attributes}

The `database => sync_attributes` array defines a set of key-value pairs. The key of each array item is the column
of your `users` database table and the value is the name of the users LDAP attribute.

> You do not need to add your users `guid` or `domain` database columns. These are done automatically for you.

For further control on sync attributes, see the below [attribute handler](#attribute-handlers) feature.

## Attribute Handlers {#attribute-handlers}

If you require logic for synchronizing attributes when users sign into your application or are
being [imported](/docs/laravel/auth/importing), you can create an attribute handler class
responsible for setting / synchronizing your database models attributes from their
LDAP model.

This class you define must have a `handle` method. This method must accept the LDAP model you
have configured as the first parameter and your Eloquent database model as the second.

For the example below, we will create a handler named `AttributeHandler.php` inside of your `app/Ldap` folder:

> You do not need to call `save()` on your Eloquent database model.
> This is called for you after attribute synchronization.

```php
<?php

namespace App\Ldap;

use App\User as DatabaseUser;
use App\Ldap\User as LdapUser;

class AttributeHandler
{
    public function handle(LdapUser $ldap, DatabaseUser $database)
    {
        $database->name = $ldap->getFirstAttribute('cn');
        $database->email = $ldap->getFirstAttribute('mail');
    }
}
```

> Attribute handlers are created using Laravel's `app()` helper, so you may type-hint
> any dependencies you require in your handlers constructor to be made available
> during synchronization.

Then inside of your `config/auth.php` file for your provider, set the attribute handler class as the `sync_attributes` value:

```php
'providers' => [
    // ...

    'ldap' => [
        // ...
        'database' => [
            // ...
            'sync_attributes' => \App\Ldap\LdapAttributeHandler::class,
        ],
    ],
],
```

You may also add multiple if you'd prefer, or combine them with `key => value` pairs:

```php
// ...
'database' => [
    // ...
    'sync_attributes' => [
        'name' => 'cn',
        'email' => 'mail',
        \App\Ldap\MyFirstAttributeHandler::class,
        \App\Ldap\MySecondAttributeHandler::class,
    ],
],
```

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
