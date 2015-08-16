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

use ICanBoogie\ActiveRecord\DateTimePropertySupport;
use ICanBoogie\DateTime;

/**
 * Implements the`logged_at` property.
 *
 * @property DateTime $logged_at The date and time at which the user was logged.
 */
trait LoggedAtProperty
{
	/**
	 * The date and time at which the user was logged.
	 *
	 * @var DateTime
	 */
	private $logged_at;

	/**
	 * Returns the date and time at which the user was logged.
	 *
	 * @return DateTime
	 */
	protected function get_logged_at()
	{
		return DateTimePropertySupport::get($this->logged_at);
	}

	/**
	 * Sets the date and time at which the user was logged.
	 *
	 * @param \DateTime|string|null $datetime
	 */
	protected function set_logged_at($datetime)
	{
		DateTimePropertySupport::set($this->logged_at, $datetime);
	}
}
