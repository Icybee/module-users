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

use ICanBoogie\ActiveRecord;
use ICanBoogie\ActiveRecord\CreatedAtProperty;
use ICanBoogie\ActiveRecord\RecordNotFound;

use Brickrouge\AlterCSSClassNamesEvent;
use Brickrouge\CSSClassNames;
use Brickrouge\CSSClassNamesProperty;

use Icybee\Modules\Users\Roles\Role;
use Icybee\Binding\Core\PrototypedBindings as IcybeeBindings;
use Icybee\Modules\Registry\Binding\UserBindings as RegistryBindings;

/**
 * A user.
 *
 * @property-read UserModel $model
 * @property-read \ICanBoogie\Core|Binding\CoreBindings $app
 *
 * @property-read string $name The formatted name of the user.
 * @property-read boolean $is_admin true if the user is admin, false otherwise.
 * @property-read boolean $is_guest true if the user is a guest, false otherwise.
 * @property-read Role $role
 *
 * @property-read string $password_hash The password hash.
 * @property-read bool|null $has_legacy_password_hash Whether the password hash is a legacy hash.
 * {@link User::get_has_legacy_password_hash()}.
 */
class User extends ActiveRecord implements CSSClassNames
{
	use IcybeeBindings, RegistryBindings;
	use CreatedAtProperty, LoggedAtProperty, CSSClassNamesProperty;
	use PasswordTrait;

	const MODEL_ID = 'users';

	const UID = 'uid';
	const EMAIL = 'email';
	const PASSWORD = 'password';
	const PASSWORD_HASH = 'password_hash';
	const PASSWORD_VERIFY = 'password-verify';
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
	 * @inheritdoc
	 *
	 * The method takes care of setting the {@link password_hash} property which is not
	 * settable otherwise.
	 *
	 * @return static
	 */
	static public function from($properties = null, array $construct_args = [], $class_name = null)
	{
		if (!is_array($properties) || !array_key_exists('password_hash', $properties))
		{
			return parent::from($properties, $construct_args, $class_name);
		}

		$password_hash = $properties['password_hash'];
		unset($properties['password_hash']);
		$instance = parent::from($properties, $construct_args);
		$instance->password_hash = $password_hash;

		return $instance;
	}

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
	 * Preferred format to create the value of the {@link $name} property.
	 *
	 * @var string
	 */
	public $name_as = self::NAME_AS_USERNAME;

	/**
	 * Preferred language of the user.
	 *
	 * @var string
	 */
	public $language = '';

	/**
	 * Preferred timezone of the user.
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
	 * If empty, the {@link $constructor} property is initialized with the model identifier.
	 *
	 * @inheritdoc
	 */
	public function __construct($model = null)
	{
		parent::__construct($model);

		if (empty($this->constructor))
		{
			$this->constructor = $this->model_id;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function __get($property)
	{
		$value = parent::__get($property);

		if ($property === 'css_class_names')
		{
			new AlterCSSClassNamesEvent($this, $value);
		}

		return $value;
	}

	/**
	 * @inheritdoc
	 */
	public function create_validation_rules()
	{
		return [

			'username' => 'required',
			'email' => 'required|email|unique',
			'timezone' => 'timezone'

		];
	}

	/**
	 * @inheritdoc
	 */
	public function save(array $options = [])
	{
		if ($this->get_created_at()->is_empty)
		{
			$this->set_created_at('now');
		}

		return parent::save($options);
	}

	/**
	 * Adds the {@link $password_hash} property.
	 */
	public function to_array()
	{
		$array = parent::to_array();

		if ($this->password_hash)
		{
			$array['password_hash'] = $this->password_hash;
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
	 * @return Role
	 */
	protected function lazy_get_role()
	{
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

		$role = new Role;
		$role->perms = $permissions;

		if ($name)
		{
			$role->name = substr($name, 2);
		}

		return $role;
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
		if ($this->is_admin)
		{
			return [];
		}

		return $this->model->models['users/has_many_sites']
		->select('site_id')
		->filter_by_uid($this->uid)
		->all(\PDO::FETCH_COLUMN);
	}

	/**
	 * Checks if the user has a given permission.
	 *
	 * @param string|int $permission
	 * @param mixed $target
	 *
	 * @return mixed
	 */
	public function has_permission($permission, $target = null)
	{
		if ($this->is_admin)
		{
			return Module::PERMISSION_ADMINISTER;
		}

		return $this->app->check_user_permission($this, $permission, $target);
	}

	/**
	 * Checks if the user has the ownership of an entry.
	 *
	 * If the ownership information is missing from the entry (the 'uid' property is null), the user
	 * must have the ADMINISTER level to be considered the owner.
	 *
	 * @param ActiveRecord $record
	 *
	 * @return boolean
	 */
	public function has_ownership($record)
	{
		return $this->app->check_user_ownership($this, $record);
	}

	/**
	 * Logs the user in.
	 *
	 * A user is logged in by setting its id in the `user_id` session key.
	 *
	 * Note: The method does *not* check user authentication!
	 *
	 * The following things happen when the user is logged in:
	 *
	 * - The `$app->user` property is set to the user.
	 * - The `$app->user_id` property is set to the user id.
	 * - The session id is regenerated and the user id, ip and user agent are stored in the session.
	 *
	 * @throws \Exception in attempt to log in a guest user.
	 *
	 * @see \Icybee\Modules\Users\Hooks\get_user_id
	 */
	public function login()
	{
		if (!$this->uid)
		{
			throw new \Exception('Guest users cannot login.');
		}

		$app = $this->app;
		$app->user = $this;
		$app->user_id = $this->uid;
		$app->session->regenerate();
		$app->session['user_id'] = $this->uid;
	}

	/**
	 * Log the user out.
	 *
	 * The following things happen when the user is logged out:
	 *
	 * - The `$app->user` property is unset.
	 * - The `$app->user_id` property is unset.
	 * - The `user_id` session property is removed.
	 */
	public function logout()
	{
		$app = $this->app;
		$app->session->regenerate();

		unset($app->user);
		unset($app->user_id);
		unset($app->session['user_id']);
	}

	/**
	 * @inheritdoc
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
