<?php
/**
* @author		Johan Sundell <labs@pixpro.net>
* @link			https://www.pixpro.net/labs
* @copyright	Copyright © You Rock AB 2003-2017 All Rights Reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*/

// No direct access.
defined('_JEXEC') or die;

abstract class PixPublishHelper
{
	public static function isEnabled()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery( true );
		$query->select( 'enabled' )
			->from( '#__extensions' )
			->where( 'folder = "system"' )
			->where( 'element = "pixpublish"' );
		$db->setQuery( $query );
		
		$result = (boolean) $db->loadResult();
		return $result;
	}
	
	public static function addSubmenu( $view )
	{
		if( JVERSION >= 3.0 )
		{
			JHtmlSidebar::addEntry( JText::_('COM_PIXPUBLISH_VIEW_PANEL'), 'index.php?option=com_pixpublish&view=panel', $view == 'panel' );
			
			JHtmlSidebar::addFilter(
					JText::_('JOPTION_SELECT_PUBLISHED'),
					'filter_state',
					JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('all'=>false) ), 'value', 'text', '', true)
			);
			
			JHtmlSidebar::addFilter(
					JText::_('JOPTION_SELECT_ACCESS'),
					'filter_access',
					JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', '' )
			);
			
			JHtmlSidebar::addFilter(
					JText::_('JOPTION_SELECT_LANGUAGE'),
					'filter_language',
					JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', '' )
			);
		}
	}
	
	public static function getActions( $component = '', $section = '', $id = 0 )
	{
		jimport('joomla.access.access');
		
		$user	= JFactory::getUser();
		$result	= new JObject;
		
		if( $section && $id )
		{
			$assetName = $component.'.'.$section.'.'.(int)$id;
		}
		else
		{
			$assetName = $component;
		}
		
		$actions = JAccess::getActions( $component, 'component' );
		
		foreach( $actions as $action )
		{
			$result->set( $action->name, $user->authorise( $action->name, $assetName ) );
		}
		return $result;
	}
}
