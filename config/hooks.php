<?php

namespace Icybee\Modules\Users;

use ICanBoogie;
use Icybee;

$hooks = Hooks::class . '::';

return [

	'events' => [

		ICanBoogie\HTTP\AuthenticationRequired::class .'::rescue' => $hooks . 'on_security_exception_rescue',
		ICanBoogie\HTTP\PermissionRequired::class . '::rescue' => $hooks . 'on_security_exception_rescue',
		ICanBoogie\Routing\RouteDispatcher::class . '::dispatch:before' => $hooks . 'before_routing_dispatcher_dispatch',
		Icybee\Modules\Users\Roles\DeleteOperation::class . '::process:before' => $hooks . 'before_roles_delete',
		Icybee\Modules\Users\WebsiteAdminNotAccessible::class . '::rescue' => $hooks . 'on_website_admin_not_accessible_rescue'

	],

	'prototypes' => [

		ICanBoogie\Core::class . '::lazy_get_user' => $hooks . 'get_user',
		ICanBoogie\Core::class . '::lazy_get_user_id' => $hooks . 'get_user_id',
		ICanBoogie\Core::class . '::lazy_get_user_permission_resolver' => $hooks . 'get_user_permission_resolver',
		ICanBoogie\Core::class . '::lazy_get_user_ownership_resolver' => $hooks . 'get_user_ownership_resolver',
		ICanBoogie\Core::class . '::check_user_permission' => $hooks . 'check_user_permission',
		ICanBoogie\Core::class . '::check_user_ownership' => $hooks . 'check_user_ownership'

	],

	'patron.markups' => [

		'user' => [ $hooks . 'markup_user' ],
		'users:form:login' => [ $hooks . 'markup_form_login' ]

	]

];
