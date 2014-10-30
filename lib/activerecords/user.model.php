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

class Model extends \Icybee\ActiveRecord\Model\Constructor
{
	public function save(array $properties, $key=null, array $options=[])
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

		#
		# roles
		#

		if (isset($properties[User::ROLES]))
		{
			$has_many_roles = ActiveRecord\get_model('users/has_many_roles');

			if ($key)
			{
				$has_many_roles->filter_by_uid($key)->delete();
			}

			foreach ($properties[User::ROLES] as $rid)
			{
				if ($rid == 2)
				{
					continue;
				}

				$has_many_roles->execute('INSERT {self} SET uid = ?, rid = ?', [ $rc, $rid ]);
			}
		}

		#
		# sites
		#

		if (isset($properties[User::RESTRICTED_SITES]))
		{
			$has_many_sites = ActiveRecord\get_model('users/has_many_sites');

			if ($key)
			{
				$has_many_sites->filter_by_uid($key)->delete();
			}

			foreach ($properties[User::RESTRICTED_SITES] as $siteid)
			{
				$has_many_sites->execute('INSERT {self} SET uid = ?, siteid = ?', [ $rc, $siteid ]);
			}
		}

		return $rc;
	}
}
