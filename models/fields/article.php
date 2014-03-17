<?php
/**
* @copyright	Copyright (C) 2012 Johan Sundell. All rights reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*/

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldArticle extends JFormFieldList
{
	protected $type = 'Article';
	
	protected function getOptions()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery( true );
		
		$query->select( 'tbl.pixpublish_article_id, tbl.name, c.title as category, tbl.category_id' )
			->from( '#__pixpublish_articles AS tbl' )
			->leftJoin( '#__categories c ON c.id = tbl.category_id' );
		$db->setQuery( $query );
		
		$articles = $db->loadObjectList();
		$options = array();
		
		if( $articles )
		{
			foreach( $articles as $article )
				$options[] = JHtml::_( 'select.option', $article->pixpublish_article_id, $article->name.( $article->catid ? ' ('.$article->category.')' : '' ) );
		}
		$options = array_merge( parent::getOptions(), $options );
		
		return $options;
	}
}
