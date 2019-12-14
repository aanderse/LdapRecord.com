---
title: Logging
description: Logging events in LdapRecord
extends: _layouts.documentation
section: content
---

# Logging {#logging}

LdapRecord includes an implementation of PSR's widely supported [Logger](https://github.com/php-fig/log) interface.

By default, all of LdapRecord's [events](/docs/events) will call the logger you have set to utilize.

> LdapRecord does not include a file / text logger. You must implement your own.

## Registering & Enabling a Logger {#enabling-logger}

To register a logger call `LdapRecord\Container::setLogger()`. The logger must implement the `Psr\Log\LoggerInterface`.

```php
\LdapRecord\Container::setLogger($myLogger);
```

## Disabling Logging {#disabling-logger}

If you need to disable the event logger after a certain set of operations, simply pass in `null` and logging will be disabled:

```php
\LdapRecord\Container::setLogger($myLogger);

$connection = new \LdapRecord\Connection(['...']);

try {
    $connection->connect();
    
    // Disable logging anything else.
    \LdapRecord::setLogger(null);
} catch (\LdapRecord\Auth\BindException $e) {
    //
}
```

## Logged Information {#logged}

After enabling LdapRecord logging, the following events are logged:

### `LdapRecord\Auth\Events\Attempting`

```
LDAP (ldap://192.168.1.1:389) - Operation: LdapRecord\Auth\Events\Attempting - Username: CN=Steve Bauman,OU=Users,DC=corp,DC=acme,DC=org
```

### `LdapRecord\Auth\Events\Binding`

```
LDAP (ldap://192.168.1.1:389) - Operation: LdapRecord\Auth\Events\Binding - Username: CN=Steve Bauman,OU=Users,DC=corp,DC=acme,DC=org
```

### `LdapRecord\Auth\Events\Bound`

```
LDAP (ldap://192.168.1.1:389) - Operation: LdapRecord\Auth\Events\Bound - Username: CN=Steve Bauman,OU=Users,DC=corp,DC=acme,DC=org
```

### `LdapRecord\Auth\Events\Passed`

```
LDAP (ldap://192.168.1.1:389) - Operation: LdapRecord\Auth\Events\Passed - Username: CN=Steve Bauman,OU=Users,DC=corp,DC=acme,DC=org
```

### `LdapRecord\Auth\Events\Failed`

```
LDAP (ldap://192.168.1.1:389) - Operation: LdapRecord\Auth\Events\Failed - Username: CN=Steve Bauman,OU=Users,DC=corp,DC=acme,DC=org - Result: Invalid Credentials
```

### `LdapRecord\Models\Events\Saving`

```
LDAP (ldap://192.168.1.1:389) - Operation: Saving - On: LdapRecord\Models\Entry - Distinguished Name: cn=John Doe,dc=acme,dc=org
```

### `LdapRecord\Models\Events\Saved`

```
LDAP (ldap://192.168.1.1:389) - Operation: Saved - On: LdapRecord\Models\Entry - Distinguished Name: cn=John Doe,dc=acme,dc=org
```

### `LdapRecord\Models\Events\Creating`

```
LDAP (ldap://192.168.1.1:389) - Operation: Creating - On: LdapRecord\Models\Entry - Distinguished Name: cn=John Doe,dc=acme,dc=org
```

### `LdapRecord\Models\Events\Created`

```
LDAP (ldap://192.168.1.1:389) - Operation: Created - On: LdapRecord\Models\Entry - Distinguished Name: cn=John Doe,dc=acme,dc=org
```

### `LdapRecord\Models\Events\Updating`

```
LDAP (ldap://192.168.1.1:389) - Operation: Updating - On: LdapRecord\Models\Entry - Distinguished Name: cn=John Doe,dc=acme,dc=org
```

### `LdapRecord\Models\Events\Updated`

```
LDAP (ldap://192.168.1.1:389) - Operation: Updated - On: LdapRecord\Models\Entry - Distinguished Name: cn=John Doe,dc=acme,dc=org
```

### `LdapRecord\Models\Events\Deleting`

```
LDAP (ldap://192.168.1.1:389) - Operation: Deleting - On: LdapRecord\Models\Entry - Distinguished Name: cn=John Doe,dc=acme,dc=org
```

### `LdapRecord\Models\Events\Deleted`

```
LDAP (ldap://192.168.1.1:389) - Operation: Deleted - On: LdapRecord\Models\Entry - Distinguished Name: cn=John Doe,dc=acme,dc=org
```
