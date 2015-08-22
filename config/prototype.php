<?php

namespace Icybee\Modules\Users;

use ICanBoogie;

$hooks = Hooks::class . '::';

return [

	ICanBoogie\Core::class . '::lazy_get_user' => $hooks . 'get_user',
	ICanBoogie\Core::class . '::lazy_get_user_id' => $hooks . 'get_user_id',
	ICanBoogie\Core::class . '::lazy_get_user_permission_resolver' => $hooks . 'get_user_permission_resolver',
	ICanBoogie\Core::class . '::lazy_get_user_ownership_resolver' => $hooks . 'get_user_ownership_resolver',
	ICanBoogie\Core::class . '::check_user_permission' => $hooks . 'check_user_permission',
	ICanBoogie\Core::class . '::check_user_ownership' => $hooks . 'check_user_ownership'

];
