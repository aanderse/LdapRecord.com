---
title: Importing Users
description: Running the import command
extends: _layouts.laravel-documentation
section: content
---

# Importing LDAP Users

- [Introduction](#introduction)
- [Attribute Synchronization](#attribute-synchronization)
- [Synchronizing Existing Records](#syncing-existing-records)
- [Running the command](#running-the-command)
- [Scheduling the command](#scheduling-the-command)
- [Programmatically Executing](#programmatically-executing)
- [Single Users](#single-users)
- [Command Options](#command-options)
 - [Filter](#option-filter)
 - [Delete](#option-delete)
 - [Delete Missing](#option-delete-missing)
 - [Restore](#option-restore)
 - [No Logging](#option-no-logging)
 - [No Interaction](#option-no-interaction)
- [Additional Tips](#tips)

## Introduction {#introduction}

LdapRecord-Laravel allows you to import users from your LDAP directories into your local database.
This is done by executing the `php artisan ldap:import` command and is only available to LDAP
authentication providers you configure with [database synchronization](/docs/laravel/auth/configuration/#database).

As it is with signing users into your application, the Eloquent database model you specify in your
`config/auth.php` file is used for the creation and retrieval of users in your database.

## Attribute Synchronization {#attribute-synchronization}

The `sync_attributes` you define inside of your `config/auth.php` file for your provider will be used
for importing and synchronizing users.

Be sure to look at the [documentation](/docs/laravel/auth/configuration/#database-sync-attributes)
to get a further understanding on what is possible with this option.

## Syncing Existing Records {#syncing-existing-records}

The `sync_existing` array you define inside of your `config/auth.php` will be used to synchronize existing database records with your LDAP users.

Be sure to look at the [documentation](/docs/laravel/auth/configuration/#database-sync-existing)
to get a further understanding on what is possible with this option.

## Password Synchronization {#password-synchronization}

The `sync_passwords` option you define inside of your `config/auth.php` file is used when importing 
and synchronizing users. However, there are some main takeaways you must be aware of:

- **Passwords cannot be retrieved from users who are being imported from your LDAP server.**
  <br/>This would be a major security risk if this were possible. If a password is already
  set for the user being imported, it will be left untouched. This is to retain a
  possible synchronized password that was set upon login.
- **Passwords will always be set to a hashed 16 character string if not already present.**
  <br/>If the user being imported does not have a password, their password will be set to a
  hashed 16 character random string using `Str::random`.
- **Passwords will not be set** if you have defined `false` for `password_column`.

## Running the command {#running-the-command}

To run the command you must insert the `provider` name that you have setup for LDAP database synchronization
inside of your `config/auth.php` file. Let's walk through an example.

In our application we have a configured authentication provider named `ldap`:

```php
'providers' => [
    // ...

    'ldap' => [
        // ...
        'database' => [
            // ...
        ],
    ],
],
```

We will then insert the providers name into our import command and execute it:

```bash
php artisan ldap:import ldap
```

You will then be asked after a successful search in your directory:

```text
Found 2 user(s).

Would you like to display the user(s) to be imported / synchronized? (yes/no) [no]:
> y
```

A table will then be shown so you can confirm the import of the located users:

```text
+-------------+-------------------+---------------------+
| Name        | Account Name      | UPN                 |
+-------------+-------------------+---------------------+
| John Doe    | johndoe           | johndoe@local.com   |
| Jane Doe    | janedoe           | janedoe@local.com   |
+-------------+-------------------+---------------------+
```

Then, you will be asked to import the users shown and the import will begin:

```text
 Would you like these users to be imported / synchronized? (yes/no) [no]:
 > y

  2/2 [============================] 100%

Successfully imported / synchronized 2 user(s).
```

## Scheduling the command {#scheduling-the-command}

To run the import as a scheduled job, place the following in your `app/Console/Kernel.php` in the command scheduler:

```php
protected function schedule(Schedule $schedule)
{
    // Import LDAP users hourly.
    $schedule->command('ldap:import ldap', [
        '--no-interaction',
        '--restore',
        '--delete',
        '--filter' => '(objectclass=user)',
    ])->hourly();
}
```

The above scheduled import command will:

- Run without interaction and import new users as well as synchronize already imported users
- Restore user models who have been re-activated in your LDAP directory (if you're using [Eloquent Soft Deletes](https://laravel.com/docs/eloquent#soft-deleting))
- Soft-Delete user models who have been deactived in your LDAP directory (if you're using [Eloquent Soft Deletes](https://laravel.com/docs/eloquent#soft-deleting))
- Only import users that have an `objectclass` equal to user

> It's recommended to use [model query scopes](/docs/models#query-scopes) instead of the `--filter`
> option on your configured authentication LdapRecord model so LDAP users signing into your
> application are applied the same search filter.

## Programmatically Executing {#programmatically-executing}

You can call the `ldap:import` command using Laravel's [Artisan](https://laravel.com/docs/artisan#programmatically-executing-commands)
facade to programmatically execute the import inside of your application wherever you'd like:

```php
Artisan::call('ldap:import', ['provider' => 'ldap', '--no-interaction']);
```

To use more options, include them as array values:

```php
Artisan::call('ldap:import', [
    'provider' => 'ldap',
    '--no-interaction',
    '--restore' => true,
    '--delete' => true,
    '--delete-missing' => true,
    '--filter' => '(cn=John Doe)',
]);
```

## Single Users {#single-users}

To import or synchronize a single user, insert one of their attributes and LdapRecord will
try to locate the user for you using Ambiguous Name Resolution. If your LDAP server
does not support ANR, an equivalent query will be created automatically.

```text
php artisan ldap:import ldap jdoe@email.com

Found user 'John Doe'.

Would you like to display the user(s) to be imported / synchronized? (yes/no) [no]:
> y
```

## Command Options {#command-options}

### Filter {#option-filter}

The `--filter` (or `-f`) option allows you to enter in a raw filter to further narrow down the users who are imported:

> **Important**: If your filter contains commas, or other types of "escape" level LDAP search filter characters,
> you **must** escape the value with a backslash (`\`) before passing it into the search string. More on this below.

```text
php artisan ldap:import ldap --filter "(cn=John Doe)"
```

#### Escaping

In some cases, you may need to pass commas or other escape level characters into the search filter.

To do so, add a backslash (`\`) **before** the character to escape it properly:

```text
php artisan ldap:import ldap --filter "(cn=Doe\, John)"
```

If this is not done, you will receive a `Bad search filter` exception during import.

### Delete {#option-delete}

> This option is only available on Active Directory models.

The `--delete` (or `-d`) option allows you to soft-delete deactivated LDAP users. No users
will be deleted if your `User` Eloquent model does not have soft-deletes enabled.

```text
php artisan ldap:import ldap --delete
```

### Delete Missing {#option-delete-missing}

> This option is available for **all LDAP directories**.

The `--delete-missing` option allows you to soft-delete all LDAP users that
were missing from the import. This is useful when a user is deleted in your
LDAP server, and therefore should be soft-deleted inside of your application.

This option was designed to have the utmost safety of user data in mind.
Here are some paramount things to understand with this option:

**No users will be deleted if soft-deletes are not enabled on your `User` eloquent model.**

Deletion will not occur. You must setup [Soft Deletes](https://laravel.com/docs/eloquent#soft-deleting)
on your `User` eloquent model.

**If no users were successfully imported, no users will be soft-deleted.**

If an executed import imports zero (0) users, no users will be soft-deleted.

**Only users that belong to the domain you are importing will be soft-deleted.**

This means, all other users will be left untouched, such as local database
users  that were not imported from an LDAP server, as well as users
that were imported from another domain.

**Soft-deleted users are reported in the log.**

When users are soft-deleted, a log entry will be created for each one:

```text
User with [id = 2] has been soft-deleted due to being missing from LDAP import.
User with [id = 5] has been soft-deleted due to being missing from LDAP import.
```

#### The DeletedMissing Event

A `DeletedMissing` event is fired in the event of any users being soft-deleted.

You may listen for this event and access the IDs of the deleted users, as well as the Eloquent model
that was used to perform the deletion, and the LdapRecord model that was used to perform the import.

Here is an example listener that accesses this event and its properties:

```php
// app/Listeners/UsersDeletedFromImport.php

namespace App\Listeners;

use LdapRecord\Laravel\Events\DeletedMissing;

class UsersDeletedFromImport
{
    public function handle(DeletedMissing $event)
    {
        // \Illuminate\Support\Collection
        $event->ids;
        
        // \LdapRecord\Models\ActiveDirectory\User
        $event->ldap;
        
        // \App\User
        $event->eloquent;
    }
}
```

### Restore {#option-restore}

> This option is only available on Active Directory models.

The `--restore` (or `-r`) option allows you to restore soft-deleted re-activated LDAP users.

```text
php artisan ldap:import ldap --restore
```

> Usually the `--restore` and `--delete` options are used in tandem to allow
> full synchronization of user disablements and restoration.

### No Logging {#option-no-logging}

The `--no-log` option allows you to disable logging during the command.

```text
php artisan ldap:import ldap --no-log
```

By default this is enabled, regardless if `logging` is disabled in your `config/ldap.php` file.

### No Interaction {#option-no-interaction}

To run the import command via a schedule, use the `--no-interaction` flag:

```text
php artisan ldap:import ldap --no-interaction
```

Users will be imported automatically with no prompts.

You can also call the command from the Laravel Scheduler, or other commands:

```php
// Importing one user
$schedule->command('ldap:import ldap sbauman', ['--no-interaction'])
            ->everyMinute();

// Importing all users
$schedule->command('ldap:import ldap', ['--no-interaction'])
            ->everyMinute();

// Importing users with a filter
$dn = 'CN=Accounting,OU=SecurityGroups,DC=local,DC=com';

$filter = sprintf('(memberof:1.2.840.113556.1.4.1941:=%s)', $dn);

$schedule->command('ldap:import ldap', ['--no-interaction', '--filter' => $filter])
    ->everyMinute();
```

### Additional Tips {#tips}

- Users who already exist inside your database will be updated with your configured providers `sync_attributes`
- Users are never deleted from the import command, you will need to delete users regularly through your Eloquent model
- If you have a password mutator (setter) on your `User` Eloquent model, it will not override it.
  This allows you to hash the random 16 character passwords any way you prefer.
- Successfully imported (new) users are reported in your log files:
```text
[2020-01-29 14:51:51] local.INFO: Imported user johndoe
```
- Unsuccessful imported users are also reported in your log files, with the message of the exception:
```text
[2020-01-29 14:51:51] local.ERROR: Unable to import user janedoe. SQLSTATE[23000]: Integrity constraint violation: 1048
```
