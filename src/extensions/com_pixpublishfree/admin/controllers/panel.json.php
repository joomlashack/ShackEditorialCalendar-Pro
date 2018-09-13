<?php
/**
 * @package         ShackEditorialCalendarfree
 * @copyright       Copyright (C) 2018. All rights reserved.
 * @link            https://www.joomlashack.com/joomla-extensions/shack-editorial-calendar/
 * @author       	You Rock AB 2003-2017 All Rights Reserved
 * @author       	2018, Joomlashack <help@joomlashack.com> - https://www.joomlashack.com.
 * @license         GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;


class PixPublishfreeControllerPanel extends JControllerLegacy
{
	public function getData()
	{
		$input = JFactory::getApplication()->input;
		$start = $input->getUint( 'start', 0 );
		$end = $input->getUint( 'end', 0 );
		$data = json_decode( $input->get( 'data', '', 'raw' ) );
		//$this->logThis( 'data: '.print_r( $data, true ) );
		$result = array();
		
		if( $start != 0 && $end != 0 )
		{
			/*$dispatcher = $this->importPlugins();
			$results = $dispatcher->trigger( 'onDataFetch', array( JDate::getInstance( $start ), JDate::getInstance( $end ), $data ) );
			
			$rows = array();
			foreach ($results as $result)
			{
				$rows = array_merge((array) $rows, (array) $result);
			}*/
			
			//$result = array();
			$start =  JDate::getInstance( $start );
			$stop = JDate::getInstance( $end );
			$db = JFactory::getDbo();
			$query = $db->getQuery( true );
			$query->select( 'tbl.id AS id, tbl.title AS title, tbl.publish_up AS start, tbl.state, "'.'contentfree'.'" as plugin' )
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
			
			//ColorFixer::$st_color = $this->params->get( 'background_colour', '#08C' ); // #3a87ad
			$result = $db->setQuery( $query )->loadObjectList( ''/*, 'ColorFixer'*/ );
			
			// Fix dates
			$result = self::fixDates( $result, 'start' );
		}
		echo json_encode( $result );
		JFactory::getApplication()->close();
	}
	
	protected static function fixDates( &$arr, $fieldname )
	{
		foreach( $arr as $row )
			$row->$fieldname = JFactory::getDate( $row->start, 'UTC' )->setTimezone( new DateTimeZone( self::getUserTimeoffset() ) )->format( 'Y-m-d H:i:s', true, false );
		return $arr;
	}
	
	protected static function getUserTimeoffset()
	{
		$config = JFactory::getConfig();
		$user = JFactory::getUser();
		return $user->getParam( 'timezone', $config->get('offset', 'UTC' ) );
	}
	
	public function updateEndTime()
	{
		JFactory::getApplication()->close();
	}
	
	public function move()
	{
		$input = JFactory::getApplication()->input;
		$id = $input->getCmd( 'id', '' );
		$source = $input->getCmd( 'plugin', '' );
		$dayd = $input->getInt( 'dayd', 0 );
		$mind = $input->getInt( 'mind', 0 );
		
		if( !$this->getAuth( $id )->get( 'core.edit' ) )
		{
			if( !$this->canEditOwn( $id ) )
				return false;
		}
			
		$db = JFactory::getDbo();
		$query = $db->getQuery( true );
		$query->update( '#__content' );
		$query->set( 'publish_up = DATE_ADD( ADDDATE( publish_up, '.(int)$dayd.' ), INTERVAL "'.(int)$mind.'" MINUTE )' );
		
		$query->where( 'id = '.(int)$id );
			
		$db->setQuery($query)->execute();
		
		//throw new Exception('Whoops, something happened!', 404);
		JFactory::getApplication()->close();
	}
	
	protected function getAuth( $id = 0 )
	{
		return PixPublishfreeHelper::getActions( 'com_content', 'article', $id );
	}
	
	public function edit()
	{
		$input = JFactory::getApplication()->input;
		$id = $input->getCmd( 'id', '' );
		$source = $input->getCmd( 'plugin', '' );
		$form = new JForm( 'com_pixpublishfree' );
		$extra = '';
		$db = JFactory::getDbo();
		$query = $db->getQuery( true );
		$query->select( 'tbl.id AS id, tbl.title AS title, tbl.publish_up AS start, tbl.state, tbl.introtext as articletext, tbl.language, tbl.access, tbl.catid, tbl.alias, "'.'contentfree'.'" as plugin' )
			->from( '#__content tbl' )
			->where( 'tbl.id = '.(int)$id );
			
		$result = $db->setQuery( $query )->loadObject();
			
		if( $result )
		{
			$arr = array( $result );
			$arr = self::fixDates( $arr, 'start' );
			$result = $arr[0];
		}
		
		// TODO: SUDDE Put all this on the normal component form
		JForm::addFieldPath( JPATH_ADMINISTRATOR.'/components/com_categories/models/fields' );
		JForm::addFieldPath( JPATH_ADMINISTRATOR.'/components/com_pixpublish/models/fields' );
		JForm::addFormPath( __DIR__ . '/../models/forms/' );
		$form->loadFile( 'form', false );
		
		$item = null;
		if( $result || (int)$id == 0 )
		{
			if( $result )
				$item = $result;
			
			if( $item != null || (int)$id == 0 )
			{
				$form->bind( $item );
				
				// Output form (XML fieldsets must have name attribute set!)
				echo '<form action="" method="post" id="pixsubmit_form">';
				$fieldsets = $form->getFieldsets();
				foreach( $fieldsets as $fieldset )
				{
					echo '<fieldset class="'.$fieldset->class.'">'.$form->renderFieldset( $fieldset->name ).'</fieldset>';
				}
				echo '</form>';

				$lines = array();
				$inits = array();
				foreach( $form->getFieldset() as $row )
				{
					if( $row->type ==  'fixed' )
					{
						$lines[] = $row->save();
						$inits[] = $row->getInit();
						//$this->logThis( print_r( $row->getInit(), true ) );
					}
				}
				if( count( $lines ) > 0 )
				{
					echo '<script type="text/javascript">';
					echo 'function toggleMe(){';
					foreach( $lines as $row )
						echo  $row;
					echo '};</script>';
				}
				foreach( $inits as $row )
					echo $row;
				
			}
		}
		else
		{
			throw new Exception('Whoops, something happened!', 500);
			JFactory::getApplication()->close();
		}
		JFactory::getApplication()->close();
	}
	
	public function save()
	{
		$input = JFactory::getApplication()->input;
		$id = $input->getCmd( 'id', '' );
		$source = $input->getCmd( 'plugin', '' );
		$data = json_decode( urldecode( $input->get( 'data', '', 'raw' ) ) );
		$this->logThis( urldecode( $input->get( 'data', '', 'raw' ) ) );
		
		$canEdit = $this->getAuth( $id )->get( 'core.edit' ); // create
		if( !$canEdit )
			$canEdit = $this->canEditOwn( $id );
		$canEditState = $this->canEditState( $id );
			
		if( (int)$id == 0 )
			return false;
			
		if( !$canEdit && !$canEditState )
			return false;
			
		$db = JFactory::getDbo();
		$query = $db->getQuery( true );
		$query->update( '#__content' );
			
		$time = $data->start;
		$title = $data->title;
		$articletext = $data->articletext;
		$alias = $data->alias;
		
		if( $time )
		{
			$time = JFactory::getDate( $time, self::getUserTimeoffset() )->format( 'H:i', false );
			if( $canEdit )
				$query->set( 'publish_up = TIMESTAMP( DATE( publish_up ),'.$query->q( $time ).' )' );
		}
		if( $title && $canEdit )
		{
			$query->set( 'title = '.$query->q( $title ) );
			$query->set( 'introtext = '.$query->q( $articletext ) );
		
			if( trim( $alias ) == '' )
			{
				if( JFactory::getConfig()->get( 'unicodeslugs' ) == 1 )
				{
					$alias = JFilterOutput::stringURLUnicodeSlug( $title );
				}
				else
				{
					$alias = JFilterOutput::stringURLSafe( $title );
				}
			}
			else
				$alias = JFilterOutput::stringURLSafe( $alias );
			$query->set( 'alias = '.$query->q( $alias ) );
			$query->set( 'language = '.$query->q( $data->language ) );
			$query->set( 'access = '.$query->q( $data->access ) );
			$query->set( 'catid = '.$query->q( $data->catid ) );
		}
			
		$user = JFactory::getUser();
		$query->set( 'modified = '.$query->q( JFactory::getDate( 'now', self::getUserTimeoffset() )->toSql() ) );
		$query->set( 'modified_by = '.(int)$user->id );
			
		if( $canEditState )
			$query->set( 'state = '.(int)$data->state );
			
		$query->where( 'id = '.(int)$id );
			
		if( trim( $title ) == '' )
			return;
		//$this->logThis( (string)$query );
			
		if( !$db->setQuery($query)->execute() )
		{
			return false;
		}
		
		JFactory::getApplication()->close();
	}
	
	protected function canEditOwn( $id )
	{
		if( $this->getAuth( $id )->get( 'core.edit.own' ) )
		{
			$created_by = (int)$this->getItem( $id )->created_by;
			$user = JFactory::getUser();
			$user_id = (int)$user->id;
			if( $user_id != 0 )
			{
				if( $user_id == $created_by )
					return true;
			}
			return false;
	
		}
		return false;
	}
	
	protected function canEditState( $id )
	{
		return JFactory::getUser()->authorise( 'core.edit.state', 'com_content.article.'.(int)$id );
	}
	
	protected function getItem( $id )
	{
		if( $this->item == null )
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery( true );
			$query->select( '*' )
			->from( '#__content' )
			->where( 'id = '.(int)$id );
			$this->item = $db->setQuery( $query )->loadObject();
		}
		return $this->item;
	}
	
	protected function logThis( $message )
	{
		/*jimport( 'joomla.log.log' );
	
		JLog::addLogger
			(
					array
					(
							'text_file' => 'com_pixpublishfree.log.php'
					),
					JLog::ALL,
					'com_pixpublishfree'
			);
		JLog::add( $message, JLog::WARNING, 'com_pixpublishfree' );*/
	}
}
