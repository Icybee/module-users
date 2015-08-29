<?php

namespace Icybee\Modules\Users;

use ICanBoogie\HTTP\Request;
use ICanBoogie\Operation;

use Icybee\Modules\Users\Operation\ActivateOperation;
use Icybee\Modules\Users\Operation\DeactivateOperation;
use Icybee\Modules\Users\Operation\LoginOperation;
use Icybee\Modules\Users\Operation\LogoutOperation;
use Icybee\Routing\RouteMaker as Make;

return [

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

	]

] + Make::admin('users', Routing\UsersAdminController::class, [

	'id_name' => 'uid',
	'only' => [ 'index', 'create', 'edit', 'confirm-delete', 'profile', 'authenticate' ],
	'actions' => [

		'profile' => [

			'/profile', Request::METHOD_ANY, [

				'permission' => 'modify own profile',
				'navigation_excluded' => true

			]

		]

	]

]);
