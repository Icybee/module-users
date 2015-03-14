<?php

namespace Icybee\Modules\Users;

use ICanBoogie\ActiveRecord\Model;
use ICanBoogie\Module\Descriptor;

return [

	Descriptor::ID => 'users',
	Descriptor::DESCRIPTION => 'User management',
	Descriptor::CATEGORY => 'users',
	Descriptor::MODELS => [

		'primary' => [

			Model::SCHEMA => [

				'fields' => [

					'uid' => 'serial',
					'constructor' => [ 'varchar', 64, 'indexed' => true ],
					'email' => [ 'varchar', 64, 'unique' => true ],
					'password_hash' => [ 'varchar', 255, 'charset' => 'ascii/bin' ],
					'username' => [ 'varchar', 32, 'unique' => true ],
					'firstname' => [ 'varchar', 32 ],
					'lastname' => [ 'varchar', 32 ],
					'nickname' => [ 'varchar', 32 ],
					'name_as' => [ 'integer', 'tiny' ],
					'language' => [ 'varchar', 8 ],
					'timezone' => [ 'varchar', 32 ],
					'logged_at' => 'datetime',
					'created_at' => [ 'timestamp', 'default' => 'CURRENT_TIMESTAMP' ],
					'is_activated' => [ 'boolean', 'indexed' => true ]

				]
			]
		],

		'has_many_roles' => [

			Model::ALIAS => 'has_many_roles',
			Model::ACTIVERECORD_CLASS => 'ICanBoogie\ActiveRecord',
			Model::CLASSNAME => 'ICanBoogie\ActiveRecord\Model',
			Model::SCHEMA => [

				'fields' => [

					'uid' => [ 'foreign', 'primary' => true ],
					'rid' => [ 'foreign', 'primary' => true ]

				]
			]
		],

		'has_many_sites' => [

			Model::ALIAS => 'has_many_sites',
			Model::ACTIVERECORD_CLASS => 'ICanBoogie\ActiveRecord',
			Model::CLASSNAME => 'ICanBoogie\ActiveRecord\Model',
			Model::SCHEMA => [

				'fields' => [

					'uid' => [ 'foreign', 'primary' => true ],
					'siteid' => [ 'foreign', 'primary' => true ]

				]
			]
		]
	],

	Descriptor::NS => __NAMESPACE__,
	Descriptor::PERMISSIONS => [

		'modify own profile'

	],

	Descriptor::REQUIRED => true,
	Descriptor::REQUIRES => [

		'users.roles' => '1.0'

	],

	Descriptor::TITLE => 'Users',
	Descriptor::VERSION => '2.0-dev'

];
