<?php

namespace Icybee\Modules\Users;

use ICanBoogie;

$hooks = Hooks::class . '::';

return [

	ICanBoogie\Application::class . '::lazy_get_user' => $hooks . 'get_user',
	ICanBoogie\Application::class . '::lazy_get_user_id' => $hooks . 'get_user_id',
	ICanBoogie\Application::class . '::lazy_get_user_permission_resolver' => $hooks . 'get_user_permission_resolver',
	ICanBoogie\Application::class . '::lazy_get_user_ownership_resolver' => $hooks . 'get_user_ownership_resolver',
	ICanBoogie\Application::class . '::check_user_permission' => $hooks . 'check_user_permission',
	ICanBoogie\Application::class . '::check_user_ownership' => $hooks . 'check_user_ownership'

];
