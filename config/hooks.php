<?php

namespace Icybee\Modules\Users;

use ICanBoogie;
use Icybee;

$hooks = Hooks::class . '::';

return [

	'patron.markups' => [

		'user' => [ $hooks . 'markup_user' ],
		'users:form:login' => [ $hooks . 'markup_form_login' ]

	]

];
