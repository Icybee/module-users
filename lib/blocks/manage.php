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

use ICanBoogie\ActiveRecord\Query;
use Brickrouge\Document;
use Icybee\ManageBlock\DateTimeColumn;

class ManageBlock extends \Icybee\ManageBlock
{
	static protected function add_assets(Document $document)
	{
		parent::add_assets($document);

		$document->css->add(DIR . 'public/admin.css');
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
	 * - `created_at`: An instance of {@link \Icybee\ManageBlock\DateTimeColumn}.
	 * - `logged_at`: An instance of {@link ManageBlock\LoggedAtColumn}.
	 * - `is_activated`: An instance of {@link ManageBlock\IsActivatedColumn}.
	 *
	 * @inheritdoc
	 */
	protected function get_available_columns()
	{
		return array_merge(parent::get_available_columns(), [

			User::USERNAME => ManageBlock::class . '\UsernameColumn',
			User::EMAIL => ManageBlock::class . '\EmailColumn',
			'roles' => ManageBlock::class . '\RolesColumn',
			User::CREATED_AT => DateTimeColumn::class,
			User::LOGGED_AT => ManageBlock::class . '\LoggedAtColumn',
			User::IS_ACTIVATED => ManageBlock::class . '\IsActivatedColumn'

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

namespace Icybee\Modules\Users\ManageBlock;

use ICanBoogie\ActiveRecord\Model;
use ICanBoogie\ActiveRecord\Query;
use ICanBoogie\ActiveRecord\RecordNotFound;

use Brickrouge\Element;

use Icybee\ManageBlock;
use Icybee\ManageBlock\BooleanColumn;
use Icybee\ManageBlock\Column;
use Icybee\ManageBlock\DateTimeColumn;
use Icybee\ManageBlock\FilterDecorator;
use Icybee\Modules\Users\User;

/**
 * Representation of the `username` column.
 */
class UsernameColumn extends Column
{
	/**
	 * @param User $record
	 *
	 * @inheritdoc
	 */
	public function render_cell($record)
	{
		$label = $record->username;
		$name = $record->name;

		if ($label != $name)
		{
			$label .= ' <small>(' . $name . ')</small>';
		}

		return new Element('a', [

			Element::INNER_HTML => $label,

			'class' => 'edit',
			'href' => \ICanBoogie\Routing\contextualize("/admin/{$record->constructor}/{$record->uid}/edit"),
			'title' => $this->t('manage.edit')

		]);
	}
}

/**
 * Representation of the `email` column.
 */
class EmailColumn extends Column
{
	/**
	 * @param User $record
	 *
	 * @inheritdoc
	 */
	public function render_cell($record)
	{
		$email = $record->email;

		return '<a href="mailto:' . $email . '" title="' . $this->manager->t('Send an E-mail') . '">' . $email . '</a>';
	}
}

/**
 * Representation of the `roles` column.
 */
class RolesColumn extends Column
{
	/**
	 * @param User $record
	 *
	 * @inheritdoc
	 */
	public function render_cell($record)
	{
		if ($record->uid == 1)
		{
			return '<em>Admin</em>';
		}

		if (!$record->roles)
		{
			return null;
		}

		$label = '';

		foreach ($record->roles as $role)
		{
			if ($role->rid == 2)
			{
				continue;
			}

			$label .= ', ' . $role->name;
		}

		return substr($label, 2);
	}
}

/**
 * Representation of the `logged_at` column.
 */
class LoggedAtColumn extends DateTimeColumn
{
	/**
	 * @param User $record
	 *
	 * @inheritdoc
	 */
	public function render_cell($record)
	{
		$logged_at = $record->logged_at;

		if ($logged_at->is_empty)
		{
			return '<em class="small">' . $this->manager->t("Never connected") . '</em>';
		}

		return parent::render_cell($record);
	}
}

/**
 * Representation of the `is_activated` column.
 */
class IsActivatedColumn extends BooleanColumn
{
	public function __construct(ManageBlock $manager, $id, array $options = [])
	{
		parent::__construct($manager, $id, $options + [

			'title' => null,
			'class' => 'cell-fitted',
			'filters' => [

				'options' => [

					'=1' => 'Activated',
					'=0' => 'Deactivated'

				]

			]

		]);
	}

	/**
	 * @param User $record
	 *
	 * @inheritdoc
	 */
	public function render_cell($record)
	{
		if ($record->is_admin)
		{
			return null;
		}

		return parent::render_cell($record);
	}
}

/**
 * Representation of a _user_ column.
 *
 * This column can be used to represent the users associated to records.
 */
class UserColumn extends Column
{
	/**
	 * The users associated with the records, indexed by their identifier.
	 *
	 * @var User[]
	 */
	private $user_cache;

	/**
	 * The names of the users associated with the records, indexed by their identifier.
	 *
	 * @var array
	 *
	 * @see resolved_user_names()
	 */
	private $resolved_user_names;

	/**
	 * Initializes the {@link $resolved_user_names} property.
	 *
	 * @inheritdoc
	 */
	public function __construct(ManageBlock $manager, $id, array $options = [])
	{
		parent::__construct($manager, $id, $options + [

			'title' => 'User',
			'orderable' => true

		]);

		$this->resolved_user_names = $this->resolve_user_names($manager->model);
	}

	private function resolve_user_names(Model $model)
	{
		$query = $model->select("DISTINCT `{$this->id}`");

		if ($model->has_scope('own'))
		{
			$query = $query->own;
		}

		if ($model->has_scope('similar_site'))
		{
			$query = $query->similar_site;
		}

		$users_keys = $query->all(\PDO::FETCH_COLUMN);

		if (count($users_keys) < 2)
		{
			return null;
		}

		return \ICanBoogie\app()
			->models['users']
			->select('uid, IF((firstname != "" AND lastname != ""), CONCAT_WS(" ", firstname, lastname), username) name')
			->filter_by_uid($users_keys)
			->order('name')
			->pairs;
	}

	public function alter_query_with_filter(Query $query, $filter_value)
	{
		$query = parent::alter_query_with_filter($query, $filter_value);

		if ($filter_value)
		{
			$query->and([ $this->id => $filter_value ]);
		}

		return $query;
	}

	public function alter_query_with_order(Query $query, $order_direction)
	{
		$keys = array_keys($this->resolved_user_names);

		if ($order_direction < 0)
		{
			$keys = array_reverse($keys);
		}

		return $query->order($this->id, $keys);
	}

	/**
	 * Includes the users associated with the records.
	 *
	 * @param User[] $records
	 *
	 * @inheritdoc
	 */
	public function alter_records(array $records)
	{
		$records = parent::alter_records($records);
		$keys = [];

		foreach ($records as $record)
		{
			$keys[] = $record->{ $this->id };
		}

		if ($keys)
		{
			$keys = array_unique($keys, SORT_NUMERIC);

			try
			{
				$this->user_cache = \ICanBoogie\app()->models['users']->find($keys);
			}
			catch (RecordNotFound $e)
			{
				$this->user_cache = $e->records;
			}
		}

		return $records;
	}

	public function get_options()
	{
		if (!$this->resolved_user_names)
		{
			return null;
		}

		$options = [];

		foreach ($this->resolved_user_names as $uid => $name)
		{
			$options["?uid=" . urlencode($uid)] = $name;
		}

		return $options;
	}

	/**
	 * @param User $record
	 *
	 * @inheritdoc
	 */
	public function render_cell($record)
	{
		$uid = $record->{ $this->id };
		$user = $uid ? $this->user_cache[$uid] : null;

		if (!$user)
		{
			return <<<EOT
<div class="alert alert-error undismissable">Undefined user: {$uid}</div>
EOT;
		}

		return new FilterDecorator($record, $this->id, $this->is_filtering, $user ? $user->name : '');
	}
}
