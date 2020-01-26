---
title: Authentication Rules
description: Creating LDAP authentication rules
extends: _layouts.laravel-documentation
section: content
---

# Authentication Rules

LDAP authentication rules give you the ability to allow or deny users from signing into your
application using a condition you would like to apply. These rules are executed **after**
a user successfully passes LDAP authentication against your configured server.

Think of them as a final authorization gate before they are allowed in.

> Authentication rules are never executed if a user fails LDAP authentication.

Let's create an LDAP rule that only allows members of our domain `Administrators` group.

To create an authentication rule, call the `make:ldap-rule` command:

```bash
php artisan make:ldap-rule OnlyAdministrators
```

A rule will then be created in your applications `app/Ldap/Rules` directory:

```php
<?php

namespace App\Ldap\Rules;

use LdapRecord\Laravel\Validation\Rules\Rule;

class OnlyAdministrators extends Rule
{
    /**
     * Check if the rule passes validation.
     *
     * @return bool
     */
    public function isValid()
    {
        //
    }
}
```

In the authentication rule, there are two properties made available to us.

- A `user` property that is the **LdapRecord** model of the authenticating user
- A `model` property that is the **Eloquent** model of the authenticating user

> The `model` property will be `null` if you are not using database synchronization.

Now, we will update the `isValid` method to check the LDAP users `groups` relationship to see if they are a member:

```php
<?php

namespace App\Ldap\Rules;

use LdapRecord\Models\ActiveDirectory\Group;
use LdapRecord\Laravel\Validation\Rules\Rule;

class OnlyAdministrators extends Rule
{
    /**
     * Check if the rule passes validation.
     *
     * @return bool
     */
    public function isValid()
    {
        $administrators = Group::find('cn=Administrators,dc=local,dc=com');
    
        return $this->user->groups()->recursive()->exists($administrators);
    }
}
```

> We call the `recurisve` method on the relationship to make sure that we load groups of
> groups in case the user is not an immediate member of the `Administrators` group.

Once we have our rule defined, we will add it into our authentication provider in the `config/auth.php` file:

```php
'providers' => [
    // ...
  
    'ldap' => [
        'driver' => 'ldap',
        'model' => LdapRecord\Models\ActiveDirectory\User::class,
        'rules' => [
            App\Ldap\Rules\OnlyAdministrators::class,
        ],
    ],
],
```

Now when you attempt to login to your application with a LDAP user that successfully passes
LDAP authentication, they will need to be a member of the `Administrators` group.

If you are caching your configuration, make sure you re-run `config:cache` to re-cache your modifications.
