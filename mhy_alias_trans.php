<?php
/**
 * @copyright Copyright (C) 2014 maihuayi.com, All rights reserved.
 * @license GNU/GPL V2 http://gnu.org/licenses/gpl-2.0.html
 * @author bsdnemo at gmail dot com
 * @url https://github.com/maihuayi/mhy-alias-trans
 */
 
defined('_JEXEC') or die;

class PlgContentMHY_Alias_Trans extends JPlugin
{
	public function onContentBeforeSave($context, $article, $isNew) {
		if(isset($article->title)) {
			$title = $article->title;
		} else if (isset($article->name)) {
			$title = $article->name;
		} else {
			return true;
		}
		$alias = JFilterOutput::stringURLSafe($title);
		
		if($alias == '') {
			$alias = JFactory::getDate()->format('Y-m-d-H-i-s');
		}
		
		if($alias = $article->alias) {
			$trans = $this->getTrans($title);
			if($trans) {
				$article->alias = JFilterOutput::stringURLSafe(str_replace(' ', '-', strtolower($trans)));
			}
		}
		return true;
	}
	
	private function getTrans($string) {
		$q = urlencode($string);
		$url = 'http://openapi.baidu.com/public/2.0/bmt/translate?client_id=' . $this->params->get('baidu_app_key') . '&q=' . $q . '&from=' . $this->params->get('from', auto) . '&to=en';
		$result = file_get_contents($url);
		$data = json_decode($result);
		if(isset($data->error_code)) {
			return false;
		} else {
			return urldecode($data->trans_result[0]->dst);			
		}
	}
}
