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

use Brickrouge\Element;
use Icybee\Block\ManageBlock\Column;
use Icybee\Modules\Users\User;

/**
 * Representation of the `username` column.
 */
class UsernameColumn extends Column
{
	/**
	 * @param User $record
	 *
	 * @inheritdoc
	 */
	public function render_cell($record)
	{
		$label = $record->username;
		$name = $record->name;

		if ($label != $name)
		{
			$label .= ' <small>(' . $name . ')</small>';
		}

		return new Element('a', [

			Element::INNER_HTML => $label,

			'class' => 'edit',
			'href' => $this->app->url_for("admin:{$record->constructor}:edit", $record),
			'title' => $this->t('manage.edit')

		]);
	}
}
