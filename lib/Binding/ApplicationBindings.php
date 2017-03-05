<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Users\Binding;

use ICanBoogie\ActiveRecord;

use Icybee\Modules\Users\OwnershipResolverInterface;
use Icybee\Modules\Users\PermissionResolverInterface;
use Icybee\Modules\Users\User;

/**
 * {@link \ICanBoogie\Core} prototype bindings.
 *
 * @method bool check_user_permission(User $user, $permission, $target = null)
 * @method bool check_user_ownership(User $user, ActiveRecord $record)
 *
 * @property User $user
 * @property int $user_id
 * @property PermissionResolverInterface $user_permission_resolver
 * @property OwnershipResolverInterface $user_ownership_resolver
 */
trait ApplicationBindings
{

}
