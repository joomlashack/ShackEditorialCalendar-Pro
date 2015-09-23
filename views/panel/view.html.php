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
		$doc->addScript( JUri::root().'administrator/components/com_pixpublish/media/lib/fullcalendar/fullcalendar.min.js','text/javascript' );
		$doc->addStyleSheet( JUri::root().'administrator/components/com_pixpublish/media/lib/fullcalendar/fullcalendar.css' );
		
		// Popup
		$doc->addScript( JUri::root().'administrator/components/com_pixpublish/media/lib/messi/messi.hacked.js' );
		$doc->addStyleSheet(JUri::root().'administrator/components/com_pixpublish/media/lib/messi/messi.hacked.min.css' );
		
		// Timepicker
		$doc->addScript( JUri::root().'administrator/components/com_pixpublish/media/lib/test/bootstrap-timepicker.min.js' );
		$doc->addStyleSheet( JUri::root().'administrator/components/com_pixpublish/media/lib/test/bootstrap-timepicker.min.css' );
		
		// Component
		$doc->addScript( JUri::root().'administrator/components/com_pixpublish/media/js/pixpublish.js' );
		$doc->addStyleSheet( JUri::root().'administrator/components/com_pixpublish/media/css/pixpublish.css' );
		$doc->addScriptDeclaration('var ADDNEW = "'.JText::_('COM_PIXPUBLISH_ADD_NEW').'";
									var EDIT = "'.JText::_('COM_PIXPUBLISH_EDIT').'";
									var PLUGIN = [];
									var SAVE = "'.JText::_('JSAVE').'";
									var CANCEL = "'.JText::_('JCANCEL').'";
									var JANUARY = "'.JText::_('JANUARY').'";
									var FEBRUARY = "'.JText::_('FEBRUARY').'";
									var MARCH = "'.JText::_('MARCH').'";
									var APRIL = "'.JText::_('APRIL').'";
									var MAY = "'.JText::_('MAY').'";
									var JUNE = "'.JText::_('JUNE').'";
									var JULY = "'.JText::_('JULY').'";
									var AUGUST = "'.JText::_('AUGUST').'";
									var SEPTEMBER = "'.JText::_('SEPTEMBER').'";
									var OCTOBER = "'.JText::_('OCTOBER').'";
									var NOVEMBER = "'.JText::_('NOVEMBER').'";
									var DECEMBER = "'.JText::_('DECEMBER').'";
									var JAN = "'.JText::_('JANUARY_SHORT').'";
									var FEB = "'.JText::_('FEBRUARY_SHORT').'";
									var MAR = "'.JText::_('MARCH_SHORT').'";
									var APR = "'.JText::_('APRIL_SHORT').'";
									var MAY = "'.JText::_('MAY_SHORT').'";
									var JUN = "'.JText::_('JUNE_SHORT').'";
									var JUL = "'.JText::_('JULY_SHORT').'";
									var AUG = "'.JText::_('AUGUST_SHORT').'";
									var SEP = "'.JText::_('SEPTEMBER_SHORT').'";
									var OCT = "'.JText::_('OCTOBER_SHORT').'";
									var NOV = "'.JText::_('NOVEMBER_SHORT').'";
									var DEC = "'.JText::_('DECEMBER_SHORT').'";
									var MONDAY = "'.JText::_('MONDAY').'";
									var TUESDAY = "'.JText::_('TUESDAY').'";
									var WEDNESDAY = "'.JText::_('WEDNESDAY').'";
									var THURSDAY = "'.JText::_('THURSDAY').'";
									var FRIDAY = "'.JText::_('FRIDAY').'";
									var SATURDAY = "'.JText::_('SATURDAY').'";
									var SUNDAY = "'.JText::_('SUNDAY').'";
									var MON = "'.JText::_('MON').'";
									var TUE = "'.JText::_('TUE').'";
									var WED = "'.JText::_('WED').'";
									var THU = "'.JText::_('THU').'";
									var FRI = "'.JText::_('FRI').'";
									var SAT = "'.JText::_('SAT').'";
									var SUN = "'.JText::_('SUN').'";
									var TODAY = "'.JText::_('JLIB_HTML_BEHAVIOR_TODAY').'";
									var MONTH = "'.JText::_('COM_PIXPUBLISH_MONTH').'";
									');

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
