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

use function ICanBoogie\app;

class HooksTest extends \PHPUnit_Framework_TestCase
{
	static private $app;

	static public function setupBeforeClass()
	{
		/* @var $app \ICanBoogie\Application */

		self::$app = $app = app();

		$app->models['users']->truncate();

		User::from([

			'username' => 'admin',
			'email' => 'admin@example.com',
			'timezone' => 'Europe/Paris',

		])->save();

		User::from([

			'username' => 'user',
			'email' => 'user@example.com',
			'timezone' => 'Europe/Paris',

		])->save();
	}

	public function test_get_user()
	{
		self::$app->user_id = 1;
		$this->assertInstanceOf(User::class, self::$app->user);
		$this->assertTrue(self::$app->user->is_admin);
	}

	public function test_get_user_permission_resolver()
	{
		$this->assertInstanceOf(PermissionResolver::class, self::$app->user_permission_resolver);
	}

	public function test_get_user_ownership_resolver()
	{
		$this->assertInstanceOf(OwnershipResolver::class, self::$app->user_ownership_resolver);
	}
}
