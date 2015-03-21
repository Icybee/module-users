<?php

namespace Icybee\Modules\Users;

use ICanBoogie\Operation;

return [

	'!admin:manage' => [

		'pattern' => '!auto',
		'controller' => true,

	],

	'!admin:new' => [

		'pattern' => '!auto',
		'controller' => true

	],

	'!admin:edit' => [

		'pattern' => '!auto',
		'controller' => true
	],

	'api:login' => [

		'pattern' => '/api/login',
		'controller' => LoginOperation::class,
		'via' => 'POST'

	],

	'api:logout' => [

		'pattern' => '/api/logout',
		'controller' => LogoutOperation::class,
		'via' => [ 'GET', 'POST' ]

	],

	'api:users/is_activated::set' => [

		'pattern' => '/api/users/<uid:\d+>/is_activated',
		'controller' => ActivateOperation::class,
		'via' => 'PUT',
		'param_translation_list' => [

			'uid' => Operation::KEY

		]

	],

	'api:users/is_activated::unset' => [

		'pattern' => '/api/users/<uid:\d+>/is_activated',
		'controller' => DeactivateOperation::class,
		'via' => 'DELETE',
		'param_translation_list' => [

			'uid' => Operation::KEY

		]

	],

	/**
	 * A route to the user's profile.
	 */
	'admin:profile' => [

		'pattern' => '/admin/profile',
		'controller' => ProfileController::class,
		'title' => 'Profile',
		'block' => 'profile',
		'visibility' => 'auto'

	],

	'admin:authenticate' => [

		'pattern' => '/admin/authenticate',
		'controller' => true,
		'title' => 'Connection',
		'block' => 'connect',
		'visibility' => 'auto'

	]
];
