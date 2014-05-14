<?php
/**
* @copyright	Copyright (C) 2014 Johan Sundell. All rights reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*/

// No direct access.
defined('_JEXEC') or die;

class PixPublishViewPanel extends JViewLegacy
{
	public function display( $tpl = null )
	{
		JHtml::_( 'jquery.framework', true );
		JHtml::_( 'bootstrap.framework' );
		
		$doc = JFactory::getDocument();
		// Fullcalendar
		$doc->addScript( JUri::root().'administrator/components/com_pixpublish/media/lib/jquery/jquery-ui.custom.min.js' );
		$doc->addScript( JUri::root().'administrator/components/com_pixpublish/media/lib/fullcalendar/fullcalendar.min.js' );
		$doc->addStyleSheet( JUri::root().'administrator/components/com_pixpublish/media/lib/fullcalendar/fullcalendar.css' );
		
		// Popup
		$doc->addScript( JUri::root().'administrator/components/com_pixpublish/media/lib/messi/messi.hacked.js' );
		$doc->addStyleSheet(JUri::root().'administrator/components/com_pixpublish/media/lib/messi/messi.min.css' );
		
		// Timepicker
		$doc->addScript( JUri::root().'administrator/components/com_pixpublish/media/lib/test/bootstrap-timepicker.min.js' );
		$doc->addStyleSheet( JUri::root().'administrator/components/com_pixpublish/media/lib/test/bootstrap-timepicker.min.css' );
		
		
		$this->addToolbar();
		
		$dispatcher = $this->importPlugins();

		PixPublishHelper::addSubmenu( $this->getName() );
		$results = $dispatcher->trigger( 'onRegisterSearchFilters' );
		$this->sidebar = JHtmlSidebar::render();
		
		JHtml::_('formbehavior.chosen', 'select');
		JHtml::_('behavior.keepalive');
		
		parent::display( $tpl );
	}
	
	protected function addToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_PIXPUBLISH_MANAGER_PANEL' ) );
	}
	
	/**
	 * @return JDispatcher
	 */
	protected function importPlugins()
	{
		JPluginHelper::importPlugin( 'pixpublish' );
		$dispatcher = JDispatcher::getInstance();
		return $dispatcher;
	}
}
