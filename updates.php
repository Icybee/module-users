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