<?php
/**
* @copyright	Copyright (C) 2012 Johan Sundell. All rights reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

jimport('joomla.database.table');

class PixPublishTableArticle extends JTable
{
	function __construct(&$db)
	{
		parent::__construct( '#__pixpublish_articles', 'pixpublish_article_id', $db );
		
		//$this->access = (int) JFactory::getConfig()->get('access');
	}
	
	public function bind( $array, $ignore = '' )
	{
		// Bind the rules.
		if (isset($array['rules']) && is_array($array['rules']))
		{
			$rules = new JAccessRules($array['rules']);
			$this->setRules($rules);
		}
		return parent::bind( $array, $ignore );
	}
	
	public function check()
	{
		// Check the publish down date is not earlier than publish up.
		if( intval( $this->publish_down ) > 0 && $this->publish_down < $this->publish_up )
		{
			$this->setError( JText::_( 'JGLOBAL_START_PUBLISH_AFTER_FINISH' ) );
			return false;
		}
		return true;
	}
	
	public function store( $updateNulls = false )
	{
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();
		$k = $this->_tbl_key;
		if( (int)$this->$k )
		{
			$this->modified	= $date->toSql();
			$this->modified_by = $user->get( 'id' );
		}
		else
		{
			if( !intval( $this->created ) )
				$this->created = $date->toSql();
			if( empty( $this->created_by ) )
				$this->created_by = $user->get('id');
		}
		return parent::store( $updateNulls );
	}
	
	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form table_name.id
	 * where id is the value of the primary key of the table.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	protected function _getAssetName()
	{
		$extension = 'com_pixpublish';
		$k = $this->_tbl_key;
		return $extension.'.article.'.(int)$this->$k;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	protected function _getAssetTitle()
	{
		return $this->name;
	}
	
	protected function _getAssetParentId()
	{
		$asset = JTable::getInstance( 'Asset' );
		$asset->loadByName( 'com_pixpublish' );
		return $asset->id;
	}
}
