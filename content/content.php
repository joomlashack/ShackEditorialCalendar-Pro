<?php
/**
* @copyright	Copyright (C) 2014 Johan Sundell. All rights reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*/

// No direct access.
defined('_JEXEC') or die;

JLoader::import( 'pixpublishplugin', JPATH_COMPONENT_ADMINISTRATOR.'/classes' );

class PlgPixPublishContent extends PixPublishPlugin implements iPixPublishPlugin
{
	protected $autoloadLanguage = true;
	
	/**
	 *
	 * @param JDate $start
	 * @param JDate $stop
	 */
	public function onDataFetch( $start, $stop, $data )
	{
		$result = array();
		$db = JFactory::getDbo();
		$query = $db->getQuery( true );
		$query->select( 'tbl.id AS id, tbl.title AS title, tbl.publish_up AS start, tbl.state, "'.$this->getName().'" as plugin' )
			->from( '#__content tbl' )
			->where( 'tbl.publish_up >= '.$query->q( $start->toSql() ) )
			->where( 'tbl.publish_up <= '.$query->q( $stop->toSql() ) );
		if( $data->filter_state != '' )
		{
			$query->where( 'state = '.(int)$data->filter_state );
		}
		
		if( $data->filter_category_id != '' )
		{
			$query->where( 'catid = '.(int)$data->filter_category_id );
		}
		
		if( $data->filter_access != '' )
		{
			// TODO: Add the query for access
		}
		
		if( $data->filter_language != '' )
		{
			$query->where( 'language = '.$query->q( $data->filter_language ) );
		}
		
		ColorFixer::$st_color = $this->params->get( 'background_colour', '#3a87ad' );
		$result = $db->setQuery( $query )->loadObjectList( '', 'ColorFixer' );
		
		// Fix dates
		$result = self::fixDates( $result, 'start' );
		
		return $result;
	}

	// TODO: Remove title, not used anymore
	public function onItemMove( $source, $id, $dayd, $mind )
	{
		if( $source === $this->getName() )
		{
			if( !$this->getAuth( $id )->get( 'core.edit' ) /*&& !$this->getAuth( $id )->get( 'core.edit.own' )*/ )
				return false;
			
			$this->logThis( 'Got to content onItemMove' );
			$db = JFactory::getDbo();
			$query = $db->getQuery( true );
			$query->update( '#__content' );
			$query->set( 'publish_up = DATE_ADD( ADDDATE( publish_up, '.(int)$dayd.' ), INTERVAL "'.(int)$mind.'" MINUTE )' );

			$query->where( 'id = '.(int)$id );
			
			// TODO: query -> fetch -> check
			/*if( $this->getAuth( $id )->get( 'core.edit.own' ) )
				$query->where( 'created_by = '.(int)JFactory::getUser()->get( 'id' ) );*/
			
			$this->logThis( (string)$query );
			if( !$db->setQuery($query)->execute() )
				return false;
		}
		return true;
	}
	
	public function onGetDialog( $source, $id, &$html, &$extra )
	{
		if( $source === $this->getName() )
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery( true );
			$query->select( 'tbl.id AS id, tbl.title AS title, tbl.publish_up AS start, tbl.state, "'.$this->getName().'" as plugin' )
				->from( '#__content tbl' )
				->where( 'tbl.id = '.(int)$id );
			
			$result = $db->setQuery( $query )->loadObject();
			
			if( $result )
			{
				$arr = array( $result );
				$arr = self::fixDates( $arr, 'start' );
				$result = $arr[0];
			}
			return $result;
		}
	}
	
	public function onItemSave( $source, $id, $data  )
	{
		if( $source === $this->getName() )
		{
			if( !$this->getAuth( $id )->get( 'core.edit' ) /*&& !$this->getAuth( $id )->get( 'core.edit.own' )*/ )
				return false;
			
			$db = JFactory::getDbo();
			$query = $db->getQuery( true );
			$query->update( '#__content' );
			
			$time = $data->pixtest_start;
			$title = $data->pixtest_title;

			if( $time )
			{
				$offset = JFactory::getConfig()->get('offset');
				$time = JFactory::getDate( $time, $offset )->format( 'H:i', false );
				$query->set( 'publish_up = TIMESTAMP( DATE( publish_up ),'.$query->q( $time ).' )' );
			}
			if( $title )
				$query->set( 'title = '.$query->q( $title ) );
			
			// Fix the auth core.edit.state
			$query->set( 'state = '.(int)$data->filter_status );
			
			$query->where( 'id = '.(int)$id );
			
			// TODO: Fix this so that it works
			/*if( $this->getAuth( $id )->get( 'core.edit.own' ) )
				$query->where( 'created_by = '.(int)JFactory::getUser()->get( 'id' ) );*/
			
			$this->logThis( print_r( $data, true ) );
			$this->logThis( (string)$query );
			if( !$db->setQuery($query)->execute() )
				return false;
		}
		return true;
	}
	
	protected function getName()
	{
		return 'content';
	}
	
	protected function getAuth( $id = 0 )
	{
		return PixPublishHelper::getActions( 'com_content', 'article', $id );
	}
	
	public function onRegisterSearchFilters()
	{
		JHtmlSidebar::addFilter(
			JText::_('PLG_PIXPUBLISH_CONTENT_CATEGORY_LABEL'),
			'filter_category_id',
			JHtml::_('select.options', JHtml::_('category.options', 'com_content'), 'value', 'text', '' )
		);
	}
	
	protected function logThis( $message )
	{
		jimport( 'joomla.log.log' );
	
		JLog::addLogger
		(
				array
				(
						'text_file' => 'com_pixpublish.log.php'
				),
				JLog::ALL,
				'com_pixpublish'
		);
		JLog::add( $message, JLog::WARNING, 'com_pixpublish' );
	}
}

