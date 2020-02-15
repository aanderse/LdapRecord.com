---
title: Testing Authentication
description: LdapRecord-Laravel testing guide
extends: _layouts.laravel-documentation
section: content
---

# Testing

- [Introduction](#introduction)
- [Getting Started](#getting-started)
- [Creating the test](#creating-the-test)

## Introduction {#introduction}

LdapRecord-Laravel prides itself on giving you a great and easy testing experience using
the [Directory Emulator](/docs/laravel/testing#directory-emulator). Using it, we can
test authentication [rules](/docs/laravel/auth/configuration#rules),
[scopes](/docs/models#query-scopes) and group memberships. 

## Getting Started {#getting-started}

Before we begin, you must require the `doctrine/dbal` into your composers `require-dev` for testing.
This is due to the `$table->dropColumns(['guid', 'domain'])` call inside of the additional
LdapRecord auth migration and that we are using SQLite in our test environment.

This package is required for modifying columns - as described in the
[Laravel documentation](https://laravel.com/docs/migrations#modifying-columns).

To do so, run the following command:

```bash
composer require doctrine/dbal --dev
```

## Creating the test {#creating-the-test}

Let's whip up a test by running the following command:

```bash
php artisan make:test TestLdapAuthentication
```

Inside of our generated test, we'll make use of the following traits:

**DatabaseMigrations**

```text
Illuminate\Foundation\Testing\DatabaseMigrations
```

Using this trait will execute our migrations and ensure our database is to import our LDAP user.

**WithFaker**

```text
Illuminate\Foundation\Testing\WithFaker
```

Using this trait provides us with generating fake UUID's (great for creating mock "guids"), names and emails.

Let's add a `test_auth_works` method into the generated test:

```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use LdapRecord\Laravel\Testing\DirectoryEmulator;
use LdapRecord\Models\ActiveDirectory\User;
use Tests\TestCase;

class TestLdapAuthentication extends TestCase
{
    use DatabaseMigrations, WithFaker;

    public function test_auth_works()
    {
        $fake = DirectoryEmulator::setup('default');

        $ldapUser = User::create([
            'mail' => $this->faker->email,
            'cn' => $this->faker->name,
            'objectguid' => $this->faker->uuid,
        ]);

        $fake->actingAs($ldapUser);

        $this->post('/login', [
            'email' => $ldapUser->mail[0],
            'password' => 'secret',
        ])->assertRedirect('/home');

        $user = Auth::user();

        $this->assertInstanceOf(\App\User::class, $user);
        $this->assertEquals($ldapUser->mail[0], $user->email);
        $this->assertEquals($ldapUser->cn[0], $ldapUser->name);
    }
}
```

Let's deconstruct what's going on here.

The first line creates a new Directory Emulator for our LDAP connection named `default` inside
of our `config/ldap.php` file. It returns a fake LDAP connection that we can use to indicate
that the user we insert will successfully pass LDAP authentication:

```php
$fake = DirectoryEmulator::setup('default');
```

On the second line, we're creating a fake LDAP user who will be signing into our application.
You'll notice that we assign the attributes that are inside of our `sync_attributes`
specified inside of our `config/auth.php` file, as well as the users `objectguid`:

```php
$user = User::create([
    'mail' => $this->faker->email,
    'cn' => $this->faker->name,
    'objectguid' => $this->faker->uuid,
]);
```

Third line, we are asserting that the user we have created will automatically pass
LDAP authentication:

```php
$fake->actingAs($user);
```

Fourth, we are sending a post request to our `login` page, with our LDAP users email address.
The password can be anything, since we asserted above that the user **will** pass:

```php
 $this->post('/login', [
    'email' => $user->mail[0],
    'password' => 'secret',
])->assertRedirect('/home');
```

Finally, we check to make sure we can retrieve the successfully authenticated
user and that their attributes were successfully synchronized.
