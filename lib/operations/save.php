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

/**
 * Create or update a user profile.
 */
class SaveOperation extends \Icybee\Operation\Constructor\Save
{
	protected function lazy_get_properties()
	{
		$properties = parent::lazy_get_properties();
		$request = $this->request;

		#
		# password
		#

		unset($properties[User::PASSWORD_HASH]);

		if ($request[User::PASSWORD])
		{
			$properties[User::PASSWORD_HASH] = User::hash_password($request[User::PASSWORD]);
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
	 * @see ICanBoogie.Operation::control_form()
	 */
	protected function control_form()
	{
		$this->request->params[User::ROLES][2] = 'on';

		return parent::control_form($this);
	}

	protected function validate(\ICanboogie\Errors $errors)
	{
		$properties = $this->properties;

		if (!empty($properties[User::PASSWORD]))
		{
			if (!$this->request[User::PASSWORD . '-verify'])
			{
				$errors[User::PASSWORD . '-verify'] = $errors->format('Password verify is empty.');
			}

			if ($properties[User::PASSWORD] != $this->request[User::PASSWORD . '-verify'])
			{
				$errors[User::PASSWORD . '-verify'] = $errors->format("Password and password verify don't match.");
			}
		}

		$uid = $this->key ? $this->key : 0;
		$model = $this->app->models['users'];

		#
		# unique username
		#

		if (isset($properties[User::USERNAME]))
		{
			$username = $properties[User::USERNAME];
			$used = $model->select('uid')->where('username = ? AND uid != ?', $username, $uid)->rc;

			if ($used)
			{
				$errors[User::USERNAME] = $errors->format("The user name %username is already used.", [

					'%username' => $username

				]);
			}
		}

		#
		# check if email is unique
		#

		if (isset($properties[User::EMAIL]))
		{
			$email = $properties[User::EMAIL];
			$used = $model->select('uid')->where('email = ? AND uid != ?', $email, $uid)->rc;

			if ($used)
			{
				$errors[User::EMAIL] = $errors->format("The email address %email est already used.", [

					'%email' => $email

				]);
			}
		}

		return count($errors) == 0 && parent::validate($errors);
	}

	protected function process()
	{
		$previous_uid = $this->app->user_id;

		$rc = parent::process();

		$uid = $rc['key'];

		$errors = $this->response->errors;

		if (!$previous_uid)
		{
			$this->response->message = $errors->format("Your profile has been created.");
		}
		else if ($previous_uid == $uid)
		{
			$this->response->message = $errors->format($rc['mode'] == 'update' ? "Your profile has been updated." : "Your profile has been created.");
		}
		else
		{
			/* @var $record User */

			$record = $this->module->model[$uid];

			$this->response->message = $errors->format($rc['mode'] == 'update' ? "%name's profile has been updated." : "%name's profile has been created.", [ 'name' => $record->name ]);
		}

		return $rc;
	}
}
