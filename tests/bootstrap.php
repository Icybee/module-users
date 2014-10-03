<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Users;

use ICanBoogie\Core;

global $core;

$_SERVER['DOCUMENT_ROOT'] = __DIR__;

if (!file_exists(__DIR__ . '/../vendor/icanboogie-modules'))
{
	mkdir(__DIR__ . '/../vendor/icanboogie-modules');
}

require __DIR__ . '/../vendor/autoload.php';

#
# Create the _core_ instance used for the tests.
#

$core = new Core(\ICanBoogie\array_merge_recursive(\ICanBoogie\get_autoconfig(), [

	'config-path' => [

		__DIR__ . DIRECTORY_SEPARATOR . 'config' => 0

	],

	'module-path' => [

		realpath(__DIR__ . '/../')

	]

]));

$core->boot();

#
# Install modules
#

$errors = $core->modules->install(new \ICanBoogie\Errors);

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