<?php

class jmTemplateHelperUrl extends sfTemplateHelper
{
	public function getName()
	{
		return 'url';
	}	

	public function generate($ruleName = null, $args = array(), $useAll = true, $noAmp = true, $prependSite = true)
	{
		return URL::get($ruleName, $args, $useAll, $noAmp, $prependSite);
	}
}