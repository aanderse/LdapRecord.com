---
title: Quickstart
description: LdapRecord-Laravel Quickstart Guide
extends: _layouts.laravel-documentation
section: content
---

# Quickstart

### Step 1: Install LdapRecord-Laravel {#install-ldaprecord-laravel}

Require LdapRecord-Laravel via [composer](https://getcomposer.org/):

```bash
composer require directorytree/ldaprecord-laravel
```

### Step 2: Publish the LDAP configuration file {#publish-configuration}

```bash
php artisan vendor:publish --provider="LdapRecord\Laravel\LdapServiceProvider"
```

### Step 3: Configure your LDAP connection {#configure-connection}

Paste these environment variables into your `.env` file, and configure each option as necessary:

```dotenv
LDAP_LOGGING=true
LDAP_CONNECTION=default
LDAP_HOST=127.0.0.1
LDAP_USERNAME="cn=user,dc=local,dc=com"
LDAP_PASSWORD=secret
LDAP_PORT=389
LDAP_BASE_DN="dc=local,dc=com"
LDAP_TIMEOUT=5
LDAP_SSL=false
LDAP_TLS=false
```

View the core [configuration](/docs/configuration) documentation for more information.

### Step 4: Usage {#usage}

To begin, you may either use the built-in [models that LdapRecord comes with](/docs/models#predefined-models),
or you may create your own models that reference the connection you have created in your `ldap.php` file.

Call the below command to create a new LdapRecord model:

```bash
php artisan make:ldap-model User
```

Then use it in your application:

```php
<?php

namespace App\Http\Controllers;

use App\Ldap\User;

class LdapUserController extends Controller
{
    public function index()
    {
        $users = User::get();

        return view('ldap.users.index', ['users' => $users]);
    }
}
```

### Step 5: Authentication {#authentication}

View the [authentication quickstart guide](/docs/laravel/auth/quickstart) if you require LDAP authentication in your application.