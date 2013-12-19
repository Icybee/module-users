<?php

return array
(
	'description' => array
	(
		'is_activated' => "Only users whose account has been activated can connect.",

		'password_confirm' => "If you entered a password, please confirm.",

		'password_new' => "If the field is blank when registering a new account, a password is
		generated automatically. To personalize it, enter the password.",

		'password_update' => "If you want to change your password, please enter the new one in
		this field. Otherwise, leave the field empty.",

		'roles' => "Because you have permission, you can choose the user's roles."
	),

	'users.edit.element' => array
	(
		'description.language' => "This is the language to be used for the interface."
	),

	'users.manage.column' => array
	(
		'created_at' => 'Date created',
		'email' => 'E-mail',
		'logged_at' => 'Date logged',
		'roles' => 'Roles',
		'username' => 'User name'
	),

	'label' => array
	(
		'name_as' => 'Name as',
		'email' => 'E-mail',
		'email_confirm' => 'Confirm e-mail',
		'firstname' => 'Firstname',
		'is_activated' => "The user's account is active",
		'logged_at' => 'Date connected',
		'lastname' => 'Lastname',
		'lost_password' => 'I forgot my password',
		'name' => 'Name',
		'password' => 'Password',
		'password_confirm' => 'Confirm',
		'roles' => 'Roles',
		'timezone' => 'Timezone',
		'username' => 'Username',
		'your_email' => 'Your email address'
	),

	'manage.title' => array
	(
		'is_activated' => 'Activated'
	),

	'module_category.users' => 'Users',

	'activate.operation' => array
	(
		'title' => 'Activate users',
		'short_title' => 'Activate',
		'continue' => 'Activate',
		'cancel' => "Don't activate",

		'confirm' => array
		(
			'one' => 'Are you sure you want to activate the selected user?',
			'other' => 'Are you sure you want to activate the :count selected users?'
		)
	),

	'deactivate.operation' => array
	(
		'title' => 'Deactivate users',
		'short_title' => 'Deactivate',
		'continue' => 'Deactivate',
		'cancel' => "Don't deactivate",

		'confirm' => array
		(
			'one' => 'Are you sure you want to deactivate the selected user?',
			'other' => 'Are you sure you want to deactivate the :count selected users?'
		)
	),

	'permission.modify own profile' => "The user can modify its profile",

	'group.title' => array
	(
		'contact' => 'Contact',
		'connection' => 'Connection'
	),

	'module_title.users' => 'Users',

	'users.login.updated_security' => "The safety of user accounts has been strengthened, <a href=\"!url\">update your password</a> to benefit from it."
);