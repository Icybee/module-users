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

use Icybee\Binding\Core\PrototypedBindings;
use Icybee\Modules\Users\User;

/**
 * Log the user out of the system.
 *
 * @property-read User $record The active record representing the user that was logged out. This
 * property is still available after the user was logged out, unlike the {@link $user} property of
 * `ICanBoogie/Core` instances.
 */
class LogoutOperation extends Operation
{
	use PrototypedBindings;

	/**
	 * Returns the record of the user to logout.
	 *
	 * The current user is returned.
	 */
	protected function lazy_get_record()
	{
		return $this->app->user;
	}

	/**
	 * Adds the {@link CONTROL_RECORD} control.
	 */
	protected function get_controls()
	{
		return [

			self::CONTROL_RECORD => true

		] + parent::get_controls();
	}

	/**
	 * Always returns `true`.
	 *
	 * @inheritdoc
	 */
	protected function validate(ErrorCollection $errors)
	{
		return true;
	}

	/**
	 * Logs out the user.
	 *
	 * The {@link logout()} method of the user is invoked to log the user out.
	 *
	 * The location of the response can be defined by the `continue` request parameter or the request referer, or '/'.
	 */
	protected function process()
	{
		$this->record->logout();

		$request = $this->request;
		$this->response->location = ($request['redirect_to'] ?: $request['continue']) ?: ($request->referer ?: '/');

		return true;
	}
}
