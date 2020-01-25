---
title: Authentication Usage
description: LdapRecord-Laravel authentication usage guide
extends: _layouts.laravel-documentation
section: content
---

# Usage

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

