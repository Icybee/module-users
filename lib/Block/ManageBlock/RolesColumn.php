<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Users\Block\ManageBlock;

use Icybee\Block\ManageBlock\Column;
use Icybee\Modules\Users\Roles\Binding\UserBindings;
use Icybee\Modules\Users\User;

/**
 * Representation of the `roles` column.
 */
class RolesColumn extends Column
{
	/**
	 * @param User|UserBindings $record
	 *
	 * @inheritdoc
	 */
	public function render_cell($record)
	{
		if ($record->uid == 1)
		{
			return '<em>Admin</em>';
		}

		if (!$record->roles)
		{
			return null;
		}

		$label = '';

		foreach ($record->roles as $role)
		{
			if ($role->rid == 2)
			{
				continue;
			}

			$label .= ', ' . $role->name;
		}

		return substr($label, 2);
	}
}
