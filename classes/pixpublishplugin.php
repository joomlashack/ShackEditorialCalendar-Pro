<?php
/**
* @copyright	Copyright (C) 2014 Johan Sundell. All rights reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*/

// No direct access.
defined('_JEXEC') or die;

/*abstract class*/ interface PixPublishPlugin //extends JPlugin
{
	/*abstract*/ public function onDataFetch( $start, $stop, $data );
	
	public function onItemMove( $source, $id, $dayd, $mind );
	
	public function onGetDialog( $source, $id, &$html, &$extra );
	
	public function onItemSave( $source, $id, $data  );
	
	public function onRegisterSearchFilters();
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
