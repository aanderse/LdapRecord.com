# LdapRecord - Laravel

- [Introduction](#introduction)
- [Installation](#installation)
- [Setup](#setup)
- [Usage](#usage)

## Introduction

### What is LdapRecord-Laravel?

LdapRecord-Laravel is an extension to the core [LdapRecord](https://github.com/DirectoryTree/LdapRecord) package.

This package allows you to:

1. Easily configure and manage multiple LDAP connections at once
2. Authenticate LDAP users into your Laravel application
3. Import / Synchronize LDAP users into your database and easily keep them up to date with changes in your directory
4. Search your LDAP directory with a fluent and easy to use query builder
5. Create / Update / Delete LDAP entities with ease
6. And more

## Installation

### Requirements

LdapRecord-Laravel requires the following:

- Laravel 5.5
- PHP 7.2 or greater
- PHP LDAP extension enabled
- An LDAP Server

### Composer

Run the following command in the root of your project:

```bash
composer require ldaprecord/ldaprecord-laravel
```

> **Note**: If you are using laravel 5.5 or higher you can skip the service provider
> and facade registration and continue with publishing the configuration file.

Once finished, insert the service provider in your `config/app.php` file:

```php
LdapRecord\Laravel\LdapRecordServiceProvider::class,
```

Then insert the facade alias (if you're going to use it):

```php
'LdapRecord' => LdapRecord\Laravel\Facades\LdapRecord::class
```

Finally, publish the `ldap.php` configuration file by running:

```bash
php artisan vendor:publish --provider "LdapRecord\Laravel\LdapRecordServiceProvider"
```

## Setup

### Configuration

Upon publishing your `ldap.php` configuration, you'll see an array named `connections`. This
array contains a key value pair for each LDAP connection you're looking to configure.

Each connection you configure should be separate domains. Only one connection is necessary
when using multiple LDAP servers on the same domain.

#### Connection Name

The `default` key is your LDAP connections name. This is used as an identifier when connecting.

Usually this is set to your domain name. For example:

```php
'connections' => [
    'corp.acme.org' => [
        '...',
    ],
],
```

You may change this to whatever name you prefer.

#### Auto Connect

The `auto_connect` configuration option determines whether LdapRecord-Laravel will try to bind to your
LDAP server automatically using your configured credentials when calling the `LdapRecord`
facade or injecting the `ManagerInterface`.

For the example below, notice how we don't have to connect manually and we can assume connectivity:

```php
use LdapRecord\ManagerInterface;

public class UserController extends Controller
{
    public function index(ManagerInterface $ldap)
    {
        return view('users.index', [
            'users' => $ldap->search()->users()->get();
        ]);
    }
}
```

If this is set to `false`, you **must** connect manually before running operations on your server.
Otherwise, you will receive an exception upon performing operations.

#### Settings

The `settings` option contains a configuration array of your LDAP server connection.

Please view the core [LdapRecord Configuration Guide](/docs/{{version}}/setup)
for definitions on each option and its meaning.

## Usage

LpapRecord-Laravel leverages the core [LdapRecord](https://github.com/DirectoryTree/LdapRecord) package.

This means, upon calling the included facade (`LdapRecord\Laravel\Facades\LdapRecord`) or interface (`LdapRecord\ManagerInterface`), the same instance will be returned.

This is extremely useful to know, because the `LdapRecord\Manager` class acts as a container that stores each of your LDAP connections.

For example:

```php
use LdapRecord\Laravel\Facades\LdapRecord;

// Returns instance of `LdapRecord\Manager`
$manager = LdapRecord::getFacadeRoot();
```

For brevity, please take a look at the core [LdapRecord documentation](/docs/{{version}}/) for usage.

## Versioning

LdapRecord-Laravel is versioned under the [Semantic Versioning](http://semver.org/) guidelines as much as possible.

Releases will be numbered with the following format:

`<major>.<minor>.<patch>`

And constructed with the following guidelines:

* Breaking backward compatibility bumps the major and resets the minor and patch.
* New additions without breaking backward compatibility bumps the minor and resets the patch.
* Bug fixes and misc changes bumps the patch.

Minor versions are not maintained individually, and you're encouraged to upgrade through to the next minor version.

Major versions are maintained individually through separate branches.
