```php
$user = User::create([
    'cn' => 'Steve Bauman',
    'company' => 'Acme',
    'password' => 'P@ssw0rd',
]);

$administrators = Group::find('cn=Admins,dc=local,dc=com');

$user->groups()->attach($administrators);
```