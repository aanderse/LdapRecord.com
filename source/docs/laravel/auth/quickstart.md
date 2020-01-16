---
title: Authentication Quickstart
description: LdapRecord-Laravel Auth Quickstart Guide
extends: _layouts.laravel-documentation
section: content
---

# Authentication Quickstart

### Step 1: Follow and Complete Quickstart {#complete-quickstart}

Complete the [LdapRecord-Laravel quickstart guide](/docs/laravel/quickstart) to install
LdapRecord and configure your LDAP connection.

### Step 2: Publish the Migration {#publish-migration}

> **Note**: You may skip this step if you are not utilizing database synchronization.

```bash
php artisan vendor:publish --provider LdapRecord\Laravel\LdapAuthServiceProvider
```

Then, run the migrations via:

```bash
php artisan migrate
```

### Step 3: Configure the Authentication Driver {#configure-auth}

Inside of your `config/auth.php` file, we must add a new `provider`:

```php
'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\User::class,
    ],

    'ldap' => [
        'driver' => 'ldap',
        'model' => LdapRecord\Models\ActiveDirectory\User::class,
        'database' => [
            'model' => App\User::class,
            'sync_attributes' => [
                'name' => 'cn',
                'email' => 'mail',
            ],
        ],
    ],
],
```
