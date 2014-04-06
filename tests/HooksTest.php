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

class HooksTest extends \PHPUnit_Framework_TestCase
{
	static private $core;

	static public function setupBeforeClass()
	{
		global $core;

		self::$core = $core;

		$core->models['users']->truncate();

		User::from([

			'username' => 'admin',
			'email' => 'admin@example.com'

		])->save();

		User::from([

			'username' => 'user',
			'email' => 'user@example.com'

		])->save();
	}

	public function test_get_user()
	{
		self::$core->user_id = 1;
		$this->assertInstanceOf(__NAMESPACE__ . '\User', self::$core->user);
		$this->assertTrue(self::$core->user->is_admin);
	}

	public function test_get_user_permission_resolver()
	{
		$this->assertInstanceOf(__NAMESPACE__ . '\PermissionResolver', self::$core->user_permission_resolver);
	}

	public function test_get_user_ownership_resolver()
	{
		$this->assertInstanceOf(__NAMESPACE__ . '\OwnershipResolver', self::$core->user_ownership_resolver);
	}
}