<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Third Party Services
	|--------------------------------------------------------------------------
	|
	| This file is for storing the credentials for third party services such
	| as Stripe, Mailgun, Mandrill, and others. This file provides a sane
	| default location for this type of information, allowing packages
	| to have a conventional place to find your various credentials.
	|
	*/

	'mailgun' => [
		'domain' => '',
		'secret' => '',
	],

	'mandrill' => [
		'secret' => '',
	],

	'ses' => [
		'key' => '',
		'secret' => '',
		'region' => 'us-east-1',
	],

	'stripe' => [
		'model'  => 'Admin',
		'secret' => '',
	],

    'google' => [
        'client_id' => getenv('GOOGLE_ID'),
        'client_secret' => getenv('GOOGLE_SECRET'),
        'redirect' => getenv('GOOGLE_REDIRECT_URL')
    ],

    'github' => [
        'client_id' => getenv('GITHUB_ID'),
        'client_secret' => getenv('GITHUB_SECRET'),
        'redirect' => getenv('GITHUB_REDIRECT_URL')
    ]

];
