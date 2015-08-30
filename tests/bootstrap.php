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

require __DIR__ . '/../vendor/autoload.php';

/* @var $app Core|\Icybee\Binding\Core\CoreBindings */

$app = new Core(array_merge_recursive(get_autoconfig(), [

	'config-path' => [

		__DIR__ . '/config' => Autoconfig\Config::CONFIG_WEIGHT_MODULE

	],

	'module-path' => [

		realpath(__DIR__ . '/../')

	]

]));

$app->boot();
$app->modules->install();
