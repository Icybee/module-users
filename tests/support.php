<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Users\Test;

use ICanBoogie\ActiveRecord\Model;
use ICanBoogie\ActiveRecord\Connection;
use ICanBoogie\Module;

// require_once 'dependencies.php';

global $core;

$core = (object) array
(
	'configs' => array
	(
		'user' => array
		(
			'password_salt' => sha1('Sufjan Stevens'),
			'unlock_login_salt' => sha1('Cat Power')
		)
	)
);

function get_connection()
{
	return new Connection('sqlite::memory:');
}

function get_model()
{
	$connection = get_connection();

	static $model;

	if ($model)
	{
		return $model;
	}

	$descriptor = require __DIR__ . '/../descriptor.php';

	$model = new \Icybee\Modules\Users\Model
	(
		$descriptor[Module::T_MODELS]['primary'] + array
		(
			Model::NAME => 'users',
			Model::CONNECTION => get_connection(),
			\Icybee\ActiveRecord\Model\Constructor::T_CONSTRUCTOR => 'users'
		)
	);

	$model->install();

	return $model;
}