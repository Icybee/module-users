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
use ICanBoogie\Operation;
use Icybee\Modules\Users\Module;
use Icybee\Modules\Users\User;

/**
 * Enables a user account.
 *
 * @property User $record
 */
class ActivateOperation extends Operation
{
	/**
	 * @inheritdoc
	 */
	protected function get_controls()
	{
		return [

			self::CONTROL_PERMISSION => Module::PERMISSION_ADMINISTER,
			self::CONTROL_RECORD => true,
			self::CONTROL_OWNERSHIP => true

		] + parent::get_controls();
	}

	/**
	 * @inheritdoc
	 */
	protected function validate(ErrorCollection $errors)
	{
		return true;
	}

	/**
	 * @inheritdoc
	 */
	protected function process()
	{
		$record = $this->record;
		$record->is_activated = true;
		$record->save();

		$this->response->message = $this->format('!name account is active.', [

			'!name' => $record->name

		]);

		return true;
	}
}
