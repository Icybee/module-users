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

use Icybee\BlockController;

/**
 * Class ProfileController
 *
 * @package Icybee\Modules\Users
 *
 * @property-read User $user
 */
class ProfileController extends BlockController
{
	protected function control_permission($permission)
	{
		$user = $this->user;

		if ($user->is_guest)
		{
			return false;
		}

		return $user->has_permission('modify own profile');
	}
}
