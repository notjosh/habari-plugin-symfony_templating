<?php

require_once dirname(__FILE__) . '/lib/SymfonyTemplatingEngine.class.php';
require_once dirname(__FILE__) . '/lib/SymfonyTemplatingHookDelegate.class.php';

class Symfony_Templating extends Plugin
{
	const VERSION = '0.1';

	public function info ()
	{
		return array(
			'name' => 'Symfony Templating Plugin',
			'url' => 'http://notjosh.com/',
			'author' => 'Joshua May',
			'authorurl' => 'http://notjosh.com/',
			'version' => self::VERSION,
			'description' => 'Symfony Templating component for templating, wee!',
			'license' => 'Apache License 2.0'
		);
	}
}

?>