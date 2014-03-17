<?php
/**
* @copyright	Copyright (C) 2012 Johan Sundell. All rights reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

class PixPublishViewArticles extends JViewLegacy
{
	public function display( $tpl = null )
	{
		$this->enabled = false;
		$this->enabled = PixPublishHelper::isEnabled();
		$items = $this->get( 'Items' );
		$pagination = $this->get( 'Pagination' );
		
		if( count( $errors = $this->get('Errors' ) ) )
		{
			JError::raiseError( 500, implode('<br />', $errors ) );
			return false;
		}
		
		$this->pagination = $pagination;
		$this->items = $items;
		$this->addToolbar();
		parent::display( $tpl );
	}
	
	protected function addToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_PIXPUBLISH_MANAGER_ARTICLES' ) );
		$access = PixPublishHelper::getActions();
		
		if( $access->get( 'core.delete' ) )
			JToolBarHelper::deleteList( '', 'articles.delete' );
		if( $access->get( 'core.edit' ) )
			JToolBarHelper::editList( 'article.edit' );
		if( $access->get( 'core.create' ) )
			JToolBarHelper::addNew( 'article.add' );
		
		if( $access->get( 'core.admin' ) )
		{
			JToolBarHelper::divider();
			JToolBarHelper::preferences( 'com_pixpublish' );
		}
	}
}
