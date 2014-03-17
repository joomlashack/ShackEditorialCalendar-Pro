<?php
/**
* @copyright	Copyright (C) 2012 Johan Sundell. All rights reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class PixPublishViewArticle extends JView
{
	public function display( $tpl = null )
	{
		$form = $this->get('Form');
		$item = $this->get( 'Item' );
		
		if( count( $errors = $this->get('Errors' ) ) )
		{
			JError::raiseError( 500, implode('<br />', $errors ) );
			return false;
		}
		
		$this->form = $form;
		$this->item = $item;
		
		$this->addToolBar();
		$this->addValidation();
		
		parent::display( $tpl );
	}
	
	protected function addToolBar()
	{
		JRequest::setVar( 'hidemainmenu', true );
		$isNew = ($this->item->pixpublish_article_id == 0);
		$access = PixPublishHelper::getActions( $this->item->pixpublish_article_id );
		$this->access = $access;
		
		JToolBarHelper::title( $isNew ? JText::_( 'COM_PIXPUBLISH_MANAGER_ARTICLE_NEW' ) : JText::_( 'COM_PIXPUBLISH_MANAGER_ARTICLE_EDIT' ) );
		
		if( $isNew )
		{
			if( $access->get( 'core.create' ) )
			{
				JToolBarHelper::apply( 'article.apply' );
				JToolBarHelper::save( 'article.save' );
				JToolBarHelper::custom( 'article.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false );
			}
			JToolBarHelper::cancel('article.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			if( $access->get( 'core.edit' ) )
			{
				JToolBarHelper::apply( 'article.apply', 'JTOOLBAR_APPLY' );
				JToolBarHelper::save( 'article.save', 'JTOOLBAR_SAVE' );
				if( $access->get( 'core.create' ) )
					JToolBarHelper::custom( 'article.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false );
			}
			else if( $access->get( 'core.create' ) )
				JToolBarHelper::custom( 'article.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false );
			
			JToolBarHelper::cancel( 'article.cancel', 'JTOOLBAR_CLOSE' );
		}
		
		
	}
	
	protected function addValidation()
	{
		JHtml::_( 'behavior.formvalidation' );
		$doc = JFactory::getDocument();
		$doc->addScript( JURI::root().'administrator/components/com_pixpublish/models/forms/article.js' );
		$doc->addScript( JURI::root().'administrator/components/com_pixpublish/models/forms/submit.js' );
	}
}
