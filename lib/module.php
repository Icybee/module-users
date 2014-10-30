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

use Icybee\Modules\Views\View;

use ICanBoogie\I18n;
use ICanBoogie\Operation;

use Brickrouge\Button;
use Brickrouge\Element;
use Brickrouge\Form;

class Module extends \Icybee\Module
{
	const OPERATION_LOGIN = 'login';
	const OPERATION_LOGOUT = 'logout';
	const OPERATION_ACTIVATE = 'activate';
	const OPERATION_DEACTIVATE = 'deactivate';
	const OPERATION_IS_UNIQUE = 'is_unique';

	static $config_default = [

		'notifies' => [

			/*
			'password' => [

				'subject' => 'Vos paramètres de connexion à Icybee',
				'from' => 'no-reply@example.com',
				'template' => 'Bonjour,

Voici vos paramètres de connexion au système de gestion de contenu Icybee :

Identifiant : "#{@username}" ou "#{@email}"
Mot de passe : "#{@password}"

Une fois connecté vous pourrez modifier votre mot de passe. Pour cela cliquez sur votre nom dans la barre de titre et éditez votre profil.

Cordialement'
			]
			*/

		]

	];

	protected function resolve_primary_model_tags($tags)
	{
		return parent::resolve_model_tags($tags, 'primary') + [

			Model::T_CONSTRUCTOR => $this->id

		];
	}

	protected function block_connect()
	{
		global $core;

		$core->document->css->add(DIR . 'public/authenticate.css');

		return new \Icybee\Modules\Users\LoginComboElement;
	}

	protected function block_logout()
	{
		return new Form([

			Form::HIDDENS => [

				Operation::NAME => self::OPERATION_LOGOUT,
				Operation::DESTINATION => $this->id

			],

			Element::CHILDREN => [

				new Button('logout', [ 'type' => 'submit' ])

			]

		]);
	}

	protected function block_profile()
	{
		global $core;

		$core->document->page_title = I18n\t('My profile');

		$module = $this;
		$user = $core->user;
		$constructor = $user->constructor;

		if ($constructor != $this->id)
		{
			$module = $core->modules[$user->constructor];
		}

		return $module->getBlock('edit', $user->uid);
	}
}
