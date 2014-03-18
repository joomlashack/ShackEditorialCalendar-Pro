<?php
/**
* @copyright	Copyright (C) 2013 Johan Sundell. All rights reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*/

// No direct access.
defined('_JEXEC') or die;

class PixPublishViewPanel extends JViewLegacy
{
	public function display( $tpl = null )
	{
		JHtml::_( 'jquery.framework', true );
		//JHtml::_('jquery.ui');
		
		$doc = JFactory::getDocument();
		$doc->addScript( JUri::root().'administrator/components/com_pixpublish/media/lib/jquery/jquery-ui.custom.min.js' );
		$doc->addScript( JUri::root().'administrator/components/com_pixpublish/media/lib/fullcalendar/fullcalendar.min.js' );
		$doc->addStyleSheet( JUri::root().'administrator/components/com_pixpublish/media/lib/fullcalendar/fullcalendar.css' );
		$this->addToolbar();
		parent::display( $tpl );
	}
	
	protected function addToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_PIXPUBLISH_MANAGER_PANEL' ) );
	}
}
