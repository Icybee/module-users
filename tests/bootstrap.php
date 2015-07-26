<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie;

$_SERVER['DOCUMENT_ROOT'] = __DIR__;

if (!file_exists(__DIR__ . '/../vendor/icanboogie-modules'))
{
	mkdir(__DIR__ . '/../vendor/icanboogie-modules');
}

require __DIR__ . '/../vendor/autoload.php';

/* @var $app Core|Module\CoreBindings */

$app = new Core(array_merge_recursive(get_autoconfig(), [

	'config-path' => [

		__DIR__ . DIRECTORY_SEPARATOR . 'config' => Autoconfig\Config::CONFIG_WEIGHT_MODULE

	],

	'module-path' => [

		realpath(__DIR__ . '/../')

	]

]));

$app->boot();

#
# Install modules
#

$errors = $app->modules->install(new \ICanBoogie\Errors);

if ($errors->count())
{
	foreach ($errors as $id => $error)
	{
		if ($error instanceof \Exception)
		{
			$error = $error->getMessage();
		}

		echo "$id: $error\n";
	}

	exit(1);
}
