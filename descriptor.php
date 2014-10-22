<?php

namespace Icybee\Modules\Users;

use ICanBoogie\ActiveRecord\Model;
use ICanBoogie\Module\Descriptor;

return array
(
	Descriptor::ID => 'users',
	Descriptor::DESCRIPTION => 'User management',
	Descriptor::CATEGORY => 'users',
	Descriptor::MODELS => array
	(
		'primary' => array
		(
			Model::T_SCHEMA => array
			(
				'fields' => array
				(
					'uid' => 'serial',
					'constructor' => array('varchar', 64, 'indexed' => true),
					'email' => array('varchar', 64, 'unique' => true),
					'password_hash' => array('varchar', 255, 'charset' => 'ascii/bin'),
					'username' => array('varchar', 32, 'unique' => true),
					'firstname' => array('varchar', 32),
					'lastname' => array('varchar', 32),
					'nickname' => array('varchar', 32),
					'name_as' => array('integer', 'tiny'),
					'language' => array('varchar', 8),
					'timezone' => array('varchar', 32),
					'logged_at' => 'datetime',
					'created_at' => array('timestamp', 'default' => 'CURRENT_TIMESTAMP'),
					'is_activated' => array('boolean', 'indexed' => true)
				)
			)
		),

		'has_many_roles' => array
		(
			Model::ALIAS => 'has_many_roles',
			Model::ACTIVERECORD_CLASS => 'ICanBoogie\ActiveRecord',
			Model::CLASSNAME => 'ICanBoogie\ActiveRecord\Model',
			Model::SCHEMA => array
			(
				'fields' => array
				(
					'uid' => array('foreign', 'primary' => true),
					'rid' => array('foreign', 'primary' => true)
				)
			)
		),

		'has_many_sites' => array
		(
			Model::ALIAS => 'has_many_sites',
			Model::ACTIVERECORD_CLASS => 'ICanBoogie\ActiveRecord',
			Model::CLASSNAME => 'ICanBoogie\ActiveRecord\Model',
			Model::SCHEMA => array
			(
				'fields' => array
				(
					'uid' => array('foreign', 'primary' => true),
					'siteid' => array('foreign', 'primary' => true)
				)
			)
		)
	),

	Descriptor::NS => __NAMESPACE__,
	Descriptor::PERMISSIONS => array
	(
		'modify own profile'
	),

	Descriptor::REQUIRED => true,
	Descriptor::REQUIRES => array
	(
		'users.roles' => '1.0'
	),

	Descriptor::TITLE => 'Users',
	Descriptor::VERSION => '2.0-dev'
);