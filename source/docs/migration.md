#  Migration From Adldap2

### High Impact Changes

- [The Adldap Instance](#adldap)
- [The Provider](#provider)
- [Schemas](#schemas)

## Adldap

The `Adldap\Adldap` instance was a container for Adldap `Provider`'s you have added into it.

This has been replaced with the `LdapRecord\Container`:

```php
$ad = new \Adldap\Adldap();

$config = ['...'];

$provider = new \Adldap\Connections\Provider($config);

$ad->addProvider($provider);
```

```php
$config = ['...'];

$conn = new \LdapRecord\Connection($config);

$conn->connect();

\LdapRecord\Container::getInstance()->add($conn);
```

## Provider

The `Adldap\Connections\Provider` has been replaced with `LdapRecord\Connection`:

```php
$provider = new \Adldap\Connections\Provider($config, $name = 'domain-a');
```

```php
$conn = new \LdapRecord\Connection($config, $name = 'domain-a');
```

You may still call `auth()` on a `LdapRecord\Connection` to bind users:

```php
$provider = new \Adldap\Connections\Provider($config, $name = 'domain-a');

$provider->auth()->attempt('username', 'password', $bindAsUser = true);
```

```php
$conn = new \LdapRecord\Connection($config, $name = 'domain-a');

$conn->auth()->attempt('username', 'password', $bindAsUser = true);
```


## Schemas

Schemas have been completely removed in LdapRecord.