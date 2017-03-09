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

use ICanBoogie\Accessor\AccessorTrait;
use ICanBoogie\HTTP\Status;

/**
 * Exception thrown when the user lacks ownership of a resource.
 *
 * @property-read User $user
 * @property-read mixed $resource
 */
class UserLacksOwnership extends \Exception
{
	use AccessorTrait;

	const DEFAULT_MESSAGE = 'User lacks ownership of the resource.';

	/**
	 * @var User
	 */
	private $user;

	/**
	 * @return User
	 */
	protected function get_user()
	{
		return $this->user;
	}

	/**
	 * @var mixed
	 */
	private $resource;

	/**
	 * @return mixed
	 */
	protected function get_resource()
	{
		return $this->resource;
	}

	/**
	 * @param User $user
	 * @param mixed $resource
	 * @param string $message
	 * @param \Exception|null $previous
	 */
	public function __construct(User $user, $resource, $message = self::DEFAULT_MESSAGE, \Exception $previous = null)
	{
		$this->user = $user;
		$this->resource = $resource;

		parent::__construct($message, Status::FORBIDDEN, $previous);
	}
}
