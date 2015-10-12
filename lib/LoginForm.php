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

use ICanBoogie\Operation;

use Brickrouge\A;
use Brickrouge\Button;
use Brickrouge\Document;
use Brickrouge\Element;
use Brickrouge\Form;
use Brickrouge\Text;

use Icybee\Binding\Core\PrototypedBindings;

class LoginForm extends Form
{
	use PrototypedBindings;

	const PASSWORD_RECOVERY_LINK = '#password-recovery-link';

	/**
	 * @var Element
	 */
	public $lost_password;

	/**
	 * Adds the "widget.css" and "widget.js" assets.
	 *
	 * @inheritdoc
	 */
	static protected function add_assets(Document $document)
	{
		$document->css->add(DIR . 'public/widget.css');
		$document->js->add(DIR . 'public/widget.js');

		parent::add_assets($document);
	}

	public function __construct(array $attributes = [])
	{
		$app = $this->app;

		$this->lost_password = new A($this->t('lost_password', [], [ 'scope' => 'users.label', 'default' => 'I forgot my password' ]), "#lost-password", [

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

				'redirect_to' => $app->request['redirect_to']

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

			Element::IS => 'Login',

			'class' => 'widget-login',
			'name' => 'users/login',
			'action' => $this->app->url_for('api:login')

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
