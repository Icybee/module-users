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

use function ICanBoogie\app;

/**
 * A trait to handle a password.
 *
 * The trait adds support for a `password` and `password_hash` property. The `password` property
 * is white-only, and the `password_hash` property is private and only accessible by the class
 * using the trait. The {@link verify_password()} and {@link hash_password()} method must be
 * used for password Operation.
 *
 * Currently, for the trait to work properly the `password` property needs to be unset during
 * `__construct()` and the `password_hash` property needs to be added during `to_array()`:
 *
 * <pre>
 * <?php
 *
 * class User extends \ICanBoogie\ActiveRecord
 * {
 *     public function __construct($model=self::MODEL_ID)
 *     {
 *         if (isset($this->password))
 *         {
 *             $this->set_password($this->password);
 *
 *             unset($this->password);
 *         }
 *
 *         parent::__construct($model);
 *     }
 *
 *     // …
 *
 *     public function to_array()
 *     {
 *         return parent::to_array() + [
 *
 *             'password_hash' => $this->password_hash
 *         ];
 *     }
 *
 *     // …
 * }
 * </pre>
 *
 * @property-write string $password
 */
trait PasswordTrait
{
	/**
	 * User password.
	 *
	 * The property is only used to update the {@link $password_hash} property when the
	 * record is saved.
	 *
	 * @var string
	 */
	protected function set_password($password)
	{
		if (!$password)
		{
			$this->password_hash = null;

			return;
		}

		$this->password_hash = $this->hash_password($password);
	}

	/**
	 * User password hash.
	 *
	 * @var string
	 */
	private $password_hash;

	/**
	 * Checks if the password hash is a legacy hash, and not a hash created by
	 * the {@link \password_hash()} function.
	 *
	 * @return bool|null `true` if the password hash is a legacy hash, `false` if the password
	 * hash was created by the {@link \password_hash()} function, and `null` if the password hash
	 * is empty.
	 */
	protected function get_has_legacy_password_hash()
	{
		if (!$this->password_hash)
		{
			return null;
		}

		return $this->password_hash[0] != '$';
	}

	/**
	 * Hashes a password.
	 *
	 * @param string $password
	 *
	 * @return string
	 */
	static public function hash_password($password)
	{
		return password_hash($password, \PASSWORD_BCRYPT);
	}

	/**
	 * Compares a password to the user's password hash.
	 *
	 * @param string $password
	 *
	 * @return bool `true` if the hashed password matches the user's password hash,
	 * `false` otherwise.
	 */
	public function verify_password($password)
	{
		if (password_verify($password, $this->password_hash))
		{
			return true;
		}

		#
		# Trying old hashing
		#

		$config = app()->configs['user'];

		if (empty($config['password_salt']))
		{
			return false;
		}

		return sha1(hash_pbkdf2('sha1', $password, $config['password_salt'], 1000)) == $this->password_hash;
	}
}
