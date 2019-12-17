```php
$user = User::create([
    'cn' => 'Steve Bauman',
    'company' => 'Acme',
    'password' => 'P@ssw0rd',
]);

$administrators = Group::find('cn=Administrators,dc=acme,dc=org');

$user->groups()->attach($administrators);
```