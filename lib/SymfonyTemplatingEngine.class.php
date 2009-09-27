<?php

class SymfonyTemplatingEngine extends TemplateEngine
{
	protected $templateParameters = array();
	protected $parameters = array();

	protected $themeDirectory,
		$templateDir = array(),
		$availableTemplates = array();

    public function __construct()
    {
		$this->loadAssets();
        $this->initialize();
    }

	protected function loadAssets()
	{
		// sfYaml
		require_once dirname(__FILE__) . '/vendor/sfYaml/sfYaml.php';

		// sfTemplating
		require_once dirname(__FILE__) . '/vendor/sfTemplating/sfTemplateAutoloader.php';
		sfTemplateAutoloader::register();
	}

    public function initialize()
    {
		new SymfonyTemplatingHookDelegate($this);
    }

	public function __get($key)
	{
		if (isset($this->$key))
		{
			return $this->$key;
		}

		return isset($this->parameters[$key]) ? $this->parameters[$key] : null;
	}

	public function __set($key, $value)
	{
		if (isset($this->$key))
		{
			$this->$key = $value;
			return;
		}

		$this->parameters[$key] = $value;
	}

	public function __unset($key)
	{
		if (isset($this->$key))
		{
			unset($this->$key);
			return;
		}

		unset($this->parameters[$key]);
	}

	public function __isset($key)
	{
		if (isset($this->$key))
		{
			return true;
		}

		return isset($this->parameters[$key]);
	}

	public function display($template, $return = false)
	{
		$loader = new sfTemplateLoaderFilesystem(
			$this->getTemplateLoaderPathMasks()
		);

		$engine = new sfTemplateEngine($loader);
		$engine->setHelperSet(new sfTemplateHelperSet($this->getTemplateHelperSets()));
// var_dump($this->templateParameters);exit;
		$result = $engine->render($template, $this->templateParameters);

		if ($return)
		{
			return $result;
		}

		echo $result;
	}

	protected function getTemplateLoaderPathMasks()
	{
		$a = array();

		foreach ($this->templateDir as $t)
		{
			$a[] = $t . '%name%.%renderer%';
		}

		return $a;
	}

	protected function getTemplateHelperSets()
	{
// var_dump(Site::get_url('habari'));exit;
		$sets = array(
			new sfTemplateHelperAssets(
				Site::get_path('theme') . '/web',
				Site::get_url('habari')
			),
			new sfTemplateHelperJavascripts(),
			new sfTemplateHelperStylesheets(),
		);

		$dirs = array(
			$this->themeDirectory . '/lib/helper',
			dirname(__FILE__) . '/helper',
		);

		foreach ($dirs as $dir)
		{
			foreach (glob($dir . '/*.php') as $helperPath)
			{
				include_once $helperPath;

				$helperFilename = basename($helperPath);

				$className = substr($helperFilename, 0, strpos($helperFilename, '.'));
				$sets[] = new $className();
			}
		}

		return $sets;
	}

	public function set_template_dir($directory)
	{
		$this->themeDirectory = $directory;

		$this->templateDir = array(
			$directory . '/templates/',
		);
	}

	public function template_exists($template)
	{
		if (empty($this->availableTemplates))
		{
			$allTemplates = array();
			$dirs = array_reverse($this->templateDir);

			foreach($dirs as $dir)
			{
				$templates = Utils::glob($dir . '*.*');
				$allTemplates = array_merge($allTemplates, $templates);
			}

			if ($allTemplates)
			{
				$this->availableTemplates = array_map('basename', $allTemplates, array_fill(1, count($allTemplates), '.php'));

				$this->template_map = array_combine($this->availableTemplates, $allTemplates);
				array_unique($this->availableTemplates);
				$this->availableTemplates = Plugins::filter('available_templates', $this->availableTemplates, __CLASS__);
			}
		}

		return in_array($template, $this->availableTemplates);
	}

	public function fetch($template)
	{
		return $this->display($template, true);
	}

	public function assign( $key, $value = '' )
	{
		$this->templateParameters[$key] = $value;
	}

	public function assigned($key)
	{
		return isset($this->templateParameters[$key]);
	}

	public function append($key, $value = '')
	{
		if (!isset($this->templateParameters[$key]))
		{
			$this->templateParameters[$key][] = $value;
		}
		else
		{
			$this->templateParameters[$key] = $value;
		}
	}

	public function getThemeDirectory()
	{
		return $this->themeDirectory;
	}
}