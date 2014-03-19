<?php

namespace Icybee\Modules\Users;

return array
(
	'!admin:manage' => array
	(

	),

	'!admin:new' => array
	(

	),

	'!admin:edit' => array
	(

	),

	'api:logout' => [

		'pattern' => '/api/logout',
		'controller' => __NAMESPACE__ . '\LogoutOperation',
		'via' => [ 'GET', 'POST' ]

	],

	/**
	 * A route to the user's profile.
	 */
	'admin:profile' => array
	(
		'pattern' => '/admin/profile',
		'controller' => __NAMESPACE__ . '\ProfileController',
		'title' => 'Profile',
		'block' => 'profile',
		'visibility' => 'auto'
	),

	'admin:authenticate' => array
	(
		'pattern' => '/admin/authenticate',
		'title' => 'Connection',
		'block' => 'connect',
		'visibility' => 'auto'
	)
);