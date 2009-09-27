<?php

class SymfonyTemplatingHookDelegate
{
	protected $engine;

	public function __construct($engine)
	{
		$this->engine = $engine;

		$this->initialize();
	}

	public function initialize()
	{
		Plugins::register(array($this, 'actionInitTheme'), 'action', 'init_theme');
	}

	protected function getConfigFilename()
	{
		return $this->engine->getThemeDirectory() . '/config/config.php';
	}

	public function actionInitTheme()
	{
		if (file_exists($this->engine->getThemeDirectory() . '/config/format.yml'))
		{
			$p = sfYaml::load($this->engine->getThemeDirectory() . '/config/format.yml');

			// $cache = '<?php' . PHP_EOL;
			$cache = '';

			foreach ($p['format'] as $function => $hooks)
			{
				foreach ($hooks as $hook => $onwhats)
				{
					foreach ($onwhats as $onwhat => $params)
					{
						$paramsString = '';

						if (null !== $params)
						{
							$paramsString = ', ';

							if (!is_array($params))
							{
								$params = array($params);
							}

							$paramsString .= sprintf(
								'\'%s\'',
								implode('\', \'', $params)
							);
						}

						$cache .= sprintf(
							'Format::%s(\'%s\', \'%s\'%s);' . PHP_EOL,
							$function,
							$hook,
							$onwhat,
							$paramsString
						);
					}

				}
			}

			eval($cache);
		}
	}
}