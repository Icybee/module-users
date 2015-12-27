<?php

namespace Icybee\Modules\Users\Facets;

use ICanBoogie\Facets\Criterion\BooleanCriterion;
use ICanBoogie\Facets\Criterion\DateCriterion;

return [

	'facets' => [

		'users' => [

			'username' => UsernameCriterion::class,
			'is_activated' => BooleanCriterion::class,
			'created_at' => DateCriterion::class,
			'logged_at' => DateCriterion::class

		]
	]
];
