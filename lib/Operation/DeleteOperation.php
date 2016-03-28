<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Users\Operation;

use ICanBoogie\ErrorCollection;

use Icybee\Modules\Users\User;

/**
 * Deletes a user.
 *
 * @property User $record
 */
class DeleteOperation extends \ICanBoogie\Module\Operation\DeleteOperation
{
	/**
	 * @inheritdoc
	 */
	protected function validate(ErrorCollection $errors)
	{
		if ($this->key == 1)
		{
			$errors->add('uid', "Daddy cannot be deleted.");
		}

		return parent::validate($errors);
	}
}
