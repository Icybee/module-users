<?php

namespace Icybee\Modules\Users;

use ICanBoogie\Updater\AssertionFailed;
use ICanBoogie\Updater\Update;

/**
 * - Rename table `user_users` as `users`.
 *
 * @module users
 */
class Update20120101 extends Update
{
	public function update_table_users()
	{
		$db = $this->app->db;

		if (!$db->table_exists('user_users'))
		{
			throw new AssertionFailed('assert_table_exists', 'user_users');
		}

		if ($db->table_exists('users'))
		{
			throw new AssertionFailed('assert_not_table_exists', 'users');
		}

		$db("RENAME TABLE `{prefix}user_users` TO `users`");
	}

	public function update_constructor_type()
	{
		$db = $this->app->db;
		$db("UPDATE `{prefix}users` SET constructor = 'users' WHERE constructor = 'user.users'");
	}
}

/**
 * - Rename `password` column as `password_hash`.
 *
 * @module users
 */
class Update20130101 extends Update
{
	public function update_column_password_hash()
	{
		$this->module->model
		->assert_has_column('password')
		->rename_column('password', 'password_hash');
	}
}

/**
 * - Alter the column `password_hash` to suit the requirements of the Password API.
 * - Rename the column `created` as `created_at`.
 *
 * @module users
 */
class Update20131021 extends Update
{
	public function update_column_password_hash()
	{
		$this->module->model
		->assert_has_column('password_hash')
		->assert_not_column_has_size('password_hash', 255)
		->alter_column('password_hash');
	}

	public function update_column_created()
	{
		$this->module->model
		->assert_has_column('created')
		->rename_column('created', 'created_at');
	}
}

/**
 * @module users
 */
class Update20131022 extends Update
{
	/**
	 * Rename the column `display` as `name_as`.
	 */
	public function update_column_display()
	{
		$this->module->model
		->assert_has_column('display')
		->rename_column('display', 'name_as');
	}

	/**
	 * Create column `nickname`.
	 */
	public function update_column_nickname()
	{
		$this->module->model
		->assert_not_has_column('nickname')
		->create_column('nickname');
	}

	/**
	 * Rename column `lastconnection` as `logged_at`.
	 */
	public function update_column_lastconnection()
	{
		$this->module->model
		->assert_has_column('lastconnection')
		->rename_column('lastconnection', 'logged_at');
	}
}
