```php
use LdapRecord\Connection;

$connection = new Connection([
	'username' => 'sbauman@acme.org',
  	'password' => 'SuperSecret',
  	'base_dn' => 'dc=acme,dc=org',
]);

$connection->connect();

$users = $connection->query()->where('title', '=', 'Accountant')->get();
```