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

use Icybee\Block\ManageBlock\DateTimeColumn;
use Icybee\Modules\Users\User;

/**
 * Representation of the `logged_at` column.
 */
class LoggedAtColumn extends DateTimeColumn
{
	/**
	 * @param User $record
	 *
	 * @inheritdoc
	 */
	public function render_cell($record)
	{
		$logged_at = $record->logged_at;

		if ($logged_at->is_empty)
		{
			return '<em class="small">' . $this->manager->t("Never connected") . '</em>';
		}

		return parent::render_cell($record);
	}
}
