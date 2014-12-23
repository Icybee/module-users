<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Users;

use ICanBoogie\AuthenticationRequired;

use Icybee\Modules\Views\ViewOptions;

class View extends \Icybee\Modules\Views\View
{
	/**
	 * @throws HTTPError with code 401 if the record is offline and the user don't have access
	 * permission to the module.
	 *
	 * @inheritdoc
	 */
	protected function provide($provider, array $conditions)
	{
		if ($this->renders == ViewOptions::RENDERS_MANY)
		{
			$conditions['is_activated'] = true;
		}

		$rc = parent::provide($provider, $conditions);

		if ($rc instanceof User && !$rc->is_activated)
		{
			if (!\ICanBoogie\app()->user->has_permission(\ICanBoogie\Module::PERMISSION_ACCESS, $rc->constructor))
			{
				throw new AuthenticationRequired;
			}
		}

		return $rc;
	}
}
