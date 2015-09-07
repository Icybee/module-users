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

use ICanBoogie\DateTime;
use ICanBoogie\ActiveRecord;

use Icybee\ConstructorModel;

class UserModel extends ConstructorModel
{
	public function save(array $properties, $key = null, array $options = [])
	{
		if (!$key)
		{
			if (empty($properties[User::PASSWORD_HASH]))
			{
				$properties[User::PASSWORD_HASH] = User::hash_password(uniqid(true));
			}

			if (empty($properties[User::CREATED_AT]) || DateTime::from($properties[User::CREATED_AT])->is_empty)
			{
				$properties[User::CREATED_AT] = DateTime::now();
			}
		}

		#
		# If defined, the password is encrypted before we pass it to our super class.
		#

		if (!empty($properties[User::PASSWORD]))
		{
			$properties[User::PASSWORD_HASH] = User::hash_password($properties[User::PASSWORD]);
		}

		$rc = parent::save($properties, $key, $options);

		if (array_key_exists(User::ROLES, $properties))
		{
			$this->save_roles($key, $rc, $properties[User::ROLES]);
		}

		if (array_key_exists(User::RESTRICTED_SITES, $properties))
		{
			$this->save_restricted_sites($key, $rc, $properties[User::RESTRICTED_SITES]);
		}

		return $rc;
	}

	protected function save_roles($key, $rc, $roles)
	{
		$has_many_roles = $this->models['users/has_many_roles'];

		if ($key)
		{
			$has_many_roles->filter_by_uid($key)->delete();
		}

		foreach ($roles as $rid)
		{
			if ($rid == 2)
			{
				continue;
			}

			$has_many_roles->execute('INSERT {self} SET uid = ?, rid = ?', [ $rc, $rid ]);
		}
	}

	protected function save_restricted_sites($key, $rc, $restricted_sites)
	{
		$has_many_sites = $this->models['users/has_many_sites'];

		if ($key)
		{
			$has_many_sites->filter_by_uid($key)->delete();
		}

		foreach ($restricted_sites as $site_id)
		{
			$has_many_sites->execute('INSERT {self} SET uid = ?, site_id = ?', [ $rc, $site_id ]);
		}
	}
}
