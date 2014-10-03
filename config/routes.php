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

	'api:logout' => [

		'pattern' => '/api/logout',
		'controller' => __NAMESPACE__ . '\LogoutOperation',
		'via' => [ 'GET', 'POST' ]

	],

	'api:users/is_activated::set' => [

		'pattern' => '/api/users/<uid:\d+>/is_activated',
		'controller' => __NAMESPACE__ . '\ActivateOperation',
		'via' => 'PUT',
		'param_translation_list' => [

			'uid' => Operation::KEY

		]

	],

	'api:users/is_activated::unset' => [

		'pattern' => '/api/users/<uid:\d+>/is_activated',
		'controller' => __NAMESPACE__ . '\DeactivateOperation',
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
		'controller' => __NAMESPACE__ . '\ProfileController',
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