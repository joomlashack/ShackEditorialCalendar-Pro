<?php
/**
* @copyright	Copyright (C) 2012 Johan Sundell. All rights reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*/

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class PixPublishModelArticles extends JModelList
{
	protected function getListQuery()
	{
		$query = JFactory::getDbo()->getQuery( true );
		$query->select( 'sm.*' )
			->from( '#__pixpublish_articles sm' );
		return $query;
	}
}
