<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Users;

require __DIR__ . '/../vendor/autoload.php';

namespace ICanBoogie;

/*
 * A dummy Core object
 */

global $core;

$core = (object) array
(
	'configs' => array
	(
		'user' => array
		(
			'password_salt' => sha1('Sufjan Stevens'),
			'unlock_login_salt' => sha1('Cat Power')
		)
	)
);

$events = new Events();

Events::patch('get', function() use($events) { return $events; });

/*
 * The following is comming from ICanBoogie.
 */

const TOKEN_NUMERIC = "23456789";
const TOKEN_ALPHA = "abcdefghjkmnpqrstuvwxyz";
const TOKEN_ALPHA_UPCASE = "ABCDEFGHJKLMNPQRTUVWXYZ";
const TOKEN_SYMBOL = "!$=@#";
const TOKEN_SYMBOL_WIDE = '%&()*+,-./:;<>?@[]^_`{|}~';

define('ICanBoogie\TOKEN_NARROW', TOKEN_NUMERIC . TOKEN_ALPHA . TOKEN_SYMBOL);
define('ICanBoogie\TOKEN_MEDIUM', TOKEN_NUMERIC . TOKEN_ALPHA . TOKEN_SYMBOL . TOKEN_ALPHA_UPCASE);
define('ICanBoogie\TOKEN_WIDE', TOKEN_NUMERIC . TOKEN_ALPHA . TOKEN_SYMBOL . TOKEN_ALPHA_UPCASE . TOKEN_SYMBOL_WIDE);

function generate_token($length=8, $possible=TOKEN_NARROW)
{
	$token = '';
	$y = strlen($possible) - 1;

	while ($length--)
	{
		$i = mt_rand(0, $y);
		$token .= $possible[$i];
	}

	return $token;
}

function pbkdf2($p, $s, $c=1000, $kl=32, $a='sha256')
{
	$hl = strlen(hash($a, null, true)); # Hash length
	$kb = ceil($kl / $hl); # Key blocks to compute
	$dk = ''; # Derived key

	# Create key
	for ($block = 1 ; $block <= $kb ; $block++)
	{
		# Initial hash for this block
		$ib = $b = hash_hmac($a, $s . pack('N', $block), $p, true);
		# Perform block iterations
		for ( $i = 1; $i < $c; $i ++ )
		# XOR each iterate
		$ib ^= ($b = hash_hmac($a, $b, $p, true));
		$dk .= $ib; # Append iterated block
	}

	# Return derived key of correct length
	return substr($dk, 0, $kl);
}