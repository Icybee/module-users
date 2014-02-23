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

class OwnershipResolver implements \ArrayAccess, \IteratorAggregate, OwnershipResolverInterface
{
	/**
	 * Creates the resolved list from the `users` config.
	 *
	 * @param \ICanBoogie\Core $core
	 *
	 * @return array
	 */
	static public function autoconfig(\ICanBoogie\Core $core)
	{
		return $core->configs->synthesize('users_ownership_resolver_list', function(array $fragments) {

			$list = [];
			$weight = [];

			foreach ($fragments as $fragment)
			{
				if (empty($fragment['ownership_resolver_list']))
				{
					continue;
				}

				foreach ($fragment['ownership_resolver_list'] as $resolver_id => $resolver)
				{
					$resolver = ((array) $resolver) + [ 'weight' => 0 ];

					$list[$resolver_id] = $resolver[0];
					$weight[$resolver_id] = $resolver['weight'];
				}
			}

			\ICanBoogie\stable_sort($list, function($v, $k) use($weight) {

				return $weight[$k];

			});

			return $list;

		}, 'users');
	}

	private $list = [];

	/**
	 * Initializes the ownership resolver list.
	 *
	 * @param array $resolver_list A ownership resolver list, such as one created by
	 * {@link autoconfig()}.
	 */
	public function __construct(array $resolver_list=[])
	{
		foreach ($resolver_list as $resolver_id => $resolver)
		{
			$this[$resolver_id] = $resolver;
		}
	}

	public function __invoke(User $user, ActiveRecord $record)
	{
		$granted = false;

		foreach ($this->list as $resolver_id => &$resolver)
		{
			if (!is_callable($resolver))
			{
				$resolver = new $resolver;
			}

			$resolver_grant = call_user_func($resolver, $user, $record);

			if ($resolver_grant === null)
			{
				continue;
			}

			$granted = $resolver_grant;
		}

		return $granted;
	}

	public function getIterator()
	{
		return new \ArrayIterator($this->list);
	}

	public function offsetExists($resolver_id)
	{
		return isset($this->list[$resolver_id]);
	}

	public function offsetGet($resolver_id)
	{
		return $this->list[$resolver_id];
	}

	public function offsetSet($resolver_id, $resolver)
	{
		$this->list[$resolver_id] = $resolver;
	}

	public function offsetUnset($resolver_id)
	{
		unset($this->list[$resolver_id]);
	}
}

interface OwnershipResolverInterface
{
	/**
	 * Resolves the owneship of a user.
	 *
	 * @param User $user A user record.
	 * @param ActiveRecord $record A record.
	 *
	 * @return boolean `true` if the user has the ownership of the record.
	 */
	public function __invoke(User $user, ActiveRecord $record);
}