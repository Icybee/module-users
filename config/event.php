<?php

namespace Icybee\Modules\Users;

use ICanBoogie;
use Icybee;

$hooks = Hooks::class . '::';

return [

	ICanBoogie\HTTP\AuthenticationRequired::class .'::rescue' => $hooks . 'on_security_exception_rescue',
	ICanBoogie\HTTP\PermissionRequired::class . '::rescue' => $hooks . 'on_security_exception_rescue',
	ICanBoogie\Routing\RouteDispatcher::class . '::dispatch:before' => $hooks . 'before_routing_dispatcher_dispatch',
	Icybee\Modules\Users\Roles\Operation\DeleteOperation::class . '::process:before' => $hooks . 'before_roles_delete',
	Icybee\Modules\Users\WebsiteAdminNotAccessible::class . '::rescue' => $hooks . 'on_website_admin_not_accessible_rescue'

];
