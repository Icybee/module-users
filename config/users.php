<?php

namespace Icybee\Modules\Users;

$hooks = Hooks::class . '::';

return [

	'ownership_resolver_list' => [

		'users' => $hooks . 'resolve_user_ownership'

	]
];
