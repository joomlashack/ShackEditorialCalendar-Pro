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

abstract class PixPublishfreeHelper
{
	public static function isEnabled()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery( true );
		$query->select( 'enabled' )
			->from( '#__extensions' )
			->where( 'folder = "system"' )
			->where( 'element = "pixpublishfree"' );
		$db->setQuery( $query );
		
		$result = (boolean) $db->loadResult();
		return $result;
	}
	
	public static function addSubmenu( $view )
	{
		if( JVERSION >= 3.0 )
		{
			JHtmlSidebar::addEntry( JText::_('COM_PIXPUBLISHFREE_VIEW_PANEL'), 'index.php?option=com_pixpublishfree&view=panel', $view == 'panel' );
			
			JHtmlSidebar::addFilter(
					JText::_('JOPTION_SELECT_PUBLISHED'),
					'filter_state',
					JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('all'=>false) ), 'value', 'text', '', true)
			);
			
			JHtmlSidebar::addFilter(
					JText::_('COM_PIXPUBLISHFREE_CONTENT_CATEGORY_LABEL'),
					'filter_category_id',
					JHtml::_('select.options', JHtml::_('category.options', 'com_content'), 'value', 'text', '' )
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
