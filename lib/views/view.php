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

use ICanBoogie\HTTP\AuthenticationRequired;
use Icybee\Modules\Views\ViewOptions;

/**
 * @property-read User $user
 */
class View extends \Icybee\Modules\Views\View
{
	protected function get_user()
	{
		return $this->app->user;
	}

	/**
	 * @throws AuthenticationRequired with code 401 if the record is offline and the user don't
	 * have access permission to the module.
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
			if (!$this->user->has_permission(Module::PERMISSION_ACCESS, $rc->constructor))
			{
				throw new AuthenticationRequired;
			}
		}

		return $rc;
	}
}
