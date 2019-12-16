---
title: Authentication
description: Binding users to an LDAP directory
extends: _layouts.documentation
section: content
---

# Authentication

- [Basic Authentication](#basic)
- [Using Other Attributes](#other-attributes)
- [Restricting Authentication](#restricting)
 - [Group Memberships](#group-memberships)
 - [Organizational Units](#organizational-units)

> Before we get started, it's paramount to know that LdapRecord does not set
> up any sort of PHP session that persists through every request. This is up
> to you to implement, as every project may vary with session usage.

## Basic Authentication {#basic}

The most widely used feature of any LDAP library is authentication. Let's walk through this step by step using LdapRecord.

Firstly, we need to define a `Connection` for your LDAP server that you would like users to authenticate against. Then,
we will call the `auth()->attempt()` method:

> If you only need to authenticate users against your LDAP server, you do not need to provide a `base_dn`. 
> This is only used for performing searches on your directory. Similarly with the `username` and
> `password` configuration options, these are only used for performing operations on your LDAP
> server that require permission - such as resetting passwords, modifying LDAP entries, and more.

```php
$connection = new \LdapRecord\Connection([
    'hosts' => ['127.0.0.1'],
]);

if ($connection->auth()->attempt('cn=john doe,dc=acme,dc=org', 'p@ssw0rd', $stayAuthenticated = true)) {
    // Successfully authenticated user.
} else {
    // Username or password is incorrect.
}
```

As you can see from the above, the first parameter of the `attempt()` method is the users Distinguished Name.
If you're running ActiveDirectory, you can use the users `userPrincipalName` instead, which (in the case 
above) would be in the format of `jdoe@acme.org`.

You may have also noticed we added a third parameter named `$stayAuthenticated = true`. This means, that throughout the 
entire lifecycle of the current request, you can perform further operations on your LDAP server *as* the
successfully authenticated user.

## Authenticating with other username attributes {#other-attributes}

No user wants to type in their full Distinguished Name to login to an application. It's
cumbersome, and will likely change over the years due to IT administrators moving
objects in the LDAP directory for organization purposes.

However, LDAP only supports binding (authenticating) users using their Distinguished Name (unless you're using ActiveDirectory).
How do we get around this limitation? Well, we can first connect to our LDAP directory and then retrieve their account
information - including their Distinguished Name. Let's walk through this.

Since we will first be searching our LDAP directory for the user that is attempting to authenticate, we have two options:

- Providing a `username` and `password` to our connection
- Anonymously bind to our connection, by not providing a `username` and `password` (if enabled in your directory)

> Since we will be searching our directory, we must provide a `base_dn`, so LdapRecord knows where to begin searching for records.

```php
// Connecting with an an account...
$connection = new \LdapRecord\Connection([
    'hosts' => ['127.0.0.1'],
    'base_dn' => 'dc=acme,dc=org',
    'username' => 'cn=WebApi,dc=acme,dc=org',
    'password' => 'super-secret',
]);

$connection->connect();

// Anonymously binding...
$connection = new \LdapRecord\Connection([
    'hosts' => ['127.0.0.1'],
    'base_dn' => 'dc=acme,dc=org',
]);

$connection->connect();
```

> It's recommended to create and use an account in your LDAP directory that is specifically for your
> web application(s), rather than using your own account or a domain administrator.

Once we're connected, we can then search for the user who is trying to authenticate.

For this example, we're wanting users to login using their `sAMAccountName`:

```php
$connection = new \LdapRecord\Connection(['...']);

$connection->connect();

$user = $connection->query()->where('samaccountname', '=', $_POST['username'])->firstOrFail();

if ($connection->auth()->attempt($user['distinguishedname'], $_POST['password'])) {
    // User has been successfully authenticated.
} else {
    // Username or password is incorrect.
}
```

## Restricting Authentication {#restricting}

Sometimes you only want certain users allowed to login to your application. You can do this in a couple ways.

### Group Memberships {#group-memberships}

To restrict who can authenticate in your application using groups that users will be members
of, we will perform the same as above, except we will check if the returned `memberof`
array of the user contains the allowed groups.

In this example, we will limit users who are members of `Accounting` and `IT`.

```php
$connection = new \LdapRecord\Connection(['...']);

$connection->connect();

$user = $connection->query()->where('samaccountname', '=', $_POST['username'])->firstOrFail();

// Get the groups from the user.
$userGroups = $user['memberof'];

// Set up our allowed groups.
$allowed = [
    'cn=Accounting,ou=Groups,dc=acme,dc=org',
    'cn=IT,ou=Groups,dc=acme,dc=org',    
];

// Normalize the group distinguished names and determine if
// the user is a member of any of the allowed groups:
$difference = array_intersect(
    array_map('strtolower', $userGroups),
    array_map('strtolower', $allowed)
);

if (count($difference) > 0) {
    // Our user is a member of one of the allowed groups.
    // Continue with authentication.
    if ($connection->auth()->attempt($user['distinguishedname'], $_POST['password'])) {
        // User has been successfully authenticated.
    } else {
        // Username or password is incorrect.
    }
}

// User is not a member of any of the allowed groups.
```

### Organizational Units {#organizational-units}

Using Organizational Units to determine which users are allowed to authenticate is easier than using groups.

In this scenario, we will limit our search to a single Organization Unit that contain users who are allowed to authenticate.

We can simply determine if a result is returned, we know the user exists inside:

```php
$connection = new \LdapRecord\Connection(['...']);

$connection->connect();

$organizationalUnit = 'ou=AllowedUsers,dc=acme,dc=org';

$user = $connection->query()
    ->in($organizationalUnit)
    ->where('samaccountname', '=', $_POST['username'])
    ->first();

if ($user) {
    // Our user is a member of one of the allowed groups.
    // Continue with authentication.
    if ($connection->auth()->attempt($user['distinguishedname'], $_POST['password'])) {
        // User has been successfully authenticated.
    } else {
        // Username or password is incorrect.
    }
}

// No user found. They are not inside the OU.
```
