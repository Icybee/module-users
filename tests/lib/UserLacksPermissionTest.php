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
 * @group permission
 */
class UserLacksPermissionTest extends \PHPUnit_Framework_TestCase
{
	public function test_exception()
	{
		$user = new User;
		$permission = uniqid();
		$resource = uniqid();
		$exception = new UserLacksPermission($user, $permission, $resource);

		$this->assertSame($user, $exception->user);
		$this->assertSame($permission, $exception->permission);
		$this->assertSame($resource, $exception->resource);
		$this->assertSame(Status::FORBIDDEN, $exception->getCode());
	}
}
