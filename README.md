# Users

[![Release](https://img.shields.io/packagist/v/icybee/module-users.svg)](https://github.com/Icybee/module-users/releases)
[![Build Status](https://img.shields.io/travis/Icybee/module-users.svg)](http://travis-ci.org/Icybee/module-users)
[![Code Quality](https://img.shields.io/scrutinizer/g/Icybee/module-users.svg)](https://scrutinizer-ci.com/g/Icybee/module-users)
[![Code Coverage](https://img.shields.io/coveralls/Icybee/module-users.svg)](https://coveralls.io/r/Icybee/module-users)
[![Packagist](https://img.shields.io/packagist/dt/icybee/module-users.svg)](https://packagist.org/packages/icybee/module-users)

Manages users of the CMS Icybee.





## Permission and Ownership

The actions a user can perform are determined by the permission which are granted to him, or the
fact that he is the owner or not of a record.

Two methods are used to determine if a user has a specific permission, or if he is the owner of a
record. In order to best suit the many different situations, plugins are used to help.





### Determine a user's permission

The method `has_permission()` determines if a user has a specific permission. This permission can
be relative to a target, a module for instance.

The following example demonstrates how to verify is a user has the permission to update his own
profile, or if he has the permission to administer the module Users.

```php
<?php

use ICanBoogie\Module;

if ($user->has_permission('update own profile')
|| $user->has_permission(Module::PERMISSION_ADMINISTER, $app->modules['users']))
{
	// …
}
```

The `assert_permission()` method throws a `UserLacksPermission` exception if the user lacks a required permission.

```php
<?php

use Icybee\Modules\Users\UserLacksPermission;

/* @var \Icybee\Modules\Users\User $user */
/* @var \ICanBoogie\Application $app */
/* @var string|int $permission */

$node = $app->models['nodes']->one;

try 
{
	$user->assert_permission($permission, $node);

	// …
}
catch (UserLacksPermission $e)
{
	var_dump($e->permission, $e->resource, $e->user);
}
```






### Determine a user ownership

The method `has_ownership()` determines if a user has ownership of a resource. Beware, _ownership_
does not mean _permission_! A user may own a resource and lack the permission to delete it.

The following example demonstrates how to verify if a user is the owner of a node.

```php
<?php

$node = $app->models['nodes']->one;

if ($user->has_ownership($node))
{
	// …
}
```

The `assert_ownership()` method throws a `UserLacksOwnership` exception if the user lacks ownership
of a resource.

```php
<?php

use Icybee\Modules\Users\UserLacksOwnership;

/* @var \Icybee\Modules\Users\User $user */
/* @var \ICanBoogie\Application $app */

$node = $app->models['nodes']->one;

try 
{
	$user->assert_ownership($node);

	// …
}
catch (UserLacksOwnership $e)
{
	var_dump($e->resource, $e->user);
}
```





### An open system

To better respond to the many different situation, an open system—based on the concept of
plugins—is used. With this system, a third party could provide support for shared ownership, which
is not supported by the module Users. Using this system, the module Roles provides support for
permissions associated with roles.

Resolvers are defined using the `users` configuration.





#### Permission resolvers

Permission resolvers determine if a user has the required permission to perform an action.
For instance, the resolver defined by the module Roles determine the permission of a user 
according to its roles and their associated permissions.

Permission resolvers are defined in the `permission_resolver_list` array of the `users`
configuration. It can be a function or the name of a class implementing the
[PermissionResolverInterface][] interface.

```php
<?php

// …/config/users.php

return [

	'permission_resolver_list' => [

		'a_resolver' => 'Hooks::my_permission_resolver',
		'another_resolver' => 'MyPermissionResolverClass'

	]

];
```




### Ownership resolvers

Ownership resolvers determine if a user is the owner of a record. The resolver defined by the
module regard a user as an owner of a record `uid` property is not empty and matches the user's
identifier, or if the user is the admin.

```php
<?php

namespace Icybee\Modules\Users;

use ICanBoogie\ActiveRecord;

function(User $user, ActiveRecord $record)
{
	$uid = $user->uid;

	if ($uid == 1 || (!empty($record->uid) && $record->uid == $uid))
	{
		return true;
	}
}
```




Ownership resolvers are defined in the `ownership_resolver_list` array of the `users`
configuration. It can be a function or the name of a class implementing the
[OwnershipResolverInterface][] interface.

```php
<?php

// …/config/users.php

return [

	'ownership_resolver_list' => [

		'a_resolver' => 'my_ownership_resolver',
		'another_resolver' => 'MyOwnershipResolverClass'

	]

];
```




### Resolver weight

A weight can be specified for each resolver using the key `weight`. This weight can be expressed
as a numeric value such as `-10` and `10`, the special values `top` and `bottom`, or a position
relative to another resolver such as `before:<resolver_id>` and `after:<resolver_id>`,
where `<resolver_id>` is the identifier of another resolver.

```
<?php

// users.roles/config/users.php

namespace Icybee\Modules\Users\Roles;

return [

	'permission_resolver_list' => [

		'roles' => [ PermissionResolver::class, 'weight' => 0 ]

	],

	'ownership_resolver_list' => [

		'roles' => [ OwnershipResolver::class, 'weight' => 10 ]

	]

];
```





In the following configuration, the weight of `-10` allows the permission resolver defined
by "mymodule" to be positioned before the one defined by "roles". With the relative weight 
`before:roles` the ownership resolver of "mymodule" is positioned just before the one defined by
"roles".

```php
<?php

// mymodule/config/users.php

namespace App\Modules\MyModule;

return [

	'permission_resolver_list' => [

		'mymodule' => [ PermissionResolver::class, 'weight' => -10 ]

	],

	'ownership_resolver_list' => [

		'mymodule' => [ OwnershipResolver::class, 'weight' => 'before:roles' ]

	]

];
```





### Calling resolvers

To determine if a user has a specific permission, the resolvers are invoked one by one, in order
to modify the permission value, which is initially set to `false`. The value is modified if the
resolver returns something else than `null`. Resolver generally only return `true` if they
grant the permission, and `null` otherwise. Indeed, a resolver returns `false` only if it wants
to overwrite the response of a previous resolver.

The same process is used to determine if a user is the owner of a record.





## Security





### Preventing brute force login

In order to prevent brute force login, each failed attempt is counted in the metas of the target
user account. When the number of failed attempts reaches a limit (e.g. 10) the user account is
locked and a message with a key to unlock it is sent to the user's email address.

Once the message has been sent all subsequent connection requests will fail during an hour. After
this delay, the counter is reseted.

The following metas properties are used for the process:

- (int) `failed_login_count`: The number of successive failed attempts. Reseted when the
user successfully login.
- (int) `failed_login_time`: Time of the last failed login.
- (string) `login_unlock_token`: Derivative salted token of the key sent by email for the user
to unlock its account.
- (int) `login_unlock_time`: Time at which login is unlocked.





----------





## Requirement

The package requires PHP 5.5 or later.





## Installation

The recommended way to install this package is through [Composer](http://getcomposer.org/):

```
$ composer require icybee/module-users
```

This module is part of the modules required by [Icybee](http://icybee.org).





### Cloning the repository

The package is [available on GitHub](https://github.com/Icybee/module-users), its repository can be
cloned with the following command line:

	$ git clone https://github.com/Icybee/module-users.git users





## Documentation

The package is documented as part of the [Icybee](http://icybee.org/) CMS
[documentation](http://icybee.org/docs/). The documentation for the package and its
dependencies can be generated with the `make doc` command. The documentation is generated in
the `docs` directory using [ApiGen](http://apigen.org/). The package directory can later by
cleaned with the `make clean` command.





## Testing

The test suite is ran with the `make test` command. [PHPUnit](https://phpunit.de/) and [Composer](http://getcomposer.org/) need to be globally available to run the suite. The command installs dependencies as required. The `make test-coverage` command runs test suite and also creates an HTML coverage report in "build/coverage". The directory can later be cleaned with the `make clean` command.

The package is continuously tested by [Travis CI](http://about.travis-ci.org/).

[![Build Status](https://img.shields.io/travis/Icybee/module-users.svg)](https://travis-ci.org/Icybee/module-users)
[![Code Coverage](https://img.shields.io/coveralls/Icybee/module-users.svg)](https://coveralls.io/r/Icybee/module-users)







## License

**icybee/module-users** is licensed under the New BSD License - See the [LICENSE](LICENSE) file for details.





[OwnershipResolverInterface]: http://icybee.org/autodoc/class-Icybee.Modules.Users.OwnershipResolverInterface.html
[PermissionResolverInterface]: http://icybee.org/autodoc/class-Icybee.Modules.Users.PermissionResolverInterface.html
