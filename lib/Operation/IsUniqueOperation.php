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

use ICanBoogie\Errors;
use ICanBoogie\Operation;
use Icybee\Modules\Users\User;

/**
 * Checks whether the specified email or username is unique, as in _not already used_.
 */
class IsUniqueOperation extends Operation
{
	/**
	 * @inheritdoc
	 */
	protected function get_controls()
	{
		return [

			self::CONTROL_AUTHENTICATION => true

		] + parent::get_controls();
	}

	/**
	 * @inheritdoc
	 */
	protected function validate(Errors $errors)
	{
		$request = $this->request;
		$username = $request[User::USERNAME];
		$email = $request[User::EMAIL];
		$uid = $request[User::UID] ?: 0;

		if ($username)
		{
			if ($this->module->model->select('uid')->where('username = ? AND uid != ?', $username, $uid)->rc)
			{
				$errors[User::USERNAME] = 'This username is already used';
			}
		}
		else
		{
			$errors[User::USERNAME] = null;
		}

		if ($email)
		{
			if ($this->module->model->select('uid')->where('email = ? AND uid != ?', $email, $uid)->rc)
			{
				$errors[User::EMAIL] = 'This email is already used';
			}
		}
		else
		{
			$errors[User::EMAIL] = null;
		}

		return count($errors) == 0;
	}

	/**
	 * @inheritdoc
	 */
	protected function process()
	{
		return true;
	}
}