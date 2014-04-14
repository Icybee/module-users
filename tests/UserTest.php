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

use ICanBoogie\DateTime;

// require_once 'support.php';

class UserTest extends \PHPUnit_Framework_TestCase
{
	public function test_get_is_admin()
	{
		$user = new User;
		$user->uid = 1;
		$this->assertTrue($user->is_admin);
		$user->uid = 2;
		$this->assertFalse($user->is_admin);
		$this->assertObjectNotHasAttribute('is_admin', $user);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_is_admin()
	{
		$user = new User;
		$user->is_admin = null;
	}

	public function test_get_is_guest()
	{
		$user = new User;
		$this->assertTrue($user->is_guest);
		$user->uid = 1;
		$this->assertFalse($user->is_guest);
		$user->uid = 2;
		$this->assertFalse($user->is_guest);
		$this->assertObjectNotHasAttribute('is_guest', $user);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_is_guest()
	{
		$user = new User;
		$user->is_guest = null;
	}

	public function test_logged_at()
	{
		$user = new User;
		$d = $user->logged_at;
		$this->assertInstanceOf('ICanBoogie\DateTime', $d);
		$this->assertTrue($d->is_empty);
		$this->assertEquals('UTC', $d->zone->name);
		$this->assertEquals('0000-00-00 00:00:00', $d->as_db);

		$user->logged_at = '2013-03-07 18:30:45';
		$d = $user->logged_at;
		$this->assertInstanceOf('ICanBoogie\DateTime', $d);
		$this->assertFalse($d->is_empty);
		$this->assertEquals('UTC', $d->zone->name);
		$this->assertEquals('2013-03-07 18:30:45', $d->as_db);

		$user->logged_at = new DateTime('2013-03-07 18:30:45', 'utc');
		$d = $user->logged_at;
		$this->assertInstanceOf('ICanBoogie\DateTime', $d);
		$this->assertFalse($d->is_empty);
		$this->assertEquals('UTC', $d->zone->name);
		$this->assertEquals('2013-03-07 18:30:45', $d->as_db);

		$user->logged_at = null;
		$this->assertInstanceOf('ICanBoogie\DateTime', $d);

		$user->logged_at = DateTime::now();
		$properties = $user->__sleep();
		$this->assertArrayHasKey('logged_at', $properties);
		$array = $user->to_array();
		$this->assertArrayHasKey('logged_at', $array);
	}

	public function test_created_at()
	{
		$user = new User;
		$d = $user->created_at;
		$this->assertInstanceOf('ICanBoogie\DateTime', $d);
		$this->assertTrue($d->is_empty);
		$this->assertEquals('UTC', $d->zone->name);
		$this->assertEquals('0000-00-00 00:00:00', $d->as_db);

		$user->created_at = '2013-03-07 18:30:45';
		$d = $user->created_at;
		$this->assertInstanceOf('ICanBoogie\DateTime', $d);
		$this->assertFalse($d->is_empty);
		$this->assertEquals('UTC', $d->zone->name);
		$this->assertEquals('2013-03-07 18:30:45', $d->as_db);

		$user->created_at = new DateTime('2013-03-07 18:30:45', 'utc');
		$d = $user->created_at;
		$this->assertInstanceOf('ICanBoogie\DateTime', $d);
		$this->assertFalse($d->is_empty);
		$this->assertEquals('UTC', $d->zone->name);
		$this->assertEquals('2013-03-07 18:30:45', $d->as_db);

		$user->created_at = null;
		$this->assertInstanceOf('ICanBoogie\DateTime', $d);

		$user->created_at = DateTime::now();
		$properties = $user->__sleep();
		$this->assertArrayHasKey('created_at', $properties);
		$array = $user->to_array();
		$this->assertArrayHasKey('created_at', $array);
	}

	public function test_created_at_save_with_record()
	{
		$user = User::from([

			'username' => 'person',
			'email' => 'person@example.tld'

		]);

		$user->save();

		$this->assertTrue(DateTime::now() == $user->created_at);

		$user->delete();
	}

	public function test_created_at_save_with_model()
	{
		$user = User::from([

			'username' => 'person',
			'email' => 'person@example.tld'

		]);

		$uid = $user->model->save(array_diff_key($user->to_array(), [ 'uid' => true ]));
		$user = $user->model[$uid];

		$this->assertFalse($user->created_at->is_empty);

		$user->delete();
	}


	public function test_get_name()
	{
		$user = new User;
		$this->assertEquals('', $user->name);

		$user->username = 'admin';
		$this->assertEquals('admin', $user->name);
		$user->name_as = User::NAME_AS_FIRSTNAME;
		$this->assertEquals('admin', $user->name);
		$user->name_as = User::NAME_AS_FIRSTNAME_LASTNAME;
		$this->assertEquals('admin', $user->name);
		$user->name_as = User::NAME_AS_LASTNAME;
		$this->assertEquals('admin', $user->name);
		$user->name_as = User::NAME_AS_LASTNAME_FIRSTNAME;
		$this->assertEquals('admin', $user->name);
		$user->name_as = User::NAME_AS_NICKNAME;
		$this->assertEquals('admin', $user->name);
		$user->name_as = User::NAME_AS_USERNAME;
		$this->assertEquals('admin', $user->name);

		$user->nickname = 'olvlvl';
		$this->assertEquals('admin', $user->name);
		$user->name_as = User::NAME_AS_FIRSTNAME;
		$this->assertEquals('admin', $user->name);
		$user->name_as = User::NAME_AS_FIRSTNAME_LASTNAME;
		$this->assertEquals('admin', $user->name);
		$user->name_as = User::NAME_AS_LASTNAME;
		$this->assertEquals('admin', $user->name);
		$user->name_as = User::NAME_AS_LASTNAME_FIRSTNAME;
		$this->assertEquals('admin', $user->name);
		$user->name_as = User::NAME_AS_NICKNAME;
		$this->assertEquals('olvlvl', $user->name);
		$user->name_as = User::NAME_AS_USERNAME;
		$this->assertEquals('admin', $user->name);

		$user->firstname = 'Olivier';
		$user->lastname = 'Laviale';
		$this->assertEquals('admin', $user->name);
		$user->name_as = User::NAME_AS_FIRSTNAME;
		$this->assertEquals('Olivier', $user->name);
		$user->name_as = User::NAME_AS_FIRSTNAME_LASTNAME;
		$this->assertEquals('Olivier Laviale', $user->name);
		$user->name_as = User::NAME_AS_LASTNAME;
		$this->assertEquals('Laviale', $user->name);
		$user->name_as = User::NAME_AS_LASTNAME_FIRSTNAME;
		$this->assertEquals('Laviale Olivier', $user->name);
		$user->name_as = User::NAME_AS_NICKNAME;
		$this->assertEquals('olvlvl', $user->name);
		$user->name_as = User::NAME_AS_USERNAME;
		$this->assertEquals('admin', $user->name);

		$this->assertObjectNotHasAttribute('name', $user);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_name()
	{
		$user = new User;
		$user->name = null;
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotReadable
	 */
	public function test_get_password_hash()
	{
		$user = new User;
		$user->password_hash;
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_password_hash()
	{
		$user = new User;
		$user->password_hash = null;
	}

	public function test_get_has_legacy_password_hash()
	{
		$this->assertNull(User::from()->has_legacy_password_hash);
		$this->assertTrue(User::from([ 'password_hash' => sha1('password') ])->has_legacy_password_hash);
		$this->assertFalse(User::from([ 'password_hash' => User::hash_password('password') ])->has_legacy_password_hash);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_has_legacy_password_hash()
	{
		$user = new User;
		$user->has_legacy_password_hash = null;
	}

	public function testPasswordMustBeExported()
	{
		$user = new User;
		$user->password = '123';

		$this->assertArrayHasKey('password', $user->to_array());
	}

	public function testEmptyPasswordMustNotBeExported()
	{
		$user = new User;
		$user->password = null;

		$this->assertArrayNotHasKey('password', $user->to_array());
	}

	public function test_password()
	{
		$user = new User;
		$user->username = "example";
		$user->email = "example@example.com";
		$user->password = 'P4SSW0RD';

		$this->assertTrue($user->verify_password('P4SSW0RD'));
	}

	public function test_css_class()
	{
		$user = new User;
		$user->uid = 1;
		$user->username = 'olvlvl';

		$this->assertEquals("user user-id-1 user-olvlvl constructor-users is-admin is-logged", $user->css_class);

		unset($user->css_class);
		unset($user->css_class_names);

		$user->uid = 0;
		$this->assertEquals("user constructor-users is-guest", $user->css_class);
	}
}