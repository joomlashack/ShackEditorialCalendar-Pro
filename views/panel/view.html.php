<?php
/**
 * @package         PixPublish
 * @author          Johan Sundell <johan@pixpro.net>
 * @link            http://www.pixpro.net/labs
 * @copyright       Copyright ©2014-2015 Pixpro Stockholm AB All Rights Reserved.
 * @license         GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

class PixPublishViewPanel extends JViewLegacy
{

	public function display( $tpl = null )
	{
		JHtml::_( 'jquery.framework', true );
		JHtml::_( 'bootstrap.framework' );
		JHtml::_('formbehavior.chosen', 'select');
		JHtml::_('behavior.keepalive');
		
		
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
		
		// Component
		$doc->addScript( JUri::root().'administrator/components/com_pixpublish/media/js/pixpublish.js' );
		$doc->addStyleSheet( JUri::root().'administrator/components/com_pixpublish/media/css/pixpublish.css' );
		$doc->addScriptDeclaration('var addNew = "'.JText::_('COM_PIXPUBLISH_ADD_NEW').'";');

		$this->addToolbar();
		
		$dispatcher = $this->importPlugins();

		PixPublishHelper::addSubmenu( $this->getName() );
		$results = $dispatcher->trigger( 'onRegisterSearchFilters' );
		$this->sidebar = JHtmlSidebar::render();
		
		$infotexts = $dispatcher->trigger( 'getInfoText' );
		$this->infotexts = $infotexts;
		
		parent::display( $tpl );
	}
	
	protected function addToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_PIXPUBLISH_MANAGER_PANEL' ), 'calendar' );
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
