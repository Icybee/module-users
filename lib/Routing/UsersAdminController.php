<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Users\Routing;

use Icybee\Modules\Users\User;
use Icybee\Routing\AdminController;

/**
 * @property User $user
 */
class UsersAdminController extends AdminController
{
	protected function action_profile()
	{
		$this->assert_has_permission('modify own profile');

		$this->view->content = $this->module->getBlock('edit', $this->user->uid);
		$this->view['block_name'] = 'edit';
	}
}
