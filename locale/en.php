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
		'created' => 'Date created',
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

	'nonce_login_request.operation' => array
	(
		'already_sent' => "A message has already been sent to your e-mail address. In order to reduce abuses, you won't be able to request a new one until :time.",

		'title' => 'Request a nonce login',
		'message' => array
		(
			'subject' => "Here's a message to help you login",
			'template' => <<<EOT
This message has been sent to help you login.

Using the following URL you'll be able to login instantly and update your password:

:url

This URL can only be used once and is only valid until :until.

If you didn't create an account neither asked for a new password, this message might be the result
of an attack attempt on the website. If you think this is the case, please contact its admin.

The remote address of the request was: :ip.
EOT
		),

		'success' => "A message to help you login has been sent to the email address %email.",

		'unknown_email' => array
		(
			'message' => array
			(
				'title' => 'Account access attempted',
				'template' => <<<EOT
You (or someone else) entered this email address when trying to change the password of an account.

However, this email address is not in our database of registered users and therefore the attempted
password change has failed.

If you are a user and where expecting this email, please try again using the email address you gave
when opening your account.

If you are not a user, please ignore this email.

The remote address of the request was: :ip.
EOT
			)
		)
	),

	'permission.modify own profile' => "The user can modify its profile",

	'group.title' => array
	(
		'contact' => 'Contact',
		'connection' => 'Connection'
	),

	'module_title.users' => 'Users'
);