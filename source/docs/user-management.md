---
title: User Management (ActiveDirectory)
description: Managing users with LdapRecord
extends: _layouts.documentation
section: content
---

# User Management (ActiveDirectory)

- [Creation](#creation)
- [Setting Passwords](#setting-passwords)
- [Changing Passwords](#changing-passwords)
- [Resetting Passwords](#resetting-passwords)
- [User Account Control](#user-account-control)
- [Password Policy Errors](#password-policy-errors)
- [Group Management](#group-management)

## Creation

Let's walk through the basics of user creation for ActiveDirectory. There
are some prerequisites you must know prior to creation:

- You must connect to your server via TLS or SSL if you set the `unicodepwd` attribute
- You must connect to your server with an account that has permission to create users
- You must set a common name (`cn`) for the user
- You must set the `unicodePwd` attribute as a non-encoded string (more on this below)
- To set the users `userAccountControl`, it must be set **after** the user has been saved

> {note} Attributes that are set below can be cased in *any* manor. They
> can be uppercase, lowercase, camel-cased, etc. Use whichever casing
> you prefer to be most readable in your application.

```php
<?php

use LdapRecord\Models\ActiveDirectory\User;

$user = new User();

$user->cn = 'John Doe';
$user->samccountname = 'jdoe';
$user->userPrincipalName = 'jdoe@acme.org';
$user->unicodePwd = 'SecretPassword#123';

// Save the user.
$user->save();

// Enable the user.
$user->userAccountControl = 512;

// Save the enablement.
$user->save();
```

To create a user inside of a container or an organizational unit, you can
use the `inside()` method to set the base DN the user must be located in.

It may be useful to first locate the organizational unit or container
prior to ensure it exists before attempting creation:

```php
<?php

use LdapRecord\Models\ActiveDirectory\User;
use LdapRecord\Models\ActiveDirectory\OrganizationalUnit;

$ou = OrganizationalUnit::findOrFail('ou=Users,dc=acme,dc=org');

$user = (new User())->inside($ou);

$user->cn = 'John Doe';

// User will be saved in the 'Users' OU.
$user->save();
```

> {note} It is wise to encapsulate saving your user in a try / catch block, so if it 
> fails you can determine if the cause of failure is due to your password policy.

### Setting Passwords

Utilizing the included `LdapRecord\Models\ActiveDirectory\User` model, an attribute
[mutator](/docs/{{version}}/model-mutators) has been added that assists in
the setting and changing of passwords on user objects. Feel free to take a
peek into the source code to see how it all works.

The password string you set on the users `unicodePwd` attribute is automatically encoded,
you do not need to encode it yourself. Doing so will cause an error or exception upon
saving the user.

Once you have set a password on a user object, this generates a modification
on the user model equal to a `LDAP_MODIFY_BATCH_REPLACE`:

```php
<?php

use LdapRecord\Models\ActiveDirectory\User;

$user = new User();

$user->unicodepwd = 'secret';

$modification = $user->getModifications()[0];

var_dump($modification);

// "attrib" => "unicodepwd"
// "modtype" => 3
// "values" => array:1 [▼
//    0 => ""\x00s\x00e\x00c\x00r\x00e\x00t\x00"\x00"
// ]
```

As you can see, a batch modification is generated for the user and upon
calling `save()`, it will be sent to your LDAP server.

### Changing Passwords

To change a users password, you must bind to your LDAP server with a user
that has permissions to reset passwords, or bind as the user whose
password you are trying to change.

There are some prerequisites you must know for changing passwords:

- You must provide the correct users old password
- You must provide a new password that abides by your password policy, such as history, complexity and length
- You must set the `unicodepwd` attribute with an array containing two (2) values (old & new password)

Let's walk through an example:

> {note} You must use a try / catch block upon saving. An LDAP exception will always be thrown
> when an incorrect old password is given, or the new password does not abide by your
> password policy.

```php
<?php

use LdapRecord\Models\ActiveDirectory\User;

$user = User::find('cn=John Doe,ou=Users,dc=acme,dc=org');

$user->unicodepwd = ['old-password', 'new-password'];

try {
    $user->save();

    // User password changed!
} catch (\Exception $ex) {
    // Failed changing password.
    $connection = $user->getConnection();

    // Get the last LDAP error to determine the cause of failure.
    $error = $connection->getLdapConnection()->getDetailedError();

    echo $error->getErrorCode();
    echo $error->getErrorMessage();
    echo $error->getDiagnosticMessage();
}
```

### Resetting Passwords

To reset a user password, you must be bound to your LDAP directory with a user whom has permission to do so.

This is done by setting the users `unicodepwd` attribute as a string, and then saving, similarly to how
it is done during user creation:

```php
<?php

use LdapRecord\Models\ActiveDirectory\User;

$user = User::find('cn=John Doe,ou=Users,dc=acme,dc=org');

$user->unicodepwd = 'new-password';

try {
    $user->save();

    // User password reset!
} catch (\Exception $ex) {
    // Failed resetting password.
    $connection = $user->getConnection();

    // Get the last LDAP error to determine the cause of failure.
    $error = $connection->getLdapConnection()->getDetailedError();

    echo $error->getErrorCode();
    echo $error->getErrorMessage();
    echo $error->getDiagnosticMessage();
}
```

### User Account Control

User account control is an integer that contains flags to control the behaviour of an ActiveDirectory user account.

You can manipulate this manually by simply setting the `userAccountControl` property on an existing user,
or you can use the account control builder `LdapRecord\Models\Attributes\AccountControl`:

```php
<?php

use LdapRecord\Models\ActiveDirectory\User;
use LdapRecord\Models\Attributes\AccountControl;

$user = User::find('cn=John Doe,ou=Users,dc=acme,dc=org');

$uac = new UserAccountControl();

$uac->accountIsNormal()->passwordDoesNotExpire();

$user->userAccountControl = $uac;

$user->save();
```

To determine what controls an existing user already has, create an `AccountControl` object
with the users `userAccountControl` value and use the `has()` method:

```php
<?php

use LdapRecord\Models\ActiveDirectory\User;
use LdapRecord\Models\Attributes\AccountControl;

$user = User::find('cn=John Doe,ou=Users,dc=acme,dc=org');

$uac = new UserAccountControl($user->userAccountControl);

if ($uac->has(AccountControl::LOCKOUT)) {
    // This account is locked out.
} elseif ($uac->has(AccountControl::ACCOUNTDISABLE)) {
    // This account is disabled.
} elseif ($uac->has(AccountControl::DONT_EXPIRE_PASSWORD)) {
    // This accounts password does not expire.
}
```

### Password Policy Errors

ActiveDirectory will return diagnostic error codes when a password modification fails.

To determine the cause, you can check this diagnostic message to see if it contains any of the following codes:

- 525 - user not found
- 52e - invalid credentials
- 530 - not permitted to logon at this time
- 531 - not permitted to logon at this workstation
- 532 - password expired
- 533 - account disabled
- 701 - account expired
- 773 - user must reset password
- 775 - user account locked

```php
<?php

use LdapRecord\Models\ActiveDirectory\User;

$user = User::find('cn=John Doe,ou=Users,dc=acme,dc=org');

$user->unicodepwd = ['old-password', 'new-password'];

try {
    $user->save();

    // User password changed!
} catch (\Exception $ex) {
    // Failed resetting password.
    $connection = $user->getConnection();

    // Get the last LDAP error to determine the cause of failure.
    $error = $connection->getLdapConnection()->getDetailedError();

    echo $error->getErrorCode(); // 49
    echo $error->getErrorMessage(); // 'Invalid credentials'
    echo $error->getDiagnosticMessage(); // '80090308: LdapErr: DSID-0C09042A, comment: AcceptSecurityContext error, data 52e, v3839'

    if (strpos($error->getDiagnosticMessage(), '52e')) {
        // This is an invalid credentials error.
    }
}
```

### Group Management

If you are utilizing the included `LdapRecord\Models\ActiveDirectory\User` model, the `groups()`
relationship exists for easily removing / adding groups to users.

> {note} To attach or detach groups on users, you must locate the first locate the group
> you wish to add or detach, and ensure the `member` attribute is selected.

#### Adding Groups

To add groups to a user, call the `groups()` relationship method, then `attach()`:

```php
<?php

use LdapRecord\Models\ActiveDirectory\User;
use LdapRecord\Models\ActiveDirectory\Group;

$group = Group::findOrFail('cn=Accounting,ou=Groups,dc=acme,dc=org');

$user = User::find('cn=John Doe,ou=Users,dc=acme,dc=org');

if ($user->groups()->attach($group)) {
    // Successfully added the group to the user.
}
```

#### Removing Groups

To remove groups on user, call the `groups()` relationship method, then `detach()`:

```php
<?php

use LdapRecord\Models\ActiveDirectory\User;
use LdapRecord\Models\ActiveDirectory\Group;

$group = Group::findOrFail('cn=Accounting,ou=Groups,dc=acme,dc=org');

$user = User::find('cn=John Doe,ou=Users,dc=acme,dc=org');

if ($user->groups()->detach($group)) {
    // Successfully removed the group from the user.
}
```

> {note} The `detach()` method will return `true` if the user is already not apart
> of the given group. This does not indicate that the user was previously a member.

You may want to locate groups on the user prior removal to ensure they are a member:

```php
<?php

use LdapRecord\Models\ActiveDirectory\User;

$user = User::find('cn=John Doe,ou=Users,dc=acme,dc=org');

$group = $user->groups()->first();

if ($group && $user->groups()->detach($group)) {
    // Successfully removed the first group from the user.
}
```