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
 * Unlocks login locked after multiple failed login attempts.
 *
 * - username (string) Username of the locked account.
 * - token (string) Token to unlock the account.
 * - continue (string)[optional] Destination of the operation successful process. Default to '/'.
 *
 * @property User $record
 */
class UnlockLoginOperation extends Operation
{
	protected function lazy_get_record()
	{
		$username = $this->request['username'];

		return $this->module->model->where('username = ? OR email = ?', $username, $username)->one;
	}

	protected function validate(Errors $errors)
	{
		$token = $this->request['token'];

		if (!$this->request['username'] || !$token)
		{
			return false;
		}

		$user = $this->record;

		if (!$user)
		{
			throw new \Exception('Unknown user');
		}

		if ($user->metas['login_unlock_token'] != $token)
		{
			throw new \Exception('Invalid token.');
		}

		return true;
	}

	protected function process()
	{
		$user = $this->record;

		$user->metas['login_unlock_token'] = null;
		$user->metas['login_unlock_time'] = null;
		$user->metas['failed_login_count'] = 0;

		$this->response->message = 'Login has been unlocked';
		$this->response->location = isset($this->request['continue']) ? $this->request['continue'] : '/';

		return true;
	}
}
