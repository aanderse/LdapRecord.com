```php
use LdapRecord\Connection;

$connection = new Connection([
	'username' => 'sbauman@acme.org',
  	'password' => 'SuperSecret',
	'hosts' => ['127.0.0.1'],
  	'base_dn' => 'dc=acme,dc=org',
]);

$connection->connect();

$users = $connection->query()->where('title', '=', 'Accountant')->get();
```
