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

## Introduction

LdapRecord-Laravel allows you to authenticate users from as many LDAP directories as you'd like.

This useful when you have separate domains that are not joined in a trust.

## Configuration {#configuration}

To begin, you must create two separate LdapRecord models for each of your domains.

Having two separate models allows you to configure their connections independently.

For this guide, we will have two example domains named `Alpha` and `Bravo`. We
first need to setup these domains connections in our `ldap.php` configuration file:

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

Now that we have our connections configured, let's go ahead and create their models by running the below commands:

```bash
php artisan make:ldap-model Alpha\User

php artisan make:ldap-model Bravo\User
```

> The `Alpha` and `Bravo` sub-directories will be created for you automatically.

Then, we must edit their connections to reflect the connection name:

```php
// app/Ldap/Alpha/User.php

class User extends Model
{
    protected $connection = 'alpha';

    // ...
```

```php
// app/Ldap/Bravo/User.php

class User extends Model
{
    protected $connection = 'bravo';

    // ...
```

Now, we need to setup a new [authentication provider](/docs/laravel/auth/configuration) in our
`config/auth.php` file for each of our connections we've created, as well as their own guard:

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

## Authenticating {#authenticating}

To start authenticating users from both of your LDAP domains, we need to modify our `LoginController`.

> If you do not have a `LoginController`, follow Laravel's [Authentication Quick-Start](https://laravel.com/docs/authentication#authentication-quickstart)
> guide to scaffold the controllers and views you need to continue below.

LdapRecord-Laravel comes with a built-in trait that makes authenticating users from multiple directories easier.
Go ahead and add it to the `LoginController`:

```php
// app/Http/Controllers/Auth/LoginController.php

use LdapRecord\Laravel\Auth\MultiDomainAuthentication;

class LoginController extends Controller
{
    use AuthenticatesUsers, MultiDomainAuthentication;

    // ...
```

Due to each domain requiring it's own `guard` that we've configured in our `auth.php` file,
we need to be able to determine which domain the user who is attempting to login in is from
so we can tell Laravel which guard to use for authenticating the user.

This could vary widely in each application, so in this guide we will be determining their domain from their
email addresses host name (`@alpha.com` and `@bravo.com`).

> If you're logging in users by username, you may want to provide a `<select>` input on your login 
> form that allows your user select their domain that they are authenticating to, instead of you
> having to determine it manually when they attempt signing in. Each `<option>`'s value would
> reflect the `guard` to use to sign the user in.

We must override the `guard()` method in our `LoginController` and let the included trait handle 
retrieving it for simplicity. 

The `guard()` method is responsible for returning which `guard` we want to use for
authenticating the users who are attempting to sign in, as well as signing the
currently authenticated user out.

We will also override the `getLdapGuardFromRequest()` method. This method is only used for *determining*
which guard to use for the user signing in. This is the method we will override for determining the
`guard` to use based on the users email address they are signing into our application with:

```php
// app/Http/Controllers/Auth/LoginController.php

// ...

public function guard()
{
    return $this->getLdapGuard();
}

public function getLdapGuardFromRequest(Request $request)
{
    $guards = [
        'alpha.com' => 'alpha',
        'bravo.com' => 'bravo',
    ];

    $domain = explode('@', $request->get('email'))[1];
    
    return $guards[$domain] ?? 'alpha'; 
}
```

If the user enters an email that is not available in our `$guards` array lookup, we will
return the `alpha` guard by default, and the authentication attempt will be made
to our `alpha` domain.

> You may wish to add a request validation rule instead to prevent users from signing
> in with invalid email domain. The way you implement this is totally up to you.

## Routes {#routes}

Having multiple authentication guards means that we need to update the `auth` middleware
that is covering our protected application routes.

Luckily, this middleware accepts a list of guards you would like to use. You will need to add
both of the guards you created above for both LDAP domains to be able to access the same
protected routes:

> By default, if no guards are given to the Laravel `auth` middleware, it will attempt
> to use the `default` guard configured - **we do not want this behaviour**.


```php
// routes/web.php

Route::group(function () {
    // Both alpha and bravo domains can access these routes...
})->middleware('auth:alpha,bravo');
```

If you would like to restrict routes to certain domains, only include one of them `auth` middleware:

```php
// routes/web.php

Route::group(function () {
    // Only alpha domain users can access these routes...
})->middleware('auth:alpha');
```

This is extremely handy for permission management - as authenticated users from certain domains
can only access the routes that have been defined.
