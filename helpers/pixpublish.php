<?php
/**
* @copyright	Copyright (C) 2012 Johan Sundell. All rights reserved.
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
	
	public static function addSubmenu( $submenu )
	{
		JSubMenuHelper::addEntry( JText::_( 'COM_PIXPUBLISH_SUBMENU_ARTICLES' ), 'index.php?option=com_pixpublish', $submenu == 'articles' );
		JSubMenuHelper::addEntry( JText::_( 'COM_PIXPUBLISH_SUBMENU_CATEGORIES' ), 'index.php?option=com_categories&view=categories&extension=com_pixpublish', $submenu == 'categories' );

		$document = JFactory::getDocument();
		
		if( $submenu == 'categories' )
		{
			$document->setTitle( JText::_( 'COM_PIXPUBLISH_ADMINISTRATION_CATEGORIES' ) );
		}
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param int $articleId
	 * @return JObject
	 */
	public static function getActions( $articleId = 0, $extension = 'com_pixpublish' )
	{
		jimport('joomla.access.access');
		$user	= JFactory::getUser();
		$result	= new JObject;
		$parts		= explode('.', $extension);
		$component	= $parts[0];
	
		if( $articleId == 0 )
		{
			$assetName = $component;
		}
		else
		{
			$assetName = $component.'.article.'.(int)$articleId;
		}
	
		$actions = JAccess::getActions( 'com_pixpublish', 'component' );

		foreach( $actions as $action )
		{
			$result->set( $action->name, $user->authorise( $action->name, $assetName ) );
		}
		
		return $result;
	}
}
