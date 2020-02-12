---
title: Importing Users
description: Running the import command
extends: _layouts.laravel-documentation
section: content
---

# Importing Users

- [Running the command](#running-the-command)
- [Scheduling the command](#scheduling-the-command)
- [Single Users](#single-users)
- [Command Options](#command-options)
- [Additional Tips](#tips)

LdapRecord-Laravel allows you to import users from your LDAP directories into your local database.
This is done from the `ldap:import` command and only available when you configure
[database synchronization](/docs/laravel/auth/configuration/#database).

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

### Filter

The --filter (or -f) option allows you to enter in a raw filter to further narrow down the users who are imported:

```text
php artisan ldap:import ldap --filter "(cn=John Doe)"
```

### No Logging

The --no-log option allows you to disable logging during the command.

```text
php artisan ldap:import ldap --no-log
```

By default this is enabled, regardless if `logging` is disabled in your `config/ldap.php` file.

### Delete

> This option is only available on ActiveDirectory models.

The --delete (or -d) option allows you to soft-delete deactivated LDAP users. No users
will be deleted if your `User` Eloquent model does not have soft-deletes enabled.

```text
php artisan ldap:import ldap --delete
```

### Restore

> This option is only available on ActiveDirectory models.

The --restore (or -r) option allows you to restore soft-deleted re-activated LDAP users.

```text
php artisan ldap:import ldap --restore
```

> Usually the `--restore` and `--delete` options are used in tandem to allow
> full synchronization of user disablements and restoration.

### No Interaction

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
- Successfully imported (new) users are reported in your log files with:
  ```text
  [2020-01-29 14:51:51] local.INFO: Imported user johndoe
  ```
- Unsuccessful imported users are also reported in your log files, with the message of the exception:
  ```text
  [2020-01-29 14:51:51] local.ERROR: Unable to import user janedoe. SQLSTATE[23000]: Integrity constraint violation: 1048
  ```
- If you have a password mutator (setter) on your `User` Eloquent model, it will not override it.
  This allows you to hash the random 16 character passwords any way you prefer.
