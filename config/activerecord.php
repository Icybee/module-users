<?php

namespace Icybee\Modules\Users;

use ICanBoogie\Facets\BooleanCriterion;
use ICanBoogie\Facets\DateTimeCriterion;

return [

	'facets' => [

		'users' => [

			'username' => UsernameCriterion::class,
			'is_activated' => BooleanCriterion::class,
			'created_at' => DateTimeCriterion::class,
			'logged_at' => DateTimeCriterion::class

		]
	]
];
