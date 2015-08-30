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

use Icybee\Block\ManageBlock\BooleanColumn;
use Icybee\Modules\Users\Block\ManageBlock;
use Icybee\Modules\Users\User;

/**
 * Representation of the `is_activated` column.
 */
class IsActivatedColumn extends BooleanColumn
{
	public function __construct(ManageBlock $manager, $id, array $options = [])
	{
		parent::__construct($manager, $id, $options + [

				'title' => null,
				'class' => 'cell-fitted',
				'filters' => [

					'options' => [

						'=1' => 'Activated',
						'=0' => 'Deactivated'

					]

				]

			]);
	}

	/**
	 * @param User $record
	 *
	 * @inheritdoc
	 */
	public function render_cell($record)
	{
		if ($record->is_admin)
		{
			return null;
		}

		return parent::render_cell($record);
	}
}
