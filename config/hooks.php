<?php

namespace Icybee\Modules\Users;

$hooks = __NAMESPACE__ . '\Hooks::';

return [

	'events' => [

		'ICanBoogie\AuthenticationRequired::rescue' => $hooks . 'on_security_exception_rescue',
		'ICanBoogie\PermissionRequired::rescue' => $hooks . 'on_security_exception_rescue',
		'ICanBoogie\Routing\Dispatcher::dispatch:before' => $hooks . 'before_routing_dispatcher_dispatch',
		'Icybee\Modules\Users\Roles\DeleteOperation::process:before' => $hooks . 'before_roles_delete',
		'Icybee\Modules\Users\WebsiteAdminNotAccessible::rescue' => $hooks . 'on_website_admin_not_accessible_rescue',
		'Icybee\Modules\Users\LoginOperation::process' => $hooks . 'on_login'

	],

	'prototypes' => [

		'ICanBoogie\Core::lazy_get_user' => $hooks . 'get_user',
		'ICanBoogie\Core::lazy_get_user_id' => $hooks . 'get_user_id',
		'ICanBoogie\Core::lazy_get_user_permission_resolver' => $hooks . 'get_user_permission_resolver',
		'ICanBoogie\Core::lazy_get_user_ownership_resolver' => $hooks . 'get_user_ownership_resolver',
		'ICanBoogie\Core::check_user_permission' => $hooks . 'check_user_permission',
		'ICanBoogie\Core::check_user_ownership' => $hooks . 'check_user_ownership'

	],

	'patron.markups' => [

		'user' => [ $hooks . 'markup_user' ],
		'users:form:login' => [ $hooks . 'markup_form_login' ]

	]

];