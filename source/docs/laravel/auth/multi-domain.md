---
title: Multi-Domain Authentication Guide
description: Setting up multi-domain authentication using LdapRecord-Laravel
extends: _layouts.laravel-documentation
section: content
---

# Multi-Domain Authentication {#multi-domain-authentication}

- [Configuration](#configuration)
- [Authenticating](#authenticating)
- [Routes](#routes)

LdapRecord-Laravel allows you to authenticate users from two or more LDAP directories.

## Configuration {#configuration}

To begin, you must create two separate LdapRecord models for each of your domains.

For this guide, we will have two example domains named `Alpha` and `Bravo`. We first
need to setup these domains in our `ldap.php` configuration file:

```php
// config/ldap.php

// ...

'connections' => [

    'alpha' => [
        // ...
    ],
    
    'bravo' => [
        // ...
    ],
],
```

Now that we have our connections configured, let's go ahead and create their models:

```bash
php artisan make:ldap-model Alpha\User

php artisan make:ldap-model Bravo\User
```

And then edit their connections:

```php
// app/Ldap/Alpha/User.php

class User extends Model
{
    // ...
   
    protected $connection = 'alpha';
}
```

```php
// app/Ldap/Bravo/User.php

class User extends Model
{
    // ...
   
    protected $connection = 'bravo';
}
```

Once that's done, we need to setup a new [authentication provider](/docs/laravel/auth/configuration)
in our `auth.php` file for each of them, as well as their own guard:

```php
// config/auth.php

'guards' => [
    'alpha' => [
        // ...
        'provider' => 'alpha',
    ],
    
    'bravo' => [
        // ...
        'provider' => 'bravo',
    ],
],

'providers' => [
    // ...

    'alpha' => [
        // ...
        'model' => App\Ldap\Alpha\User::class,        
    ],

    'bravo' => [
        // ...
        'model' => App\Ldap\Bravo\User::class,        
    ],
],
```

## Authenticating

To start authenticating users from both of your LDAP domains, we need to modify our `LoginController`.

LdapRecord-Laravel comes with a built-in trait that makes this easier for you. Go ahead and add it:

```php
// app/Http/Controllers/Auth/LoginController.php

use LdapRecord\Laravel\Auth\MultiDomainAuthentication;

class LoginController extends Controller
{
    use AuthenticatesUsers, MultiDomainAuthentication;
}
```

Due to each provider requiring it's own `guard` that we've configured in our `auth.php` file,
we need to be able to determine which domain the user who is attempting to login in is from.

This could vary widely in each application, so in this guide we will be determining their domain from their
email addresses host name (`@alpha.com` and `@bravo.com`).

We must override the `guard` method in our `LoginController` and let the included trait handle 
retrieving it for simplicity. We will also override the `getLdapGuardFromRequest` method
to dynamically determine the `guard` to use based on the users email address they are
signing into our application with:

```php
// app/Http/Controllers/Auth/LoginController.php

// ...

public function guard()
{
    return $this->getLdapGuard();
}

public function getLdapGuardFromRequest()
{
    $guards = [
        'alpha.com' => 'alpha',
        'bravo.com' => 'bravo',
    ];

    $domain = explode('@', request('email'))[1];
    
    return $guards[$domain] ?? 'alpha'; 
}
````

If the user enters an email that is not available in our array lookup, we will return the `alpha` guard by default.

## Routes

