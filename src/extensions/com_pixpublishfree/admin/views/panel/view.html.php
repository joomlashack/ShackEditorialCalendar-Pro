<?php
/**
 * @package         PixPublishfree
 * @author          Johan Sundell <labs@pixpro.net>
 * @link            http://www.pixpro.net/labs
 * @copyright       Copyright © You Rock AB 2003-2017 All Rights Reserved.
 * @license         GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

class PixPublishfreeViewPanel extends JViewLegacy
{

	public function display( $tpl = null )
	{
		JHtml::_( 'jquery.framework', true );
		JHtml::_( 'bootstrap.framework' );
		JHtml::_('formbehavior.chosen', 'select');
		JHtml::_('behavior.keepalive');
		
		
		$doc = JFactory::getDocument();
		// Fullcalendar
		$doc->addScript( JUri::root().'administrator/components/com_pixpublishfree/media/lib/jquery/jquery-ui.custom.min.js' );
		$doc->addScript( JUri::root().'administrator/components/com_pixpublishfree/media/lib/fullcalendar/fullcalendar.min.js','text/javascript' );
		$doc->addStyleSheet( JUri::root().'administrator/components/com_pixpublishfree/media/lib/fullcalendar/fullcalendar.css' );
		
		// Popup
		$doc->addScript( JUri::root().'administrator/components/com_pixpublishfree/media/lib/messi/messi.hacked.js' );
		$doc->addStyleSheet(JUri::root().'administrator/components/com_pixpublishfree/media/lib/messi/messi.hacked.min.css' );
		
		// Timepicker
		$doc->addScript( JUri::root().'administrator/components/com_pixpublishfree/media/lib/test/bootstrap-timepicker.min.js' );
		$doc->addStyleSheet( JUri::root().'administrator/components/com_pixpublishfree/media/lib/test/bootstrap-timepicker.min.css' );
		
		// Component
		$doc->addScript( JUri::root().'administrator/components/com_pixpublishfree/media/js/pixpublishfree.js' );
		$doc->addStyleSheet( JUri::root().'administrator/components/com_pixpublishfree/media/css/pixpublishfree.css' );
		$doc->addScriptDeclaration('var PLUGIN = [];');
		
		// Language strings
		JText::Script('COM_PIXPUBLISHFREE_ADD_NEW');
		JText::Script('COM_PIXPUBLISHFREE_EDIT');
		JText::Script('JSAVE');
		JText::Script('JCANCEL');
		JText::Script('JANUARY');
		JText::Script('FEBRUARY');
		JText::Script('MARCH');
		JText::Script('APRIL');
		JText::Script('MAY');
		JText::Script('JUNE');
		JText::Script('JULY');
		JText::Script('AUGUST');
		JText::Script('SEPTEMBER');
		JText::Script('OCTOBER');
		JText::Script('NOVEMBER');
		JText::Script('DECEMBER');
		JText::Script('JANUARY_SHORT');
		JText::Script('FEBRUARY_SHORT');
		JText::Script('MARCH_SHORT');
		JText::Script('APRIL_SHORT');
		JText::Script('MAY_SHORT');
		JText::Script('JUNE_SHORT');
		JText::Script('JULY_SHORT');
		JText::Script('AUGUST_SHORT');
		JText::Script('SEPTEMBER_SHORT');
		JText::Script('OCTOBER_SHORT');
		JText::Script('NOVEMBER_SHORT');
		JText::Script('DECEMBER_SHORT');
		JText::Script('MONDAY');
		JText::Script('TUESDAY');
		JText::Script('WEDNESDAY');
		JText::Script('THURSDAY');
		JText::Script('FRIDAY');
		JText::Script('SATURDAY');
		JText::Script('SUNDAY');
		JText::Script('MON');
		JText::Script('TUE');
		JText::Script('WED');
		JText::Script('THU');
		JText::Script('FRI');
		JText::Script('SAT');
		JText::Script('SUN');
		JText::Script('JLIB_HTML_BEHAVIOR_TODAY');
		JText::Script('COM_PIXPUBLISHFREE_MONTH');

		$this->addToolbar();

		PixPublishfreeHelper::addSubmenu( $this->getName() );
		$this->sidebar = JHtmlSidebar::render();
		
		JFactory::getDocument()->addScriptDeclaration('PLUGIN["contentfree"] = "'.JText::_('PLG_PIXPUBLISHFREE_CONTENT_TYPE_NAME').'";');
		
		parent::display( $tpl );
	}
	
	protected function addToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_PIXPUBLISHFREE_MANAGER_PANEL' ), 'calendar' );
	}
}
