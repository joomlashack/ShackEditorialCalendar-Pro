<?php
/**
* @copyright	Copyright (C) 2014 Johan Sundell. All rights reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*/

// No direct access.
defined('_JEXEC') or die;

JLoader::import( 'pixpublishplugin', JPATH_COMPONENT_ADMINISTRATOR.'/classes' );

class PlgPixPublishModule extends PixPublishPlugin implements iPixPublishPlugin
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
		$query->select( 'tbl.id AS id, tbl.title AS title, tbl.publish_up AS start, tbl.published, "'.$this->getName().'" as plugin' )
			->from( '#__modules tbl' )
			->where( 'tbl.publish_up >= '.$query->q( $start->toSql() ) )
			->where( 'tbl.publish_up <= '.$query->q( $stop->toSql() ) );
		
		if( $data->filter_position != '' )
			$query->where( 'tbl.position = '.$query->q( $data->filter_position ) );
		
		ColorFixer::$st_color = $this->params->get( 'background_colour', 'green' );
		$result = $db->setQuery( $query )->loadObjectList( '', 'ColorFixer' );
		
		// Fix dates
		$result = self::fixDates( $result, 'start' );
		return $result;
	}
	
	public function onItemMove( $source, $id, $dayd, $mind )
	{
		if( $source === $this->getName() )
		{
			$this->logThis( 'Got to module onItemMove' );
			$db = JFactory::getDbo();
			$query = $db->getQuery( true );
			$query->update( '#__modules' )
				->set( 'publish_up = DATE_ADD( ADDDATE( publish_up, '.(int)$dayd.' ), INTERVAL "'.(int)$mind.'" MINUTE )' )
				->set( 'publish_down = DATE_ADD( ADDDATE( publish_down, '.(int)$dayd.' ), INTERVAL "'.(int)$mind.'" MINUTE )' )
				->where( 'id = '.(int)$id );
			
			$this->logThis( (string)$query );
			if( !$db->setQuery($query)->execute() )
				return false;
		}
		return true;
	}
	
	public function onGetDialog( $source, $id, $form, &$extra )
	{
		if( $source === $this->getName() )
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery( true );
			$query->select( 'tbl.id AS id, tbl.title AS title, tbl.publish_up AS start, tbl.published as state, "'.$this->getName().'" as plugin, tbl.position' )
				->from( '#__modules tbl' )
				->where( 'tbl.id = '.(int)$id );
			$result =  $db->setQuery( $query )->loadObject();
			
			JLoader::import( 'modules', JPATH_ADMINISTRATOR.'/components/com_modules/helpers' );
			
			// Copy ;)
			require_once JPATH_ADMINISTRATOR . '/components/com_templates/helpers/templates.php';
			
			JHtml::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_modules/'.'/helpers/html' );
			$clientId       = 0;
			$state          = 1;
			$selectedPosition = $result->position;
			$positions = JHtml::_('modules.positions', $clientId, $state, $selectedPosition);
			
			// Add custom position to options
			$customGroupText = JText::_('COM_MODULES_CUSTOM_POSITION');
			
			// Build field
			$attr = array(
					'id'          => 'filter_edit_position',
					'list.select' => $result->position,
					'list.attr'   => 'class="chzn-custom-value input-xlarge" '
					. 'data-custom_group_text="' . $customGroupText . '" '
					. 'data-no_results_text="' . JText::_('COM_MODULES_ADD_CUSTOM_POSITION') . '" '
					. 'data-placeholder="' . JText::_('COM_MODULES_TYPE_OR_SELECT_POSITION') . '" '
			);
			
			//echo JHtml::_('select.groupedlist', $positions, 'jform[position]', $attr);
			// end Copy ;)
			
			$extra = JHtml::_('select.groupedlist', $positions, 'filter_edit_position', $attr);
			
			$arr = array( $result );
			$arr = self::fixDates( $arr, 'start' );
			$result = $arr[0];
			
			return $result;
		}
	}
	
	public function onItemSave( $source, $id, $data  )
	{
		if( $source === $this->getName() )
		{
			$this->logThis( print_r( $data, true ) );
			$db = JFactory::getDbo();
			$query = $db->getQuery( true );
			$query->update( '#__modules' );
				
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
			
			if( (int)$data->filter_status == 1 )
				$query->set( 'published = '.(int)$data->filter_status );
			else
				$query->set( 'published = 0' );
			
			if( $data->filter_edit_position != '' )
				$query->set( 'position = '.$query->q( $data->filter_edit_position ) );
			
			$query->where( 'id = '.(int)$id );
				
			$this->logThis( print_r( $data, true ) );
			$this->logThis( (string)$query );
			if( !$db->setQuery($query)->execute() )
				return false;
		}
		return true;
	}
	
	public function onRegisterSearchFilters()
	{
		JLoader::import( 'modules', JPATH_ADMINISTRATOR.'/components/com_modules/helpers' );
		
		JHtmlSidebar::addFilter(
			JText::_('PLG_PIXPUBLISH_MODULE_SEARCH_POSITION'),
			'filter_position',
			JHtml::_('select.options', ModulesHelper::getPositions( 0 ), 'value', 'text' )
		);
	}
	
	public function onItemReseize( $source, $id, $dayd, $mind )
	{
		if( $source === $this->getName() )
		{
			$this->logThis( 'Got to module onItemReseize' );
			$db = JFactory::getDbo();
			$query = $db->getQuery( true );
			$query->update( '#__modules' )
				->set( 'publish_down = DATE_ADD( ADDDATE( publish_down, '.(int)$dayd.' ), INTERVAL "'.(int)$mind.'" MINUTE )' )
				->where( 'id = '.(int)$id );
			
			$this->logThis( (string)$query );
			if( !$db->setQuery($query)->execute() )
				return false;
		}
		return true;
	}
	
	protected function getName()
	{
		return 'module';
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


