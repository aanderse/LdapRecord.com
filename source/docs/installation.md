---
title: Installation
description: Installing LdapRecord
extends: _layouts.documentation
section: content
---

# Installation {#installation}

## Requirements {#installation-requirements}

LdapRecord requires the following:

- PHP >= 7.2
- PHP LDAP extension enabled
- An LDAP server (ActiveDirectory, OpenLDAP, FreeIPA etc.)

## Installing {#installation-installing}

LdapRecord uses [Composer](https://getcomposer.org) for installation.

Once you have composer installed, run the following command in the root directory of your project:

```bash
composer require directorytree/ldaprecord
```

Then, if your application doesn't already require Composer's autoload, you will need to do it manually.

Insert this line at the top of your projects PHP script (usually `index.php`):

```php
require __DIR__ . '/vendor/autoload.php';
```

You're all set! Either continue on with the [quick start](#quick-start) below, or head over to
the [configuration](/docs/configuration) guide.

## Quick Start {#installation-quick-start}

```php
// Create a new connection:
$connection = new \LdapRecord\Connection([
    'hosts' => ['192.168.1.1'],
    'port' => 389,
    'username' => 'user',
    'password' => 'secret',
    'base_dn' => 'dc=local,dc=com',
]);

// Connect to your server:
$connection->connect();

// Add the connection to the container:
\LdapRecord\Container::getInstance()->add($connection);

// Get all objects:
$objects = \LdapRecord\Models\Entry::get();

// Get a single object:
$object = \LdapRecord\Models\Entry::find('cn=John Doe,dc=local,dc=com');

// Getting attributes:
foreach ($object->memberof as $group) {
    echo $group;
}

// Modifying attributes:
$object->company = 'My Company';

$object->save();
```

## Versioning {#installation-versioning}

LdapRecord is versioned under the [Semantic Versioning](http://semver.org/) guidelines as much as possible.

Releases will be numbered with the following format:

`<major>.<minor>.<patch>`

And constructed with the following guidelines:

* Breaking backward compatibility bumps the major and resets the minor and patch.
* New additions without breaking backward compatibility bumps the minor and resets the patch.
* Bug fixes and misc changes bumps the patch.

Minor versions are not maintained individually, and you're encouraged to upgrade through to the next minor version.

Major versions are maintained individually through separate branches.
