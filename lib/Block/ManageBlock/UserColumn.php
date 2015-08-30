<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Users\Block\ManageBlock;

use ICanBoogie\ActiveRecord\Model;
use ICanBoogie\ActiveRecord\Query;
use ICanBoogie\ActiveRecord\RecordNotFound;
use Icybee\Block\ManageBlock;
use Icybee\Block\ManageBlock\Column;
use Icybee\Block\ManageBlock\FilterDecorator;
use Icybee\Modules\Users\User;

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
