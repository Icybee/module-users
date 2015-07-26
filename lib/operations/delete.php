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

use ICanBoogie\DeleteOperation as Super;
use ICanBoogie\Errors;

/**
 * Deletes a user.
 */
class DeleteOperation extends Super
{
	/**
	 * @inheritdoc
	 */
	protected function validate(Errors $errors)
	{
		if ($this->key == 1)
		{
			$errors['uid'] = $errors->format("Daddy cannot be deleted.");
		}

		return parent::validate($errors);
	}
}
