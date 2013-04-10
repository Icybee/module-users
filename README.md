# The "Users" module (users) [![Build Status](https://travis-ci.org/Icybee/module-users.png?branch=master)](https://travis-ci.org/Icybee/module-users)

The "Users" module manages the users of the CMS Icybee.





## Requirement

The package requires PHP 5.3 or later.





## Installation

The recommended way to install this package is through [Composer](http://getcomposer.org/).
Create a `composer.json` file and run `php composer.phar install` command to install it:

```json
{
	"minimum-stability": "dev",
	"require":
	{
		"icybee/module-users": "*"
	}
}
```





### Cloning the repository

The package is [available on GitHub](https://github.com/Icybee/module-users), its repository can be
cloned with the following command line:

	$ git clone git://github.com/Icybee/module-users.git users





## Documentation

The package is documented as part of the [Icybee](http://icybee.org/) CMS
[documentation](http://icybee.org/docs/). The documentation for the package and its
dependencies can be generated with the `make doc` command. The documentation is generated in
the `docs` directory using [ApiGen](http://apigen.org/). The package directory can later by
cleaned with the `make clean` command.





## Testing

The test suite is ran with the `make test` command. [Composer](http://getcomposer.org/) is
automatically installed as well as all the dependencies required to run the suite. The package
directory can later be cleaned with the `make clean` command.

The package is continuously tested by [Travis CI](http://about.travis-ci.org/).

[![Build Status](https://travis-ci.org/Icybee/module-users.png?branch=master)](https://travis-ci.org/Icybee/module-users)






## License

The module is licensed under the New BSD License - See the LICENSE file for details.





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