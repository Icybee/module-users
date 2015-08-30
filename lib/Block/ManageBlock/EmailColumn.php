<?php

namespace Icybee\Modules\Users\Block\ManageBlock;

use Icybee\Block\ManageBlock\Column;
use Icybee\Modules\Users\User;

/**
 * Representation of the `email` column.
 */
class EmailColumn extends Column
{
	/**
	 * @param User $record
	 *
	 * @inheritdoc
	 */
	public function render_cell($record)
	{
		$email = $record->email;

		return '<a href="mailto:' . $email . '" title="' . $this->manager->t('Send an E-mail') . '">' . $email . '</a>';
	}
}
