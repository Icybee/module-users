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

use function ICanBoogie\app;
use ICanBoogie\ActiveRecord;
use ICanBoogie\Application;
use ICanBoogie\HTTP\AuthenticationRequired;
use ICanBoogie\HTTP\PermissionRequired;
use ICanBoogie\HTTP\Response;
use ICanBoogie\HTTP\SecurityError;
use ICanBoogie\Operation;
use ICanBoogie\PropertyNotDefined;
use ICanBoogie\Routing\RouteDispatcher;

use Icybee\Element\AdminDecorator;
use Icybee\Element\DocumentDecorator;
use Icybee\Modules\Members\Member;

class Hooks
{
	/*
	 * Events
	 */

	/**
	 * Checks if the role to be deleted is used or not.
	 *
	 * @param Operation\BeforeProcessEvent $event
	 * @param \Icybee\Modules\Users\Roles\Operation\DeleteOperation $operation
	 */
	static public function before_roles_delete(Operation\BeforeProcessEvent $event, \Icybee\Modules\Users\Roles\Operation\DeleteOperation $operation)
	{
		$rid = $operation->key;
		$count = app()->models['users/has_many_roles']->filter_by_rid($rid)->count;

		if (!$count)
		{
			return;
		}

		$event->errors['rid'] = $operation->format('The role %name is used by :count users.', [ 'name' => $operation->record->name, ':count' => $count ]);
	}

	/**
	 * Displays a login form on {@link SecurityError}.
	 *
	 * @param \ICanBoogie\Exception\RescueEvent $event
	 * @param SecurityError $target
	 */
	static public function on_security_exception_rescue(\ICanBoogie\Exception\RescueEvent $event, SecurityError $target)
	{
		$request = $event->request;

		if (!$request->is_get)
		{
			return;
		}

		if ($target instanceof PermissionRequired || \ICanBoogie\Routing\decontextualize($request->normalized_path) != '/admin/')
		{
			\ICanBoogie\log_error($target->getMessage());
		}

		$block = app()->modules['users']->getBlock('connect');

		$document = new DocumentDecorator(new AdminDecorator($block));
		$document->body->add_class('page-slug-authenticate');

		$event->response = new Response((string) $document, $target->getCode(), [

			'Content-Type' => 'text/html; charset=utf-8'

		]);

		$event->stop();
	}

	/**
	 * Displays an _available websites_ form on {@link WebsiteAdminNotAccessible}.
	 *
	 * @param \ICanBoogie\Exception\RescueEvent $event
	 * @param WebsiteAdminNotAccessible $target
	 */
	static public function on_website_admin_not_accessible_rescue(\ICanboogie\Exception\RescueEvent $event, WebsiteAdminNotAccessible $target)
	{
		$block = app()->modules['users']->getBlock('available-sites');

		$document = new DocumentDecorator(new AdminDecorator($block));

		$event->response = new Response((string) $document, $target->getCode(), [

			'Content-Type' => 'text/html; charset=utf-8'

		]);

		$event->stop();
	}

	/**
	 * The {@link PermissionRequired} exception is thrown if a member attempts to enter the admin.
	 *
	 * Authenticated users who don't have access to the admin of a website are redirected to the
	 * `/admin/profile/sites` URL, in which case the `response` property of the event is altered
	 * with a {@link RedirectResponse}.
	 *
	 * @param RouteDispatcher\BeforeDispatchEvent $event
	 * @param RouteDispatcher $target
	 *
	 * @throws PermissionRequired if a member attempt to enter the admin.
	 * @throws WebsiteAdminNotAccessible if a user attempts to access the admin of a website he
	 * doesn't have access to.
	 */
	static public function before_routing_dispatcher_dispatch(RouteDispatcher\BeforeDispatchEvent $event, RouteDispatcher $target)
	{
		$path = \ICanBoogie\normalize_url_path($event->request->decontextualized_path);

		if (strpos($path, '/admin/') !== 0)
		{
			return;
		}

		$app = app();
		$user = $app->user;

		if ($user->is_guest)
		{
			throw new AuthenticationRequired;
		}

		if ($user instanceof Member)
		{
			throw new PermissionRequired;
		}

		if ($user->language)
		{
			$app->locale = $user->language;
		}

		if (strpos($path, '/admin/profile/sites/') === 0)
		{
			return;
		}

		$restricted_sites = null;

		try
		{
			$restricted_sites = $user->restricted_sites_ids;
		}
		catch (PropertyNotDefined $e)
		{
			throw $e;
		}
		catch (\Exception $e)
		{
			#
			# not important
			#
		}

		if (!$restricted_sites || in_array($app->site_id, $restricted_sites))
		{
			return;
		}

		throw new WebsiteAdminNotAccessible;
	}

	/*
	 * Prototype methods
	 */

	/**
	 * Returns the user's identifier.
	 *
	 * This is the getter for the `$app->user_id` property.
	 *
	 * @param Application $app
	 *
	 * @return int|null Returns the identifier of the user or null if the user is a guest.
	 *
	 * @see User::login()
	 */
	static public function get_user_id(Application $app)
	{
		$session = $app->session;

		if (!$session->is_referenced)
		{
			return null;
		}

		return isset($session['user_id']) ? $session['user_id'] : null;
	}

	/**
	 * Returns the user object.
	 *
	 * If the user identifier can be retrieved from the session, it is used to find the
	 * corresponding user.
	 *
	 * If no user could be found, a guest user object is returned.
	 *
	 * This is the getter for the `$app->user` property.
	 *
	 * @param Application $app
	 *
	 * @return User The user object, or guest user object.
	 */
	static public function get_user(Application $app)
	{
		$user = null;
		$uid = $app->user_id;
		$model = $app->models['users'];

		try
		{
			if ($uid)
			{
				$user = $model[$uid];
			}
		}
		catch (\Exception $e) {}

		if (!$user)
		{
			$session = $app->session;

			if ($session->is_referenced)
			{
				unset($session['user_id']);
			}

			$user = new User($model);
		}

		return $user;
	}

	/**
	 * Returns a user permission resolver configured with the `users` config.
	 *
	 * @param Application $app
	 *
	 * @return PermissionResolver
	 */
	static public function get_user_permission_resolver(Application $app)
	{
		return new PermissionResolver($app->configs['users_permission_resolver_list']);
	}

	/**
	 * Returns a user permission resolver configured with the `users` config.
	 *
	 * @param Application $app
	 *
	 * @return OwnershipResolver
	 */
	static public function get_user_ownership_resolver(Application $app)
	{
		return new OwnershipResolver($app->configs['users_ownership_resolver_list']);
	}

	/**
	 * Checks if a user has a given permission.
	 *
	 * @param Application $app
	 * @param User $user
	 * @param string $permission
	 * @param string $target
	 *
	 * @return bool
	 */
	static public function check_user_permission(Application $app, User $user, $permission, $target = null)
	{
		$user_permission_resolver = $app->user_permission_resolver;

		return $user_permission_resolver($user, $permission, $target);
	}

	/**
	 * Checks if a user has the ownership of a record.
	 *
	 * @param Application $app
	 * @param User $user
	 * @param ActiveRecord $record
	 *
	 * @return bool
	 */
	static public function check_user_ownership(Application $app, User $user, ActiveRecord $record)
	{
		$user_ownership_resolver = $app->user_ownership_resolver;

		return $user_ownership_resolver($user, $record);
	}

	/**
	 * Resolves user ownership of a record.
	 *
	 * @param User $user
	 * @param ActiveRecord $record
	 *
	 * @return boolean|null `true` if the user identifier is 1, or the `uid` property of
	 * the record is not empty and it matches the user identifier. `null` otherwise.
	 */
	static public function resolve_user_ownership(User $user, ActiveRecord $record)
	{
		$uid = $user->uid;

		if ($uid == 1 || (!empty($record->uid) && $record->uid == $uid))
		{
			return true;
		}
	}

	/*
	 * Markups
	 */

	static public function markup_form_login(array $args, $engine, $template)
	{
		$form = new LoginForm();

		return $template ? $engine($template, $form) : $form;
	}

	/**
	 * Retrieves the current user.
	 *
	 * <pre>
	 * <p:user>
	 * <!-- Content: with-param*, template -->
	 * </p:user>
	 * </pre>
	 *
	 * @param array $args
	 * @param \Patron\Engine $engine
	 * @param array $template
	 */
	static public function markup_user(array $args, $engine, $template)
	{
		return $engine($template, app()->user);
	}
}
