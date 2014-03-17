<?php
/**
* @copyright	Copyright (C) 2012 Johan Sundell. All rights reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

class PixPublishControllerArticles extends JControllerAdmin
{
	public function getModel( $name = 'Article', $prefix = 'PixPublishModel', $config = array( 'ignore_request' => true ) )
	{
		$model = parent::getModel( $name, $prefix, $config );
		return $model;
	}
}
