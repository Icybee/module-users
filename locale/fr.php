<?php

return [

	'button' => [

		'Connect' => 'Connexion'

	],

	'module_title.users' => 'Utilisateurs',

	'users' => [

		'count' => [

			'none' => 'Aucun utilisateur',
			'one' => 'Un utilisateur',
			'other' => ':count utilisateurs'

		],

		'name' => [

			'one' => 'Utilisateur',
			'other' => 'Utilisateurs'

		]
	],

	'description' => [

		'is_activated' => "Seuls les utilisateurs dont le compte est activé peuvent se connecter.",

		'password_confirm' => "Si vous avez saisi un mot de passe, veuillez le confirmer.",

		'password_new' => "Si le champs est vide lors de l'enregistrement d'un nouveau compte, un
		mot de passe est généré automatiquement. Pour le personnaliser, veuillez saisir le mot de
		passe.",

		'password_update' => "Si vous souhaitez changer de mot de passe, veuillez saisir le
		nouveau dans ce champ. Sinon, laissez-le vide.",

		'roles' => "Parce que vous en avec la permission, vous pouvez choisir le ou les rôles de
		l'utilisateur."

	],

	'users.edit_block.element.description' => [

		'language' => "Si la langue n'est pas défini celle du site est utilisée.",
		'timezone' => "Si le fuseau horaire n'est pas défini celui du site est utilisé."

	],

	'users.edit.element' => [

		'label.siteid' => "Restriction d'accès aux sites",
		'description.siteid' => "Permet de restraindre l'accès de l'utilisateur aux
		sites sélectionnés. Si aucun site n'est sélectionné, tous les sites lui sont accessibles.",
		'description.language' => "Il s'agit de la langue à utiliser pour l'interface."

	],

	'label' => [

		'logout' => 'Déconnexion',
		'name_as' => 'Nom comme',
		'email' => 'E-mail',
		'firstname' => 'Prénom',
		'Firstname' => 'Prénom',
		'is_activated' => "Le compte de l'utilisateur est actif",
		'logged_at' => 'Connecté le',
		'lastname' => 'Nom',
		'Lastname' => 'Nom',
		'lost_password' => "J'ai oublié mon mot de passe",
		'name' => 'Nom',
		'Name' => 'Nom',
		'Nickname' => 'Surnom',
		'password' => 'Mot de passe',
		'Password' => 'Mot de passe',
		'password_confirm' => 'Confirmation',
		'Current password' => "Mot de passe actuel",
		"New password" => "Nouveau mot de passe",
		"Confirm password" => "Confirmation du mot de passe",
		'Roles' => 'Rôles',
		'Timezone' => 'Zone horaire',
		'username' => 'Identifiant',
		'Username' => 'Identifiant',
		'your_email' => 'Votre adresse e-mail'

	],

	'module_category.users' => 'Utilisateurs',

	'group.title' => [

		'connection' => 'Connexion'

	],

	'users.manager.label.logged_at' => 'Connecté le',

	'permission.modify own profile' => 'Modifier son profil',

	#
	# login
	#

	'Disconnect' => 'Déconnexion',
	'Unknown username/password combination.' => 'Combinaison Identifiant/Mdp inconnue.',
	'User %username is not activated' => "Le compte de l'utilisateur %username n'est pas actif",
	'You are connected as %username, and your role is %role.' => 'Vous êtes connecté en tant que %username, et votre rôle est %role.',
	'Administrator' => 'Administrateur',
	'My profile' => 'Mon profil',
	'User profile' => 'Profil utilisateur',
	"Password and password verify don't match." => "Le mot de passe et sa vérification ne correspondent pas.",
	'users.login.updated_security' => "La sécurité des comptes utilisateur a été renforcée, <a href=\"!url\">mettez à jour votre mot de passe</a> pour en bénéficier.",

	#
	# resume
	#

	'Users' => 'Utilisateurs',
	'Role' => 'Rôle',
	'send a new password' => 'envoyer un nouveau mot de passe',

	#
	# management
	#

	'confirm' => 'confirmer',

	"Your profile has been created." => "Votre profil a été créé.",
	"Your profile has been updated." => "Votre profil a été mis à jour.",
	"%name's profile has been created." => "Le profil de %name a été créé.",
	"%name's profile has been updated." => "Le profil de %name a été mis à jour.",

	# operation/activate

	"!name account is active." => "Le compte de !name est actif."

];
