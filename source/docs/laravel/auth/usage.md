---
title: Authentication Usage
description: LdapRecord-Laravel authentication usage guide
extends: _layouts.laravel-documentation
section: content
---

# Usage

- [Logging In](#logging-in)
- [Using Usernames](#using-usernames)
- [Eloquent Model Binding](#model-binding)
- [Pass-Through Authentication / SSO](#passthrough-authentication)
 - [Domain Verification](#sso-domain-verification)
 - [Changing the Server Key](#changing-the-sso-server-key)
- [Displaying LDAP Error Messages (password expiry, account lockouts)](#displaying-ldap-error-messages)

## Logging In {#logging-in}

Once you have finished configuring your authentication provider, you are ready to start authenticating users.

Before you get started, make sure you have either created a new authentication guard that uses your new provider,
or change the default guard to use your new provider. For now, let's change our default `web` guard to use
our new `ldap` provider:

```php
// config/auth.php

'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'ldap', // Changed from 'users'
    ],
    
    // ...
],
```

Now that we have updated our default authentication guard to use our new `ldap` provider, we will jump into
the default `LoginController` that is included with Laravel. For this example application, we will
authenticate our LDAP users with their email address using the LDAP attribute `mail`.

To have LdapRecord properly locate the user in your directory, we will override the `credentials` method in this controller:

```php
// app/Http/Controllers/Auth/LoginController.php

protected function credentials(Request $request)
{
    return [
        'mail' => $request->email,
        'password' => $request->password,
    ];
}
```

As you can see above, we set the `mail` key which is passed to the LdapRecord authentication provider.

A search query will be executed on your directory for a user that contains the `mail` attribute equal
to the entered `email` that the user has submitted on your login form. The `password` key is
automatically bypassed and will not be used in the search.

If a user is not found in your directory, or they fail authentication, they will be redirected to the
login page normally with the "Invalid credentials" error message.

## Using Usernames {#using-usernames}

In corporate environments, users are often used to signing into their computers with their username.
You can certainly keep this flow easy for them - we just need to change a couple things.

First, you will need to change the `email` column in the database migration that creates your `users`
table to `username`, as this represents what it will now contain:

```php
Schema::create('users', function (Blueprint $table) {
    // ...

    // Before...
    $table->string('email')->unique(); 
    
    // After...
    $table->string('username')->unique(); 
});
```

> Make sure you run your migrations using `php artisan migrate`.

Once we've changed the name of the column, we'll jump into the `config/auth.php` configuration and modify 
our LDAP user providers `sync_attributes` to synchronize this changed column. In this example, we will
use the users `sAMAccountName` as their username which is common in Active Directory environments:

```php
// config/auth.php

'providers' => [
    // ...

    'ldap' => [
        // ...
        
        'database' => [
            // ...

            'sync_attributes' => [
                'name' => 'cn',
                'username' => 'samaccountname',
            ],
        ],
    ],
],
```

Now, since we have changed the way our users sign into our application from the default `email` field,
we need to modify our HTML login form to reflect this. Let's jump into our `auth/login.blade.php`:

```html
<!-- resources/views/auth/login.blade.php -->

<!-- Before... -->
<input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

<!-- After... -->
<input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus>
```

After changing the HTML input, we now must modify our `LoginController` to use this new field.
We do this by overriding the `username` method, and updating our `credentials` method:

```php
// app/Http/Controllers/Auth/LoginController.php

public function username()
{
    return 'username';
}

protected function credentials(Request $request)
{
    return [
        'samaccountname' => $request->get('username'),
        'password' => $request->get('password'),
    ];
}
```

You can now sign into your application using usernames instead of email addresses.

## Eloquent Model Binding {#model-binding}

If you are using [database synchronization](/docs/laravel/auth#database), model binding allows
you to attach the users LdapRecord model to their Eloquent model so their LDAP data is
available on every request automatically.

> Enabling this option will perform a single query on your LDAP server for a logged in user per request.
> This could lead to slightly longer load times depending on your LDAP server and network speed.

To begin, insert the `LdapRecord\Laravel\Auth\HasLdapUser` trait onto your User model:

```php
namespace App;

use LdapRecord\Laravel\Auth\HasLdapUser;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasLdapUser;
```

Now, after an LDAP user logs into your application, their LdapRecord model will be available on
their model via the `ldap` property:

```php
// Instance of App\User
$user = Auth::user();

// Instance of App\Ldap\User
$ldap = $user->ldap;

echo $ldap->getFirstAttribute('cn');
```

## Pass-through Authentication / SSO {#passthrough-authentication}

Pass-through authentication allows your users to be automatically signed in when they access your
application on a Windows domain joined computer. This feature is ideal for in-house corporate
environments.

However, this feature assumes that you have enabled Windows Authentication in IIS, or have enabled
it in some other means with Apache. LdapRecord does not set this up for you. To enable Windows
Authentication, visit the [IIS configuration guide](https://www.iis.net/configreference/system.webserver/security/authentication/windowsauthentication/providers/add).

When you have it enabled on your server and a user visits your application from a domain joined computer,
the users `sAMAccountName` becomes available on a PHP server variable (`$_SERVER['AUTH_USER']`).

LdapRecord provides a middleware that you apply to your stack which retrieves this username
from the request, attempts to locate the user in your directory, then logs the user in.

To use the middleware, insert it on your middleware stack inside your `app/Http/Kernel.php` file:

```php
protected $middlewareGroups = [
    'web' => [
        // ...
        \LdapRecord\Laravel\Middleware\WindowsAuthenticate::class,
    ],
];
```

> The `WindowsAuthenticate` middleware uses the rules you have configured inside your `config/auth.php` file.
> A user may successfully authenticate against your LDAP server when visiting your site, but depending
> on your rules, may not be imported or logged in.

### SSO Domain Verification {#sso-domain-verification}

To prevent security issues using multiple-domain authentication using the `WindowsAuthenticate` middleware,
domain verification is performed on the authenticating user by checking if their domain name is contained
inside of the users distinguished name that is retrieved from each of your configured LDAP guards.

> Only 'Domain Components' are checked in the users distinguished name. More on this below.

To describe this issue further: The `WindowsAuthenticate` middleware retrieves all of your configured
authentication guards inside of your `config/auth.php` file, determines which one is using the `ldap`
driver, and then attempts to locate the authenticating users from **each connection**.

Since there is the possibility of users having the same `sAMAccountName` on two separate domains,
LdapRecord must verify that the user retrieved from your domain is in-fact the user who
is connecting to your Laravel application via Single-Sign-On.

For example, if a user visits your Laravel application with the username of:

```text
ACME\sbauman
```

And LdapRecord locates a user with the distinguished name of

```text
cn=sbauman,ou=users,dc=local,dc=com
```

They will be denied authentication, as the authenticating user has a domain of `ACME`,
but it is not contained inside of their distinguished name domain components (dc).

Using the same example, if the users distinguished name returns:

```text
cn=sbauman,ou=users,dc=acme,dc=com
```

Then they will be allowed to authenticate, as their `ACME` domain is contained inside of
their distinguished name domain components (`dc=acme`). Comparison against each domain
component is done in a **case-insensitive** manor.

If you would like to disable this check, you must call the static method `bypassDomainVerification`
on the `WindowsAuthenticate` middleware inside of your `AuthServiceProvider`:

> **Important**: This is a security issue if you use multi-domain authentication and
> disable this check. If you only connect to one domain inside your application,
> this is not a security issue. You have been warned.

```php
// app/Providers/AuthServiceProvider.php

/**
 * Register any authentication / authorization services.
 *
 * @return void
 */
public function boot()
{
    $this->registerPolicies();

    WindowsAuthenticate::bypassDomainVerification();
}
```

### Changing the Server Key {#changing-the-sso-server-key}

By default, the `WindowsAuthenticate` middleware uses the `AUTH_USER` key inside of PHP's `$_SERVER`
array (`$_SERVER['AUTH_USER']`). If you would like to change this, call the `serverKey` method on
the `WindowsAuthenticate` middleware inside of your `AuthServiceProvider`:

```php
// app/Providers/AuthServiceProvider.php

/**
 * Register any authentication / authorization services.
 *
 * @return void
 */
public function boot()
{
    $this->registerPolicies();

    WindowsAuthenticate::serverKey('PHP_AUTH_USER');
}
```

## Displaying LDAP Error Messages {#displaying-ldap-error-messages}

When a user fails LDAP authentication due to their password / account expiring, account
lockout or their password requiring to be changed, specific error codes are sent
back from your server. LdapRecord can interpret these for you and display
helpful error messages to users upon failing authentication.

To add this functionality, you must add the following trait to your `LoginController`:

```text
LdapRecord\Laravel\Auth\ListensForLdapBindFailure
```

Example:

```php
// app/Http/Controllers/Auth/LoginController.php

// ...

use LdapRecord\Laravel\Auth\ListensForLdapBindFailure;

class LoginController extends Controller
{
    use AuthenticatesUsers, ListensForLdapBindFailure;

    // ...
```

**However, this feature will only work automatically if your `LoginController` resides in the default
`App\Http\Controllers\Auth` namespace**. If you have changed the location of your `LoginController`,
you must modify the constructor and add the following method call to register the LDAP listener:

```php
// app/Http/Controllers/Auth/LoginController.php

// ...

use LdapRecord\Laravel\Auth\ListensForLdapBindFailure;

class LoginController extends Controller
{
    use AuthenticatesUsers, ListensForLdapBindFailure;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    
        $this->listenForLdapBindFailure();
    }
```

### Altering the response

By default, when an LDAP bind failure occurs, a `ValidationException` will be thrown which will
redirect users to your login page and display the error. If you would like to modify this
behaviour, you will need to override the method `handleLdapBindError`.

This method will include the error message as the first parameter and the error code as the second.

This is useful for checking for specific Active Directory response codes and returning a response:

```php
// app/Http/Controllers/Auth/LoginController.php

// ...

class LoginController extends Controller
{
    // ...

    use ListensForLdapBindFailure {
        handleLdapBindError as baseHandleLdapBindError;
    }
    
    protected function handleLdapBindError($message, $code = null)
    {
        if ($code == '773') {
            // The users password has expired. Redirect them.
            abort(redirect('/password-reset'));
        }
    
        $this->baseHandleLdapBindError($message, $code);
    }
}
```

> Refer to the [Password Policy Errors](/docs/tutorials/activedirectory/user-management/#password-policy-errors)
> documentation to see what each code means.
