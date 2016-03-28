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
use ICanBoogie\HTTP\Request;
use ICanBoogie\I18n;
use ICanBoogie\Operation;
use ICanBoogie\Module\ControllerBindings as ModuleBindings;

use Icybee\Binding\Core\PrototypedBindings;
use Icybee\Modules\Registry\MetaCollection;
use Icybee\Modules\Users\LoginForm;
use Icybee\Modules\Users\User;

/**
 * Log in a user.
 *
 * @property \ICanBoogie\Core|\Icybee\Binding\CoreBindings|\ICanBoogie\Binding\Mailer\CoreBindings $app
 * @property User $record The logged user.
 */
class LoginOperation extends Operation
{
	use PrototypedBindings, ModuleBindings;

	/**
	 * Adds form control.
	 */
	protected function get_controls()
	{
		return [

			self::CONTROL_FORM => true

		] + parent::get_controls();
	}

	/**
	 * Returns the "connect" form of the target module.
	 */
	protected function lazy_get_form()
	{
		return new LoginForm;
	}

	/**
	 * @inheritdoc
	 */
	protected function validate(ErrorCollection $errors)
	{
		$request = $this->request;
		$username = $request[User::USERNAME];
		$password = $request[User::PASSWORD];

		/* @var $uid int */

		$uid = $this->model
		->select('uid')
		->where('username = ? OR email = ?', $username, $username)
		->rc;

		if (!$uid)
		{
			$errors
				->add(User::USERNAME)
				->add(User::PASSWORD)
				->add_generic("Unknown username/password combination.");

			return false;
		}

		/* @var $user User */
		/* @var $metas MetaCollection */

		$user = $this->model[$uid];
		$metas = $user->metas;

		$now = time();
		$login_unlock_time = $metas['login_unlock_time'];

		if ($login_unlock_time)
		{
			if ($login_unlock_time > $now)
			{
				throw new \Exception
				(
					\ICanBoogie\format("The user account has been locked after multiple failed login attempts.
					An e-mail has been sent to unlock the account. Login attempts are locked until %time,
					unless you unlock the account using the email sent.", [

						'%count' => $metas['failed_login_count'],
						'%time' => I18n\format_date($login_unlock_time, 'HH:mm')

					]),

					403
				);
			}

			$metas['login_unlock_time'] = null;
		}

		if (!$user->verify_password($password))
		{
			$errors
				->add(User::USERNAME)
				->add(User::PASSWORD)
				->add_generic("Unknown username/password combination.");

			$metas['failed_login_count'] += 1;
			$metas['failed_login_time'] = $now;

			if ($metas['failed_login_count'] >= 10)
			{
				$token = \ICanBoogie\generate_token(40, \ICanBoogie\TOKEN_ALPHA . \ICanBoogie\TOKEN_NUMERIC);

				$metas['login_unlock_token'] = $token;
				$metas['login_unlock_time'] = $now + 3600;

				$until = I18n\format_date($now + 3600, 'HH:mm');

				$url = $this->app->site->url . '/api/users/unlock_login?' . http_build_query([

					'username' => $username,
					'token' => $token,
					'continue' => $request->uri

				]);

				$this->app->mail([

					'destination' => $user->email,
					'from' => 'no-reply@' . $request->headers['Host'],
					'subject' => "Your account has been locked",
					'body' => <<<EOT
You receive this message because your account has been locked.

After multiple failed login attempts your account has been locked until $until. You can use the
following link to unlock your account and try to login again:

<$url>

If you forgot your password, you'll be able to request a new one.

If you didn't try to login neither forgot your password, this message might be the result of an
attack attempt on the website. If you think this is the case, please contact its admin.

The remote address of the request was: $request->ip.
EOT
				]);

				unset($errors[User::PASSWORD]);

				$errors->add_generic("Your account has been locked, a message has been sent to your e-mail address.");
			}

			return false;
		}

		if (!$user->is_admin && !$user->is_activated)
		{
			$errors->add_generic("User %username is not activated", [ '%username' => $username ]);

			return false;
		}

		$this->record = $user;

		return true;
	}

	/**
	 * Logs the user with {@link User::login()} and updates its logged date.
	 *
	 * If the user uses as legacy password, its password is updated.
	 *
	 * @return bool `true` if the user is logged.
	 */
	protected function process()
	{
		$user = $this->record;
		$user->metas['failed_login_count'] = null;
		$user->metas['failed_login_time'] = null;
		$user->logged_at = 'now';

		if ($user->has_legacy_password_hash)
		{
			$user->password = $this->request['password'];
		}

		$user->save();
		$user->login();

		$redirect_to = ($this->request['redirect_to'] ?: $this->request['continue']) ?: null;

		if ($redirect_to)
		{
			$this->response->location = $redirect_to;
		}

		return true;
	}
}
