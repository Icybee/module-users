<?php

namespace Icybee\Modules\Users;

return [

	'!admin:manage' => [],
	'!admin:new' => [],
	'!admin:edit' => [],

	'api:logout' => [

		'pattern' => '/api/logout',
		'controller' => __NAMESPACE__ . '\LogoutOperation',
		'via' => [ 'GET', 'POST' ]

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
		'title' => 'Connection',
		'block' => 'connect',
		'visibility' => 'auto'

	]

];