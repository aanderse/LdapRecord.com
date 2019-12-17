---
title: Common Queries
description: Common LDAP queries using LdapRecord
extends: _layouts.documentation
section: content
---

# Common Queries

Most applications will require retrieving certain set / type of objects from a directory.

- [Using Models](#using-models)
- [Without Models](#without-models)

## Using Models {#using-models}

Utilizing LdapRecord's built in models allow you to easily query specific types of objects in your directory.

> The examples below assume you have created a `Connection` and have added them into the [Container](/docs/connections#container).

### ActiveDirectory

##### Generic Queries

```php
// All ActiveDirectory objects:
// Note: We use 'paginate' here so over 1000 results can be returned.
$objects = \LdapRecord\Models\ActiveDirectory\Entry::paginate();

// All ActiveDirectory users:
$users = \LdapRecord\Models\ActiveDirectory\User::get();

// All ActiveDirectory contacts:
$contacts = \LdapRecord\Models\ActiveDirectory\Contact::get();

// All ActiveDirectory groups:
$groups = \LdapRecord\Models\ActiveDirectory\Group::get();

// All ActiveDirectory organizational units:
$ous = \LdapRecord\Models\ActiveDirectory\OrganizationalUnit::get();

// All ActiveDirectory printers:
$printers = \LdapRecord\Models\ActiveDirectory\Printer::get();

// All ActiveDirectory computers:
$computers = \LdapRecord\Models\ActiveDirectory\Computer::get();

// All foreign security principals:
$foreignPrincipals = \LdapRecord\Models\ActiveDirectory\ForeignSecurityPrincipal::get();
```

##### Users Created After a Date

```php
$users = User::where();
```

### OpenLDAP

##### Generic Queries

```php
// All OpenLDAP objects:
// Note: We use 'paginate' here so over 1000 results can be returned.
$objects = \LdapRecord\Models\OpenLDAP\Entry::paginate();

// All OpenLDAP users:
$users = \LdapRecord\Models\OpenLDAP\User::get();

// All OpenLDAP groups:
$groups = \LdapRecord\Models\OpenLDAP\Group::get();

// All OpenLDAP organizational units:
$ous = \LdapRecord\Models\OpenLDAP\OrganizationalUnit::get();
```

## Without Models {#without-models}

If you do not want to use LdapRecord models, you can still use the query builder and retrieve raw LDAP results.

```php
use LdapRecord\Connection;

$connection = new Connection(['...']);

// All LDAP objects:
// Note: We use 'paginate' here so over 1000 results can be returned.
$objects = $connection->query()->paginate();
```

### ActiveDirectory

```php
use LdapRecord\Connection;

$connection = new Connection(['...']);

// All ActiveDirectory Users:
$users = $connection->query()->where([
    ['objectclass', '=', 'top'],
    ['objectclass', '=', 'person'],
    ['objectclass', '=', 'organizationalperson'],
    ['objectclass', '=', 'user'],
])->get();

// All ActiveDirectory contacts:
$contacts = $connection->query()->where([
    ['objectclass', '=', 'top'],
    ['objectclass', '=', 'person'],
    ['objectclass', '=', 'organizationalperson'],
    ['objectclass', '=', 'contact'],
])->get();

// All ActiveDirectory groups:
$groups = $connection->query()->where([
    ['objectclass', '=', 'top'],
    ['objectclass', '=', 'group'],
])->get();

// All ActiveDirectory organizational units:
$ous = $connection->query()->where([
    ['objectclass', '=', 'top'],
    ['objectclass', '=', 'organizationalunit'],
])->get();

// All ActiveDirectory printers:
$printers = $connection->query()
    ->where('objectclass', '=', 'printqueue')
    ->get();

// All ActiveDirectory computers:
$computers = $connection->query()->where([
    ['objectclass', '=', 'top'],
    ['objectclass', '=', 'person'],
    ['objectclass', '=', 'organizationalperson'],
    ['objectclass', '=', 'user'],
    ['objectclass', '=', 'computer'],
])->get();

// All foreign security principals:
$foreignPrincipals = $connection->query()
    ->where('objectclass', '=', 'foreignsecurityprincipal')
    ->get();
```

### OpenLDAP

```php
// All OpenLDAP users:
$users = $connection->query()->where([
    ['objectclass', '=', 'top'],
    ['objectclass', '=', 'person'],
    ['objectclass', '=', 'organizationalperson'],
    ['objectclass', '=', 'inetorgperson'],
])->get();

// All OpenLDAP groups:
$groups = $connection->query()->where([
    ['objectclass', '=', 'top'],
    ['objectclass', '=', 'groupofuniquenames'],
])->get();

// All OpenLDAP organizational units:
$ous = $connection->query()->where([
    ['objectclass', '=', 'top'],
    ['objectclass', '=', 'organizationalunit'],
])->get();
```
