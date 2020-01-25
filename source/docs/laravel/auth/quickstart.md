---
title: Authentication Quickstart
description: LdapRecord-Laravel Auth Quickstart Guide
extends: _layouts.laravel-documentation
section: content
---

# Authentication Quickstart

> Before you get started with the LDAP authentication driver please complete
> the [LdapRecord-Laravel quickstart guide](/docs/laravel/quickstart) to
> install LdapRecord and configure your LDAP connection.

- [Synchronized Database Authentication](#database-sync)
- [Plain Authentication](#plain)

## Synchronized Database Authentication {#database-sync}

### Step 1: Publish the Migration {#publish-migration}

LdapRecord requires you to have two additional user database columns.

1. `guid` - This is for storing your LDAP users `objectguid`. It is needed for
   locating and synchronizing your LDAP user to the database.
2. `domain` - This is for storing your LDAP users connection name. It is needed for
   storing your configured LDAP connection name of the user.

Go ahead and publish the migration using the below command:

```bash
php artisan vendor:publish --provider="LdapRecord\Laravel\LdapAuthServiceProvider"
```

Then, run the migrations with the `artisan migrate` command:

```bash
php artisan migrate
```

### Step 2: Configure the Authentication Driver {#configure-auth}

Inside of your `config/auth.php` file, we must add a new provider in the `providers` array.

In this example, we will create a provider named `ldap`:

```php
// config/auth.php

'providers' => [
    // ...

    'ldap' => [
        'driver' => 'ldap',
        'model' => LdapRecord\Models\ActiveDirectory\User::class,
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

If you are using OpenLDAP, you must switch the `ldap.model` entry to:

```php
LdapRecord\Models\OpenLDAP\User::class
```

If you are using a different LDAP type, you will need to [define your own LDAP model](/docs/models/#defining-models)
and insert it there. This model is used for locating the authenticating user in your LDAP directory.

Once you have setup your `ldap` provider, you must update the `provider` value in the `web` guard:

```php
// config/auth.php

'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'ldap', // Changed to 'ldap'
    ],
    
    // ...
```

### Step 3: Add the trait and interface to your user model {#add-trait-and-interface}

Now, we must add the following to our `User` Eloquent model:

- Interface: `LdapRecord\Laravel\Auth\LdapAuthenticatable`
- Trait: `LdapRecord\Laravel\Auth\AuthenticatesWithLdap`

```php
<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use LdapRecord\Laravel\Auth\AuthenticatesWithLdap;
use LdapRecord\Laravel\Auth\LdapAuthenticatable;

class User extends Authenticatable implements LdapAuthenticatable
{
    use Notifiable, AuthenticatesWithLdap;

    // ...
}
```

These are required so LdapRecord can set and retrieve your users `domain` and `guid` database columns.

This also allows you to configure the columns that LdapRecord uses for this by an override on the following methods:

- `getLdapDomainColumn`
- `getLdapGuidColumn`

### Step 4: Setting up your LoginController:

For LdapRecord to properly locate users that attempt to login to your application, you must
override the `credentials` method in your `Auth\LoginController.php` file.
 
Then, you must set an array key of the LDAP attribute that will be used for looking up the user
in your LDAP directory.

In the example below, we will lookup LDAP users by their `mail` attribute:

```php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    // Methods above removed for brevity...

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    protected function credentials(Request $request)
    {
        return [
            'mail' => $request->get('email'),
            'password' => $request->get('password'),
        ];
    }
}
```

## Step 5: Changing your 

## Plain LDAP Authentication {#plain}

