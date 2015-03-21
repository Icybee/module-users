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

class PermissionResolver implements \ArrayAccess, \IteratorAggregate, PermissionResolverInterface
{
	/**
	 * Synthesizes the `users_permission_resolver_list` config from `users` fragments.
	 *
	 * @param array $fragments
	 *
	 * @return array
	 */
	static public function synthesize_config(array $fragments)
	{
		$list = [];
		$weight = [];

		foreach ($fragments as $fragment)
		{
			if (empty($fragment['permission_resolver_list']))
			{
				continue;
			}

			foreach ($fragment['permission_resolver_list'] as $resolver_id => $resolver)
			{
				$resolver = ((array) $resolver) + [ 'weight' => 0 ];

				$list[$resolver_id] = $resolver[0];
				$weight[$resolver_id] = $resolver['weight'];
			}
		}

		return \ICanBoogie\sort_by_weight($list, function($v, $k) use($weight) {

			return $weight[$k];

		});
	}

	private $list = [];

	/**
	 * Initializes the permission resolver list.
	 *
	 * @param array $resolver_list A permission resolver list, such as one created by
	 * {@link autoconfig()}.
	 */
	public function __construct(array $resolver_list = [])
	{
		foreach ($resolver_list as $resolver_id => $resolver)
		{
			$this[$resolver_id] = $resolver;
		}
	}

	public function __invoke(User $user, $permission, $target = null)
	{
		$granted = false;

		foreach ($this->list as $resolver_id => &$resolver)
		{
			if (!is_callable($resolver))
			{
				$resolver = new $resolver;
			}

			$resolver_grant = call_user_func($resolver, $user, $permission, $target);

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

interface PermissionResolverInterface
{
	/**
	 * Resolves the permission of a user.
	 *
	 * @param User $user A user record.
	 * @param string $permission The permission to check.
	 * @param mixed $target A context for the permission
	 *
	 * @return boolean `true` if the user has the specified permission.
	 */
	public function __invoke(User $user, $permission, $target = null);
}
