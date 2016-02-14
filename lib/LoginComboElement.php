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

use Brickrouge\A;
use Brickrouge\Element;

use Icybee\Modules\Users\NonceLogin\NonceLoginRequestForm;

class LoginComboElement extends Element
{
	protected $elements = [];

	public function __construct(array $attributes = [])
	{
		$login = new LoginForm;
		$password = new NonceLoginRequestForm;

		$password->children['email'][Element::DESCRIPTION] = new A($this->t('Cancel', [], [ 'scope' => 'button' ]));

		$this->elements['login'] = $login;
		$this->elements['password'] = $password;

		parent::__construct('div', $attributes + [

			Element::IS => 'user-login-combo',

			'id' => 'login',
			'class' => 'widget-login-combo'

		]);
	}

	protected function render_inner_html()
	{
		return parent::render_inner_html() . <<<EOT
{$this->elements['login']}
{$this->elements['password']}
EOT;
	}
}
