<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

define('ICanBoogie\REPOSITORY', __DIR__ . DIRECTORY_SEPARATOR . 'repository' . DIRECTORY_SEPARATOR);

require __DIR__ . '/../vendor/autoload.php';

#
# Create the _core_ instance used for the tests.
#

global $core;

$core = new \ICanBoogie\Core(\ICanBoogie\array_merge_recursive(\ICanBoogie\get_autoconfig(), [

	'config-path' => [

		__DIR__ . DIRECTORY_SEPARATOR . 'config'

	],

	'module-path' => [

		__DIR__ . '/../'

	]

]));

$core();

#
# Install modules
#

$errors = new \ICanBoogie\Errors();

foreach (array_keys($core->modules->enabled_modules_descriptors) as $module_id)
{
	#
	# The index on the `constructor` column of the `nodes` module clashes with SQLite, we don't
	# care right now, so the exception is discarted.
	#

	try
	{
		$core->modules[$module_id]->install($errors);
	}
	catch (\Exception $e) {}
}