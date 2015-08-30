<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Users\Element;

use Brickrouge\Element;

use Icybee\Binding\PrototypedBindings;

/**
 * An element to pick a user.
 */
class PickUser extends Element
{
	use PrototypedBindings;

	/**
	 * The options of the element are created with {@link create_options()}.
	 *
	 * @inheritdoc
	 */
	public function __construct(array $attributes = [])
	{
		parent::__construct('select', $attributes + [

			Element::OPTIONS => [ null => '' ] + $this->create_options($attributes)

		]);
	}

	/**
	 * Creates element's options.
	 *
	 * @param array $attributes
	 *
	 * @return array
	 */
	protected function create_options(array $attributes)
	{
		return $this->app->models['users']
			->select('uid, username')
			->order('username')
			->pairs;
	}
}
