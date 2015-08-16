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

class OwnershipResolverListTest extends \PHPUnit_Framework_TestCase
{
	static private $admin_user;
	static private $simple_user;
	static private $guest_user;
	static private $resolver;

	static public function setupBeforeClass()
	{
		self::$admin_user = User::from([

			'uid' => 1

		]);

		self::$simple_user = User::from([

			'uid' => 2

		]);

		self::$guest_user = new User;

		self::$resolver = new OwnershipResolver([

			'users' => Hooks::class . '::resolve_user_ownership'

		]);
	}

	public function test_with_no_resolver()
	{
		$resolver = new OwnershipResolver;

		$record = ActiveRecord::from([ 'uid' => 0 ], [ 'dummy' ]);
		$this->assertEmpty($resolver(self::$admin_user, $record));
		$this->assertEmpty($resolver(self::$simple_user, $record));
		$this->assertEmpty($resolver(self::$guest_user, $record));
	}

	public function test_with_our_resolver()
	{
		$resolver = self::$resolver;

		$record = ActiveRecord::from([ 'uid' => 0 ], [ 'dummy' ]);
		$this->assertNotEmpty($resolver(self::$admin_user, $record));
		$this->assertEmpty($resolver(self::$simple_user, $record));
		$this->assertEmpty($resolver(self::$guest_user, $record));

		$record = ActiveRecord::from([ 'uid' => 1 ], [ 'dummy' ]);
		$this->assertNotEmpty($resolver(self::$admin_user, $record));
		$this->assertEmpty($resolver(self::$simple_user, $record));
		$this->assertEmpty($resolver(self::$guest_user, $record));

		$record = ActiveRecord::from([ 'uid' => 2 ], [ 'dummy' ]);
		$this->assertNotEmpty($resolver(self::$admin_user, $record));
		$this->assertNotEmpty($resolver(self::$simple_user, $record));
		$this->assertEmpty($resolver(self::$guest_user, $record));

		$record = ActiveRecord::from([ 'uid' => 123 ], [ 'dummy' ]);
		$this->assertNotEmpty($resolver(self::$admin_user, $record));
		$this->assertEmpty($resolver(self::$simple_user, $record));
		$this->assertEmpty($resolver(self::$guest_user, $record));
	}

	public function test_syntesize_config()
	{
		$fragments = [

			[
				'ownership_resolver_list' => [

					'user_1' => 'dummy_1',
					'user_2' => 'dummy_2'

				]
			],

			[
				'ownership_resolver_list' => [

					'user_0' => [ 'dummy_0', 'weight' => -1 ],
					'user_3' => [ 'dummy_3' ],

				]
			],

			[
				'ownership_resolver_list' => [

					'user_m10' => [ 'dummy_m10', 'weight' => -10 ],
					'user_p10' => [ 'dummy_p10', 'weight' => 10 ],
					'user_5' => [ 'dummy_5' ]

				]
			],

			[
				'ownership_resolver_list' => [

					'user_bottom' => [ 'dummy_bottom', 'weight' => 'bottom' ],
					'user_top' => [ 'dummy_top', 'weight' => 'top' ],
					'user_super_bottom' => [ 'dummy_super_bottom', 'weight' => 'bottom' ],
					'user_super_top' => [ 'dummy_super_top', 'weight' => 'top' ],
					'user_before_5' => [ 'dummy_before_5', 'weight' => 'before:user_5' ],
					'user_after_5' => [ 'dummy_after_5', 'weight' => 'after:user_5' ]

				]
			]

		];

		$config = OwnershipResolver::synthesize_config($fragments);

		$this->assertSame([

			'user_super_top' => 'dummy_super_top',
			'user_top' => 'dummy_top',
			'user_m10' => 'dummy_m10',
			'user_0' => 'dummy_0',
			'user_1' => 'dummy_1',
			'user_2' => 'dummy_2',
			'user_3' => 'dummy_3',
			'user_before_5' => 'dummy_before_5',
			'user_5' => 'dummy_5',
			'user_after_5' => 'dummy_after_5',
			'user_p10' => 'dummy_p10',
			'user_bottom' => 'dummy_bottom',
			'user_super_bottom' => 'dummy_super_bottom'

		], $config);
	}
}
