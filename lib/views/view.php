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

use ICanBoogie\HTTP\HTTPError;

class View extends \Icybee\Modules\Views\View
{
	/**
	 * @throws HTTPError with code 401 if the record is offline and the user don't have access
	 * permission to the module.
	 */
	protected function provide($provider, array $conditions)
	{
		global $core;

		if ($this->renders == self::RENDERS_MANY)
		{
			$conditions['is_active'] = true;
		}

		$rc = parent::provide($provider, $conditions);

		if ($rc instanceof User && !$rc->is_active)
		{
			if (!$core->user->has_permission(\ICanBoogie\Module::PERMISSION_ACCESS, $rc->constructor))
			{
				throw new HTTPError('The requested record requires authentication.', 401);
			}
		}

		return $rc;
	}
}
