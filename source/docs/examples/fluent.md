```php
User::whereEnabled()
    ->whereMemberOf('cn=Managers,ou=Groups,dc=acme,dc=org')
    ->whereNotContains('company', 'Acme')
    ->get()
    ->each(function ($user) {
        $user->company = 'Acme Organization';
        
        $user->save();
    });
```