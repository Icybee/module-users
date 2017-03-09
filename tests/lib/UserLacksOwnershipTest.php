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

use ICanBoogie\HTTP\Status;

/**
 * @group ownership
 */
class UserLacksOwnershipTest extends \PHPUnit_Framework_TestCase
{
	public function test_exception()
	{
		$user = new User;
		$resource = uniqid();
		$exception = new UserLacksOwnership($user, $resource);

		$this->assertSame($user, $exception->user);
		$this->assertSame($resource, $exception->resource);
		$this->assertSame(Status::FORBIDDEN, $exception->getCode());
	}
}
