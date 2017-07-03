<?php
/**
* @author		Johan Sundell <labs@pixpro.net>
* @link			https://www.pixpro.net/labs
* @copyright	Copyright © You Rock AB 2003-2017 All Rights Reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*/

// No direct access.
defined('_JEXEC') or die;

interface iPixPublishPlugin
{
	public function onDataFetch( $start, $stop, $data );
	
	public function onItemMove( $source, $id, $dayd, $mind );
	
	public function onGetDialog( $source, $id, $form );
	
	public function onItemSave( $source, $id, $data  );
	
	public function onRegisterSearchFilters();
}

abstract class PixPublishPlugin extends JPlugin
{
	protected static function fixDates( &$arr, $fieldname )
	{
		foreach( $arr as $row )
			$row->$fieldname = JFactory::getDate( $row->start, 'UTC' )->setTimezone( new DateTimeZone( self::getUserTimeoffset() ) )->format( 'Y-m-d H:i:s', true, false );
		return $arr;
	}
	
	public function getInfoText()
	{
		return '';
	}
	
	protected static function getUserTimeoffset()
	{
		$config = JFactory::getConfig();
		$user = JFactory::getUser();
		return $user->getParam( 'timezone', $config->get('offset', 'UTC' ) );
	}
}

class ColorFixer
{
	public static $st_color = '';
	public $color;
	
	public function __construct()
	{
		$this->color = self::$st_color;
	}
}
