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

use ICanBoogie\ActiveRecord\CreatedAtProperty;
use ICanBoogie\ActiveRecord\DateTimePropertySupport;
use ICanBoogie\ActiveRecord\RecordNotFound;
use ICanBoogie\DateTime;

use Icybee\Modules\Users\Roles\Role;

/**
 * A user.
 *
 * @property-read string $name The formatted name of the user.
 * @property-read boolean $is_admin true if the user is admin, false otherwise.
 * @property-read boolean $is_guest true if the user is a guest, false otherwise.
 * @property-read \Icybee\Modules\Users\Users\Role $role
 *
 * @property-read string $password_hash The password hash.
 * @property-read bool|null $has_legacy_password_hash Whether the password hash is a legacy hash.
 * {@link User::get_has_legacy_password_hash()}.
 *
 * @property \ICanBoogie\DateTime $logged_at The date and time at which the user was logged.
 */
class User extends \ICanBoogie\ActiveRecord implements \Brickrouge\CSSClassNames
{
	use \Brickrouge\CSSClassNamesProperty;

	const UID = 'uid';
	const EMAIL = 'email';
	const PASSWORD = 'password';
	const PASSWORD_HASH = 'password_hash';
	const USERNAME = 'username';
	const FIRSTNAME = 'firstname';
	const LASTNAME = 'lastname';
	const NICKNAME = 'nickname';
	const CREATED_AT = 'created_at';
	const LOGGED_AT = 'logged_at';
	const CONSTRUCTOR = 'constructor';
	const LANGUAGE = 'language';
	const TIMEZONE = 'timezone';
	const IS_ACTIVATED = 'is_activated';
	const ROLES = 'roles';
	const RESTRICTED_SITES = 'restricted_sites';

	const NAME_AS = 'name_as';

	/**
	 * The {@link $name} property should be created from `$username`.
	 *
	 * @var int
	 */
	const NAME_AS_USERNAME = 0;

	/**
	 * The {@link $name} property should be created from `$firstname`.
	 *
	 * @var int
	 */
	const NAME_AS_FIRSTNAME = 1;

	/**
	 * The {@link $name} property should be created from `$lastname`.
	 *
	 * @var int
	 */
	const NAME_AS_LASTNAME = 2;

	/**
	 * The {@link $name} property should be created from `$firstname $lastname`.
	 *
	 * @var int
	 */
	const NAME_AS_FIRSTNAME_LASTNAME = 3;

	/**
	 * The {@link $name} property should be created from `$lastname $firstname`.
	 *
	 * @var int
	 */
	const NAME_AS_LASTNAME_FIRSTNAME = 4;

	/**
	 * The {@link $name} property should be created from `$nickname`.
	 *
	 * @var int
	 */
	const NAME_AS_NICKNAME = 5;

	/**
	 * User identifier.
	 *
	 * @var string
	 */
	public $uid;

	/**
	 * Constructor of the user record (module id).
	 *
	 * The property MUST be defined to persist the record.
	 *
	 * @var string
	 */
	public $constructor;

	/**
	 * User email.
	 *
	 * The property MUST be defined to persist the record.
	 *
	 * @var string
	 */
	public $email;

	/**
	 * User password.
	 *
	 * The property is only used to update the {@link $password_hash} property when the
	 * record is saved.
	 *
	 * @var string
	 */
	protected $password;

	protected function get_password()
	{
		return $this->password;
	}

	protected function set_password($password)
	{
		$this->password = $password;

		if ($password)
		{
			$this->password_hash = $this->hash_password($password);
		}
	}

	/**
	 * User password hash.
	 *
	 * Note: The property MUST NOT be private, otherwise only instances of the class can be
	 * initialized with a value, for subclasses instances the property would be `null`.
	 *
	 * @var string
	 */
	protected $password_hash;

	/**
	 * Checks if the password hash is a legacy hash, and not a hash created by
	 * the {@link \password_hash()} function.
	 *
	 * @return bool|null `true` if the password hash is a legacy hash, `false` if the password
	 * hash was created by the {@link \password_hash()} function, and `null` if the passsword hash
	 * is empty.
	 */
	protected function get_has_legacy_password_hash()
	{
		if (!$this->password_hash)
		{
			return;
		}

		return $this->password_hash[0] != '$';
	}

	/**
	 * Username of the user.
	 *
	 * The property MUST be defined to persist the record.
	 *
	 * @var string
	 */
	public $username;

	/**
	 * First name of the user.
	 *
	 * @var string
	 */
	public $firstname = '';

	/**
	 * Last name of the user.
	 *
	 * @var string
	 */
	public $lastname = '';

	/**
	 * Nickname of the user.
	 *
	 * @var string
	 */
	public $nickname = '';

	/**
	 * Prefered format to create the value of the {@link $name} property.
	 *
	 * @var string
	 */
	public $name_as = self::NAME_AS_USERNAME;

	use CreatedAtProperty;
	use LoggedAtProperty;

	/**
	 * Prefered language of the user.
	 *
	 * @var string
	 */
	public $language = '';

	/**
	 * Prefered timezone of the user.
	 *
	 * @var string
	 */
	public $timezone = '';

	/**
	 * State of the user account activation.
	 *
	 * @var bool
	 */
	public $is_activated = false;

	/**
	 * Defaults `$model` to "users".
	 *
	 * Initializes the {@link $constructor} property with the model identifier if it is not
	 * defined.
	 *
	 * @param string|\ICanBoogie\ActiveRecord\Model $model
	 */
	public function __construct($model='users')
	{
		parent::__construct($model);

		if (empty($this->constructor))
		{
			$this->constructor = $this->model_id;
		}
	}

	public function __get($property)
	{
		$value = parent::__get($property);

		if ($property === 'css_class_names')
		{
			new \Brickrouge\AlterCSSClassNamesEvent($this, $value);
		}

		return $value;
	}

	protected function alter_persistent_properties(array $properties, \ICanBoogie\ActiveRecord\Model $model)
	{
		if ($this->get_created_at()->is_empty)
		{
			$this->set_created_at('now');
		}

		/*
		if ($this->get_updated_at()->is_empty)
		{
			$this->set_updated_at('now');
		}
		*/

		return parent::alter_persistent_properties($properties, $model);
	}

	/**
	 * Adds the {@link $logged_at} property.
	 */
	public function to_array()
	{
		$array = parent::to_array();

		if ($this->password)
		{
			$array['password'] = $this->password;
		}

		return $array;
	}

	/**
	 * Returns the formatted name of the user.
	 *
	 * The format of the name is defined by the {@link $name_as} property. The {@link $username},
	 * {@link $firstname}, {@link $lastname} and {@link $nickname} properties can be used to
	 * format the name.
	 *
	 * This is the getter for the {@link $name} magic property.
	 *
	 * @return string
	 */
	protected function get_name()
	{
		$values = [

			self::NAME_AS_USERNAME => $this->username,
			self::NAME_AS_FIRSTNAME => $this->firstname,
			self::NAME_AS_LASTNAME => $this->lastname,
			self::NAME_AS_FIRSTNAME_LASTNAME => $this->firstname . ' ' . $this->lastname,
			self::NAME_AS_LASTNAME_FIRSTNAME => $this->lastname . ' ' . $this->firstname,
			self::NAME_AS_NICKNAME => $this->nickname

		];

		$rc = isset($values[$this->name_as]) ? $values[$this->name_as] : null;

		if (!trim($rc))
		{
			return $this->username;
		}

		return $rc;
	}

	/**
	 * Returns the role of the user.
	 *
	 * This is the getter for the {@link $role} magic property.
	 *
	 * @return \Icybee\Modules\Users\Users\Role
	 */
	protected function lazy_get_role()
	{
		global $core;

		$permissions = [];
		$name = null;

		foreach ($this->roles as $role)
		{
			$name .= ', ' . $role->name;

			foreach ($role->perms as $access => $permission)
			{
				$permissions[$access] = $permission;
			}
		}

		$role = new Role();
		$role->perms = $permissions;

		if ($name)
		{
			$role->name = substr($name, 2);
		}

		return $role;
	}

	/**
	 * Returns all the roles associated with the user.
	 *
	 * This is the getter for the {@link $roles} magic property.
	 *
	 * @return array
	 */
	protected function lazy_get_roles()
	{
		global $core;

		try
		{
			if (!$this->uid)
			{
				return [ $core->models['users.roles'][1] ];
			}
		}
		catch (\Exception $e)
		{
			return [];
		}

		$rids = $core->models['users/has_many_roles']->select('rid')->filter_by_uid($this->uid)->all(\PDO::FETCH_COLUMN);

		if (!in_array(2, $rids))
		{
			array_unshift($rids, 2);
		}

		try
		{
			return $core->models['users.roles']->find($rids);
		}
		catch (RecordNotFound $e)
		{
			trigger_error($e->getMessage());

			return array_filter($e->records);
		}
	}

	/**
	 * Checks if the user is the admin user.
	 *
	 * This is the getter for the {@link $is_admin} magic property.
	 *
	 * @return boolean `true` if the user is the admin user, `false` otherwise.
	 */
	protected function get_is_admin()
	{
		return $this->uid == 1;
	}

	/**
	 * Checks if the user is a guest user.
	 *
	 * This is the getter for the {@link $is_guest} magic property.
	 *
	 * @return boolean `true` if the user is a guest user, `false` otherwise.
	 */
	protected function get_is_guest()
	{
		return !$this->uid;
	}

	/**
	 * Returns the ids of the sites the user is restricted to.
	 *
	 * This is the getter for the {@link $restricted_sites_ids} magic property.
	 *
	 * @return array The array is empty if the user has no site restriction.
	 */
	protected function lazy_get_restricted_sites_ids()
	{
		global $core;

		return $this->is_admin ? [] : $core->models['users/has_many_sites']->select('siteid')->filter_by_uid($this->uid)->all(\PDO::FETCH_COLUMN);
	}

	/**
	 * Checks if the user has a given permission.
	 *
	 * @param string|int $permission
	 * @param mixed $target
	 *
	 * @return mixed
	 */
	public function has_permission($permission, $target=null)
	{
		global $core;

		if ($this->is_admin)
		{
			return Module::PERMISSION_ADMINISTER;
		}

		return $core->check_user_permission($this, $permission, $target);
	}

	/**
	 * Checks if the user has the ownership of an entry.
	 *
	 * If the ownership information is missing from the entry (the 'uid' property is null), the user
	 * must have the ADMINISTER level to be considered the owner.
	 *
	 * @param $module
	 * @param $record
	 *
	 * @return boolean
	 */
	public function has_ownership($module, $record)
	{
		global $core;

		return $core->check_user_ownership($this, $record);
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
		return \password_hash($password, \PASSWORD_BCRYPT);
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
		if (\password_verify($password, $this->password_hash))
		{
			return true;
		}

		#
		# Trying old hashing
		#

		$config = \ICanBoogie\Core::get()->configs['user'];

		if (empty($config['password_salt']))
		{
			return false;
		}

		return sha1(\ICanBoogie\pbkdf2($password, $config['password_salt'])) == $this->password_hash;
	}

	/**
	 * Logs the user in.
	 *
	 * A user is logged in by setting its id in the `application[user_agent]` session key.
	 *
	 * Note: The method does *not* checks the user authentication !
	 *
	 * The following things happen when the user is logged in:
	 *
	 * - The `$core->user` property is set to the user.
	 * - The `$core->user_id` property is set to the user id.
	 * - The session id is regenerated and the user id, ip and user agent are stored in the session.
	 *
	 * @return boolean true if the login is successful.
	 *
	 * @throws \Exception in attempt to log in a guest user.
	 *
	 * @see \Icybee\Modules\Users\Hooks\get_user_id
	 */
	public function login()
	{
		global $core;

		if (!$this->uid)
		{
			throw new \Exception('Guest users cannot login.');
		}

		$core->user = $this;
		$core->user_id = $this->uid;
		$core->session->regenerate_id(true);
		$core->session->regenerate_token();
		$core->session->users['user_id'] = $this->uid;

		return true;
	}

	/**
	 * Log the user out.
	 *
	 * The following things happen when the user is logged out:
	 *
	 * - The `$core->user` property is unset.
	 * - The `$core->user_id` property is unset.
	 * - The `$core->session->users['user_id']` property is unset.
	 */
	public function logout()
	{
		global $core;

		unset($core->user);
		unset($core->user_id);
		unset($core->session->users['user_id']);
	}

	/**
	 * Returns the CSS class names of the node.
	 *
	 * @return array[string]mixed
	 */
	protected function get_css_class_names()
	{
		return [

			'type' => 'user',
			'id' => ($this->uid && !$this->is_guest) ? 'user-id-' . $this->uid : null,
			'username' => ($this->username && !$this->is_guest) ? 'user-' . $this->username : null,
			'constructor' => 'constructor-' . \ICanBoogie\normalize($this->constructor),
			'is-admin' => $this->is_admin,
			'is-guest' => $this->is_guest,
			'is-logged' => !$this->is_guest

		];
	}
}

/**
 * Implements the`logged_at` property.
 *
 * @property \ICanBoogie\DateTime $logged_at The date and time at which the user was logged.
 */
trait LoggedAtProperty
{
	/**
	 * The date and time at which the user was logged.
	 *
	 * @var mixed
	 */
	private $logged_at;

	/**
	 * Returns the date and time at which the user was logged.
	 *
	 * @return \ICanBoogie\DateTime
	 */
	protected function get_logged_at()
	{
		return DateTimePropertySupport::datetime_get($this->logged_at);
	}

	/**
	 * Sets the date and time at which the user was logged.
	 *
	 * @param mixed $datetime
	 */
	protected function set_logged_at($datetime)
	{
		DateTimePropertySupport::datetime_set($this->logged_at, $datetime);
	}
}