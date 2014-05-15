<?php
/**
* @copyright	Copyright (C) 2014 Johan Sundell. All rights reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*/

// No direct access.
defined('_JEXEC') or die;

interface iPixPublishPlugin
{
	public function onDataFetch( $start, $stop, $data );
	
	public function onItemMove( $source, $id, $dayd, $mind );
	
	public function onGetDialog( $source, $id, &$html, &$extra );
	
	public function onItemSave( $source, $id, $data  );
	
	public function onRegisterSearchFilters();
}

abstract class PixPublishPlugin extends JPlugin
{
	protected static function fixDates( &$arr, $fieldname )
	{
		$config = JFactory::getConfig();
		$user = JFactory::getUser();
		foreach( $arr as $row )
			$row->$fieldname = JFactory::getDate( $row->start, 'UTC' )->setTimezone( new DateTimeZone( $config->get( 'offset' ) ) )->format( 'Y-m-d H:i:s', true, false );
		return $arr;
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
