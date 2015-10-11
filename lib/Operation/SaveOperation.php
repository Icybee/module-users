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
use ICanBoogie\HTTP\Request;

use Icybee\Modules\Users\Module;
use Icybee\Modules\Users\User;
use Icybee\Modules\Users\UserModel;

/**
 * Create or update a user profile.
 *
 * @property-read UserModel $model
 * @property User $record User
 */
class SaveOperation extends \ICanBoogie\Module\Operation\SaveOperation
{
	protected function get_model()
	{
		return $this->module->model;
	}

	protected function lazy_get_properties()
	{
		$properties = parent::lazy_get_properties();
		$request = $this->request;

		unset($properties[User::PASSWORD_HASH]);

		if ($request[User::PASSWORD])
		{
			$properties[User::PASSWORD] = $request[User::PASSWORD];
		}

		if ($this->app->user->has_permission(Module::PERMISSION_ADMINISTER, $this->module))
		{
			#
			# roles - because roles are not in the properties we need to prepare them for the
			# model using the params.
			#

			$roles = [];

			if ($request[User::ROLES])
			{
				foreach ($request[User::ROLES] as $rid => $value)
				{
					$value = filter_var($value, FILTER_VALIDATE_BOOLEAN);

					if (!$value)
					{
						continue;
					}

					$roles[] = (int) $rid;
				}
			}

			$properties[User::ROLES] = $roles;

			#
			# restricted sites - because restricted sites are not in the properties we need to
			# prepare them for the model using the params.
			#

			$sites = [];

			if ($request[User::RESTRICTED_SITES])
			{
				foreach ($request[User::RESTRICTED_SITES] as $site_id => $value)
				{
					$value = filter_var($value, FILTER_VALIDATE_BOOLEAN);

					if (!$value)
					{
						continue;
					}

					$sites[] = (int) $site_id;
				}
			}

			$properties[User::RESTRICTED_SITES] = $sites;
		}
		else
		{
			unset($properties[User::IS_ACTIVATED]);
		}

		return $properties;
	}

	/**
	 * Returns the form from the `edit` block if the getter wasn't able to retrieve the form. This
	 * is currently used to create records using XHR.
	 */
	protected function lazy_get_form()
	{
		$form = parent::lazy_get_form();

		if ($form)
		{
			return $form;
		}

		$block = $this->module->getBlock('edit', $this->key);

		return $block->element;
	}

	/**
	 * Permission is granted if the user is modifying its own profile, and has permission to.
	 *
	 * @inheritdoc
	 */
	protected function control_permission($permission = Module::PERMISSION_CREATE)
	{
		$user = $this->app->user;

		if ($user->uid == $this->key && $user->has_permission('modify own profile'))
		{
			return true;
		}

		return parent::control_permission($permission);
	}

	protected function control_ownership()
	{
		$user = $this->app->user;

		if ($user->uid == $this->key && $user->has_permission('modify own profile'))
		{
			// TODO-20110105: it this ok to set the user as a record here ?

			$this->record = $user;

			return true;
		}

		return parent::control_ownership();
	}

	/**
	 * The 'User' role (rid 2) is mandatory for every user.
	 *
	 * @inheritdoc
	 */
	protected function action(Request $request)
	{
		$request->params[User::ROLES][2] = 'on';

		return parent::action($request);
	}

	/**
	 * Validates the `password`, `username`, and `email` properties.
	 *
	 * @inheritdoc
	 */
	protected function validate(Errors $errors)
	{
		$request = $this->request;

		$this->validate_password($request, $errors);
		$this->validate_username($request, $errors);
		$this->validate_email($request, $errors);

		return parent::validate($errors);
	}

	/**
	 * Checks that the password matches password-verify.
	 *
	 * @param Request $request
	 * @param Errors $errors
	 */
	protected function validate_password(Request $request, Errors $errors)
	{
		$password = $request[User::PASSWORD];

		if (!$password)
		{
			return;
		}

		$password_verify = $request[User::PASSWORD_VERIFY];

		if (!$password_verify)
		{
			$errors->add(User::PASSWORD_VERIFY, "Password verify is empty.");

			return;
		}

		if ($password === $password_verify)
		{
			return;
		}

		$errors->add(User::PASSWORD_VERIFY, "Password and password verify don't match.");
	}

	/**
	 * Checks that the username is unique.
	 *
	 * @param Request $request
	 * @param Errors $errors
	 */
	protected function validate_username(Request $request, Errors $errors)
	{
		$username = $request[User::USERNAME];

		if (!$username)
		{
			return;
		}

		$uid = $this->key ?: 0;
		$used = $this->model->where('username = ? AND uid != ?', $username, $uid)->exists;

		if (!$used)
		{
			return;
		}

		$errors->add(User::USERNAME, "The user name %username is already used.", [

			'%username' => $username

		]);
	}

	/**
	 * Checks that the email is email.
	 *
	 * @param Request $request
	 * @param Errors $errors
	 */
	protected function validate_email(Request $request, Errors $errors)
	{
		$email = $request[User::EMAIL];

		if (!$email)
		{
			return;
		}

		$uid = $this->key ?: 0;
		$used = $this->model->where('email = ? AND uid != ?', $email, $uid)->exists;

		if (!$used)
		{
			return;
		}

		$errors->add(User::EMAIL, "The email address %email is already used.", [

			'%email' => $email

		]);
	}

	protected function process()
	{
		$previous_uid = $this->app->user_id;

		$rc = parent::process();

		if (!$previous_uid)
		{
			$this->response->message = $this->format("Your profile has been created.");
		}
		else if ($previous_uid == $rc['key'])
		{
			$this->response->message = $this->format($rc['mode'] == 'update' ? "Your profile has been updated." : "Your profile has been created.");
		}
		else
		{
			$record = $this->record;

			$this->response->message = $this->format($rc['mode'] == 'update' ? "%name's profile has been updated." : "%name's profile has been created.", [ 'name' => $record->name ]);
		}

		return $rc;
	}
}
