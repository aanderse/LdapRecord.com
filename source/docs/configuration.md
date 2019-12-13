---
title: Configuration
description: Configuring LdapRecord
extends: _layouts.documentation
section: content
---

# Configuration

- [Hosts](#hosts)
- [Base DN](#base-distinguished-name)
- [Username & Password](#username-amp-password)
- [Port](#port)
- [SSL & TLS](#ssl-amp-tls)
- [Timeout](#timeout)
- [Version](#version)
- [Follow Referrals](#follow-referrals)
- [Options](#options)

To configure your LDAP connections, you can either:

- Use an array
- Use a `DomainConfiguration` object

Either or will produce the same results. Use whichever you feel most comfortable with.

## Using an array

```php
$config = [
    'hosts' => [
        'DC-01.corp.acme.org',
    ],
    '...'
];
```

## Using a `DomainConfiguration` object

```php
// Setting options using constructor:
$config = new \LdapRecord\Configuration\DomainConfiguration([
    'hosts' => [
        'DC-01.corp.acme.org',
    ],
]);

// Setting options using the `set()` method:
$config->set('hosts', [
    'DC-01.corp.acme.org',
]);
```

## Options

```php
$config = [
    // Mandatory Configuration Options
    'hosts'            => ['corp-dc1.corp.acme.org', 'corp-dc2.corp.acme.org'],
    'base_dn'          => 'dc=corp,dc=acme,dc=org',
    'username'         => 'admin',
    'password'         => 'password',

    // Optional Configuration Options
    'port'             => 389,
    'follow_referrals' => false,
    'use_ssl'          => false,
    'use_tls'          => false,
    'version'          => 3,
    'timeout'          => 5,

    // Custom LDAP Options
    'options' => [
        // See: http://php.net/ldap_set_option
        LDAP_OPT_X_TLS_REQUIRE_CERT => LDAP_OPT_X_TLS_HARD
    ]
];
```

### Hosts {#hosts}

The hosts option is an array of IP addresses or host names located on your network that serve Active Directory.

You insert as many servers or as little as you'd like depending on your forest (with the minimum of one of course).

> Do not append your port to your IP addresses or host names. Use the `port` configuration option instead.

### Base Distinguished Name {#base-distinguished-name}

The base distinguished name is the base distinguished name you'd like to perform operations on.

An example base DN would be `DC=corp,DC=acme,DC=org`.

If one is not defined, you will not retrieve any search results.

> Your base DN is **case insensitive**. You do not need to worry about incorrect casing.

### Username & Password {#username-amp-password}

To connect to your LDAP server, a username and password is required to be able to query and run operations on your server(s).

You can use any account that has these permissions.

> To run administration level operations, such as resetting passwords,
> this account **must** have permissions to do so on your directory.

### Port {#port}

The port option is used for authenticating and binding to your LDAP server.

The default ports are already used for non SSL and SSL connections (389 and 636).

Only insert a port if your LDAP server uses a unique port.

### SSL & TLS {#ssl-amp-tls}

These Boolean options enable an SSL or TLS connection to your LDAP server.

Only **one** can be set to `true`. You must chose either or.

> You **must** enable SSL or TLS to reset passwords in ActiveDirectory.

These options are definitely recommended if you have the ability to connect to your server securely.

> TLS is recommended over SSL, as SSL is now labelled as a depreciated mechanism for securely running LDAP operations.

### Timeout {#timeout}

The timeout option allows you to configure the amount of seconds to wait until
your application receives a response from your LDAP server.

The default is 5 seconds.

### Version {#version}

The LDAP version to use for your connection.

Must be an integer and can either be `2` or `3`.

### Follow Referrals {#follow-referrals}

The follow referrals option is a boolean to tell active directory to follow a referral to another server on your network if the server queried knows the information your asking for exists, but does not yet contain a copy of it locally.

This option is defaulted to false.

Disable this option if you're experiencing search / connectivity issues.

For more information, visit: https://technet.microsoft.com/en-us/library/cc978014.aspx

### Options {#options}

Arbitrary options can be set for the connection to fine-tune TLS and connection behavior.

Please note that `LDAP_OPT_PROTOCOL_VERSION`, `LDAP_OPT_NETWORK_TIMEOUT` and `LDAP_OPT_REFERRALS` will be ignored if set.

These are set above with the `version`, `timeout` and `follow_referrals` keys respectively.

Valid options are listed in the [PHP documentation for ldap_set_option](http://php.net/ldap_set_option).