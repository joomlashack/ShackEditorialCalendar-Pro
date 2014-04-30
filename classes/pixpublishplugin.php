<?php
/**
* @copyright	Copyright (C) 2013 Johan Sundell. All rights reserved.
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

