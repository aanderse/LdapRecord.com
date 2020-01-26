---
title: Authentication Usage
description: LdapRecord-Laravel authentication usage guide
extends: _layouts.laravel-documentation
section: content
---

# Usage

- [Logging In](#logging-in)
- [Using Usernames](#using-usernames)
- [Pass-Through Authentication](#passthrough-authentication)

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

/**
 * Get the needed authorization credentials from the request.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return array
 */
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
You can certainly keep this flow easy for your users - we just need to change a couple things.

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
use the users `sAMAccountName` as their username, which is used in ActiveDirectory environments:

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

```blade
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
        'samaccountname' => $request->username,
        'password' => $request->password,
    ];
}
```

You can now sign into your application using usernames instead of email addresses.

## Pass-through Authentication {#passthrough-authentication}

