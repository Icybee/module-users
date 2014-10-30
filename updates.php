<?php

namespace Icybee\Modules\Users;

/**
 * - Alter the column `password_hash` to suit the requirements of the Password API.
 * - Rename the column `created` as `created_at`.
 *
 * @module users
 */
class Update20131021 extends \ICanBoogie\Updater\Update
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
class Update20131022 extends \ICanBoogie\Updater\Update
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
