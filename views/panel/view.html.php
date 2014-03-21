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
