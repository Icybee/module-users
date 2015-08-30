<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Users\Block;

use ICanBoogie\ActiveRecord\Query;
use Brickrouge\Document;
use Icybee\Block\ManageBlock\DateTimeColumn;
use Icybee\Modules\Users\User;
use Icybee\Modules\Users\Module;

class ManageBlock extends \Icybee\Block\ManageBlock
{
	static protected function add_assets(Document $document)
	{
		parent::add_assets($document);

		$document->css->add(\Icybee\Modules\Users\DIR . 'public/admin.css');
	}

	public function __construct($module, array $attributes = [])
	{
		parent::__construct($module, $attributes + [

			self::T_COLUMNS_ORDER => [ User::USERNAME, User::IS_ACTIVATED, User::EMAIL, 'roles', User::CREATED_AT, User::LOGGED_AT ],
			self::T_ORDER_BY => [ 'created_at', 'desc' ]

		]);
	}

	/**
	 * Adds the following columns:
	 *
	 * - `username`: An instance of {@link ManageBlock\UsernameColumn}.
	 * - `email`: An instance of {@link ManageBlock\EmailColumn}.
	 * - `roles`: An instance of {@link ManageBlock\RolesColumn}.
	 * - `created_at`: An instance of {@link \Icybee\Block\ManageBlock\DateTimeColumn}.
	 * - `logged_at`: An instance of {@link ManageBlock\LoggedAtColumn}.
	 * - `is_activated`: An instance of {@link ManageBlock\IsActivatedColumn}.
	 *
	 * @inheritdoc
	 */
	protected function get_available_columns()
	{
		return array_merge(parent::get_available_columns(), [

			User::USERNAME => ManageBlock\UsernameColumn::class,
			User::EMAIL => ManageBlock\EmailColumn::class,
			'roles' => ManageBlock\RolesColumn::class,
			User::CREATED_AT => DateTimeColumn::class,
			User::LOGGED_AT => ManageBlock\LoggedAtColumn::class,
			User::IS_ACTIVATED => ManageBlock\IsActivatedColumn::class

		]);
	}

	/**
	 * Filters records according to the constructor (the module that created the record).
	 *
	 * @inheritdoc
	 */
	protected function alter_query(Query $query, array $filters)
	{
		return parent::alter_query($query, $filters)
		->filter_by_constructor($this->module->id);
	}

	/**
	 * Adds the following jobs:
	 *
	 * - `activate`: Activate the selected records.
	 * - `deactivate`: Deactivate the selected records.
	 *
	 * @inheritdoc
	 */
	protected function get_available_jobs()
	{
		return array_merge(parent::get_available_jobs(), [

			Module::OPERATION_ACTIVATE => $this->t('activate.operation.title'),
			Module::OPERATION_DEACTIVATE => $this->t('deactivate.operation.title')

		]);
	}
}
