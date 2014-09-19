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
use ICanBoogie\Operation;

use Brickrouge;
use Brickrouge\A;
use Brickrouge\Button;
use Brickrouge\Element;
use Brickrouge\Form;
use Brickrouge\Text;

class LoginForm extends Form
{
	const PASSWORD_RECOVERY_LINK = '#password-recovery-link';

	public $lost_password = [];

	/**
	 * Adds the "widget.css" and "widget.js" assets.
	 *
	 * @param Brickrouge\Document $document
	 */
	static protected function add_assets(\Brickrouge\Document $document)
	{
		$document->css->add(DIR . 'public/widget.css');
		$document->js->add(DIR . 'public/widget.js');

		parent::add_assets($document);
	}

	public function __construct(array $attributes=[])
	{
		global $core;

		$this->lost_password = new A(I18n\t('lost_password', [], [ 'scope' => 'users.label', 'default' => 'I forgot my password' ]), "#lost-password", [

			'rel' => 'nonce-request'

		]);

		parent::__construct($attributes + [

			Form::ACTIONS => [

				new Button('Connect', [

					'type' => 'submit',
					'class' => 'btn-primary'

				])

			],

			Form::RENDERER => 'Simple',

			Form::HIDDENS => [

				Operation::DESTINATION => 'users',
				Operation::NAME => Module::OPERATION_LOGIN,
				Operation::SESSION_TOKEN => $core->session->token,
				'redirect_to' => $core->request['redirect_to']

			],

			Element::CHILDREN => [

				User::USERNAME => new Text([

					Form::LABEL => 'username',
					Element::REQUIRED => true

				]),

				User::PASSWORD => new Text([

					Form::LABEL => 'password',
					Element::REQUIRED => true,
					Element::DESCRIPTION => $this->lost_password,

					'type' => 'password'

				])
			],

			Element::WIDGET_CONSTRUCTOR => 'Login',

			'class' => 'widget-login',
			'name' => 'users/login'

		]);
	}

	public function render()
	{
		$password_recovery_link = $this[self::PASSWORD_RECOVERY_LINK];

		if ($password_recovery_link)
		{
			$this->lost_password['href'] = $password_recovery_link;
		}

		return parent::render();
	}
}