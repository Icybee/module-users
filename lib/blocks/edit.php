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

use Brickrouge\Group;

use Icybee\Modules\Users\User;

use Brickrouge\Document;
use Brickrouge\Element;
use Brickrouge\Form;
use Brickrouge\Text;
use Brickrouge\Widget;

/**
 * A block to edit users.
 */
class EditBlock extends \Icybee\EditBlock
{
	static protected function add_assets(Document $document)
	{
		parent::add_assets($document);

		$document->js->add(DIR . 'public/admin.js');
	}

	protected function get_permission()
	{
		$user = $this->app->user;

		if ($user->has_permission(Module::PERMISSION_MANAGE, $this->module))
		{
			return true;
		}
		else if ($user->uid == $this->record->uid && $user->has_permission('modify own profile'))
		{
			return true;
		}

		return parent::get_permission();
	}

	protected function lazy_get_attributes()
	{
		return \ICanBoogie\array_merge_recursive(parent::lazy_get_attributes(), [

			Element::GROUPS => [

				'connection' => [ 'title' => 'Connection' ],
				'advanced' => [ 'title' => 'Advanced' ]

			]

		]);
	}

	protected function lazy_get_children()
	{
		$values = $this->values;
		$user = $this->app->user;
		$uid = $values[User::UID];
		$languages = $this->app->locale['languages'];

		uasort($languages, 'ICanBoogie\unaccent_compare_ci');

		$administer = $user->has_permission(Module::PERMISSION_MANAGE, $this->module);

		return array_merge(parent::lazy_get_children(), [

			#
			# name group
			#

			User::FIRSTNAME => new Text([

				Group::LABEL => 'firstname'

			]),

			User::LASTNAME => new Text([

				Group::LABEL => 'lastname'

			]),

			User::NICKNAME => new Text([

				Group::LABEL => 'Nickname'

			]),

			User::USERNAME => $administer ? new Text([

				Group::LABEL => 'username',
				Element::REQUIRED => true

			]) : null,

			User::NAME_AS => $this->create_control_for_name_as(),

			#
			# connection group
			#

			User::EMAIL => new Text([

				Group::LABEL => 'email',
				Element::GROUP => 'connection',
				Element::REQUIRED => true,

				'autocomplete' => 'off'

			]),

			User::PASSWORD => new Text([

				Element::LABEL => 'password',
				Element::LABEL_POSITION => 'above',
				Element::DESCRIPTION => 'password_' . ($uid ? 'update' : 'new'),
				Element::GROUP => 'connection',

				'autocomplete' => 'off',
				'type' => 'password',
				'value' => ''

			]),

			User::PASSWORD . '-verify' => new Text([

				Element::LABEL => 'password_confirm',
				Element::LABEL_POSITION => 'above',
				Element::DESCRIPTION => 'password_confirm',
				Element::GROUP => 'connection',

				'autocomplete' => 'off',
				'type' => 'password',
				'value' => ''

			]),

			User::IS_ACTIVATED => ($uid == 1 || !$administer) ? null : new Element(Element::TYPE_CHECKBOX, [

				Element::LABEL => 'is_activated',
				Element::GROUP => 'connection',
				Element::DESCRIPTION => 'is_activated'

			]),

			User::ROLES => $this->create_control_for_role(),

			User::LANGUAGE => new Element('select', [

				Group::LABEL => 'language',
				Element::GROUP => 'advanced',
				Element::DESCRIPTION => 'language',
				Element::OPTIONS => array(null => '') + $languages

			]),

			'timezone' => new Widget\TimeZone([

				Group::LABEL => 'timezone',
				Element::GROUP => 'advanced',
				Element::DESCRIPTION =>'timezone'

			]),

			User::RESTRICTED_SITES => $this->create_control_for_restricted_sites_ids()

		]);
	}

	protected function alter_actions(array $actions, array $params)
	{
		$actions = parent::alter_actions($actions, $params);

		$user = $this->app->user;
		$record = $this->record;

		if ($record && $record->uid == $user->uid && !$user->has_permission(Module::PERMISSION_ADMINISTER, $this->module))
		{
			unset($actions[\Icybee\OPERATION_SAVE_MODE]);
		}

		return $actions;
	}

	protected function create_control_for_role()
	{
		$app = $this->app;
		$user = $app->user;
		$uid = $this->values[User::UID];

		if ($uid == 1 || !$user->has_permission(Module::PERMISSION_ADMINISTER, $this->module))
		{
			return;
		}

		$rid = [ 2 => true ];

		if ($uid)
		{
			$record = $this->module->model[$uid];

			foreach ($record->roles as $role)
			{
				$rid[$role->rid] = true;
			}
		}

		$options = $app->models['users.roles']
		->select('rid, name')
		->where('rid != 1')
		->order('rid')
		->pairs;

		return new Element(Element::TYPE_CHECKBOX_GROUP, [

			Form::LABEL => 'roles',
			Element::GROUP => 'advanced',
			Element::OPTIONS => $options,
			Element::OPTIONS_DISABLED => [ 2 => true ],
			Element::REQUIRED => true,
			Element::DESCRIPTION => 'roles',

			'class' => 'framed inputs-list sortable',
			'value' => $rid

		]);
	}

	/**
	 * Returns the control element for the `name_as` param.
	 *
	 * @return Element
	 */
	protected function create_control_for_name_as()
	{
		$values = $this->values;

		$options = [ '<username>' ];

		if ($values[User::USERNAME])
		{
			$options[0] = $values[User::USERNAME];
		}

		$firstname = $values[User::FIRSTNAME];

		if ($firstname)
		{
			$options[1] = $firstname;
		}

		$lastname = $values[User::LASTNAME];

		if ($lastname)
		{
			$options[2] = $lastname;
		}

		if ($firstname && $lastname)
		{
			$options[3] = $firstname . ' ' . $lastname;
			$options[4] = $lastname . ' ' . $firstname;
		}

		$nickname = $values[User::NICKNAME];

		if ($nickname)
		{
			$options[User::NAME_AS_NICKNAME] = $nickname;
		}

		return new Element('select', [

			Group::LABEL => 'name_as',
			Element::OPTIONS => $options

		]);
	}

	protected function create_control_for_restricted_sites_ids()
	{
		$app = $this->app;
		$user = $app->user;

		if (!$user->has_permission(Module::PERMISSION_ADMINISTER, $this->module))
		{
			return;
		}

		$value = [];

		if ($this->record)
		{
			$value = $this->record->restricted_sites_ids;

			if ($value)
			{
				$value = array_combine($value, array_fill(0, count($value), true));
			}
		}

		$options = $app
			->models['sites']
			->select('siteid, IF(admin_title != "", admin_title, concat(title, ":", language))')
			->order('admin_title, title')
			->pairs;

		if (!$options)
		{
			return;
		}

		return new Element(Element::TYPE_CHECKBOX_GROUP, [

			Form::LABEL => 'siteid',
			Element::OPTIONS => $options,
			Element::GROUP => 'advanced',
			Element::DESCRIPTION => 'siteid',

			'class' => 'inputs-list widget-bordered',
			'value' => $value

		]);
	}
}
