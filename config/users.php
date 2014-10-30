<?php

namespace Icybee\Modules\Users;

$hooks = __NAMESPACE__ . '\Hooks::';

return [

	'ownership_resolver_list' => [

		'users' => $hooks . 'resolve_user_ownership'

	]
];
