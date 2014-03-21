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
		JHtml::_( 'bootstrap.framework' );
		//JHtml::_('jquery.ui');
		
		$doc = JFactory::getDocument();
		// Fullcalendar
		$doc->addScript( JUri::root().'administrator/components/com_pixpublish/media/lib/jquery/jquery-ui.custom.min.js' );
		$doc->addScript( JUri::root().'administrator/components/com_pixpublish/media/lib/fullcalendar/fullcalendar.min.js' );
		$doc->addStyleSheet( JUri::root().'administrator/components/com_pixpublish/media/lib/fullcalendar/fullcalendar.css' );
		
		// Popup
		//$doc->addScript( JUri::root().'administrator/components/com_pixpublish/media/lib/gristmill-jquery-popbox/popbox_fixed.min.js' );
		//$doc->addStyleSheet( JUri::root().'administrator/components/com_pixpublish/media/lib/gristmill-jquery-popbox/popbox.css' );
		//$doc->addScript( JUri::root().'administrator/components/com_pixpublish/media/lib/messi/messi.js' );
		//$doc->addScript( JUri::root().'administrator/components/com_pixpublish/media/lib/messi/messi.min.js' );
		$doc->addScript( JUri::root().'administrator/components/com_pixpublish/media/lib/messi/messi.hacked.js' );
		$doc->addStyleSheet(JUri::root().'administrator/components/com_pixpublish/media/lib/messi/messi.min.css' );
		
		//$doc->addScript( JUri::root().'administrator/components/com_pixpublish/media/lib/jquery/jquery.ui.datepicker.min.js' );
		//$doc->addScript( JUri::root().'administrator/components/com_pixpublish/media/lib/jquery/jquery.ui.slider.min.js' );
		
		//$doc->addScript( JUri::root().'administrator/components/com_pixpublish/media/lib/timepicker/jquery-ui-timepicker-addon.js' );
		//$doc->addStyleSheet( JUri::root().'administrator/components/com_pixpublish/media/lib/timepicker/jquery-ui-timepicker-addon.css' );
		
		// TEST
		$doc->addScript( JUri::root().'administrator/components/com_pixpublish/media/lib/test/bootstrap-timepicker.min.js' );
		$doc->addStyleSheet( JUri::root().'administrator/components/com_pixpublish/media/lib/test/bootstrap-timepicker.min.css' );
		
		
		$this->addToolbar();
		
		$dispatcher = $this->importPlugins();
		$results = $dispatcher->trigger( 'onRegisterSearch' );
		//print_r( $results ); die();
		$rows = array();
		foreach ($results as $result)
		{
			$rows = array_merge((array) $rows, (array) $result);
		}
		//print_r( $rows ); die();
		
		parent::display( $tpl );
	}
	
	protected function addToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_PIXPUBLISH_MANAGER_PANEL' ) );
	}
	
	// TODO: Move this to the model
	/**
	 * @return JDispatcher
	 */
	protected function importPlugins()
	{
		JPluginHelper::importPlugin( 'pixsubmit' );
		$dispatcher =& JDispatcher::getInstance();
		return $dispatcher;
	}
}
