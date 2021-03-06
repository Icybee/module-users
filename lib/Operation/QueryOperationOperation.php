<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Users\Operation;

class QueryOperationOperation extends \Icybee\Operation\Module\QueryOperation
{
	protected function query_activate()
	{
		$keys = $this->request['keys'];

		return [

			'params' => [

				'keys' => $keys

			]

		];
	}

	protected function query_deactivate()
	{
		$keys = $this->request['keys'];

		return [

			'params' => [

				'keys' => $keys

			]

		];
	}

	protected function query_send_password()
	{
		return [

			'params' => [

				'keys' => $this->request['keys']

			]

		];
	}
}
