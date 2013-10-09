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

use ICanBoogie\I18n;

use Brickrouge\A;
use Brickrouge\Element;

use Icybee\Modules\Users\NonceLogin\NonceRequestForm;

class LoginComboElement extends Element
{
	protected $elements = array();

	public function __construct(array $attributes=array())
	{
		$login = new LoginForm;
		$password = new NonceRequestForm();

		$password->children['email'][Element::DESCRIPTION] = new A(I18n\t('Cancel', array(), array('scope' => 'button')));

		$this->elements['login'] = $login;
		$this->elements['password'] = $password;

		parent::__construct
		(
			'div', $attributes + array
			(
				Element::WIDGET_CONSTRUCTOR => 'LoginCombo',

				'id' => 'login',
				'class' => 'widget-login-combo'
			)
		);
	}

	protected function render_inner_html()
	{
		return parent::render_inner_html() . <<<EOT
<div class="wrapper">{$this->elements['login']}</div>
<div class="wrapper" style="height: 0">{$this->elements['password']}</div>
EOT;
	}
}