<?php

class jmTemplateHelperTheme extends sfTemplateHelper
{
	public function getName()
	{
		return 'theme';
	}	

	public function postCommentsLink($post, $zero, $one, $more)
	{
		$c = $post->comments->approved->count;

		switch ($c) {
			case '0':
				return $zero;
				break;
			case '1':
				return str_replace('%s', '1', $one);
				break;
			default:
				return str_replace('%s', $c, $more);
		}
	}
}