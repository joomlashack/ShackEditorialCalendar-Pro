<?php
/**
* @copyright	Copyright (C) 2012 Johan Sundell. All rights reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

class PixPublishModelArticle extends JModelAdmin
{
	public function getTable( $type = 'Article', $prefix = 'PixPublishTable', $config = array() )
	{
		return JTable::getInstance( $type, $prefix, $config );
	}
	
	public function getForm( $data = array(), $loadData = true )
	{
		$form = $this->loadForm( 'com_pixpublish.article', 'article', array( 'control' => 'jform', 'load_data' => $loadData ) );
		
		if( empty( $form ) )
			return false;
		return $form;
	}
	
	protected function allowEdit( $data = array(), $key = 'pixpublish_article_id' )
	{
		return JFactory::getUser()->authorise( 'core.edit', 'com_pixpublish.article.'.( (int)isset( $data[$key]) ? $data[$key] : 0 ) ) or parent::allowEdit( $data, $key );
	}
	
	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState( 'com_pixpublish.edit.article.data', array() );
		
		if( empty( $data ) )
			$data = $this->getItem();
		
		return $data;
	}
	
	function publish( &$pks, $value = 1 )
	{
		return parent::publish( $pks, $value );
	}
}
