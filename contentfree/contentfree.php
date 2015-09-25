<?php
/**
 * @package         PixPublish
 * @author          Johan Sundell <johan@pixpro.net>
 * @link            http://www.pixpro.net/labs
 * @copyright       Copyright ©2014-2015 Pixpro Stockholm AB All Rights Reserved.
 * @license         GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

JLoader::import( 'pixpublishplugin', JPATH_COMPONENT_ADMINISTRATOR.'/classes' );

class PlgPixPublishContentFree extends PixPublishPlugin implements iPixPublishPlugin
{
	protected $autoloadLanguage = true;
	protected $item = null;
	
	public function onPageLoad()
	{
	}
		
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
		
		ColorFixer::$st_color = $this->params->get( 'background_colour', '#08C' ); // #3a87ad
		$result = $db->setQuery( $query )->loadObjectList( '', 'ColorFixer' );
		
		// Fix dates
		$result = self::fixDates( $result, 'start' );
		
		return $result;
	}

	public function onItemMove( $source, $id, $dayd, $mind )
	{
		if( $source === $this->getName() )
		{
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
			
			if( !$db->setQuery($query)->execute() )
				return false;
		}
		return true;
	}
	
	public function onGetDialog( $source, $id, $form )
	{
		if( $source === $this->getName() )
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery( true );
			$query->select( 'tbl.id AS id, tbl.title AS title, tbl.publish_up AS start, tbl.state, tbl.introtext as articletext, tbl.language, tbl.access, tbl.catid, tbl.alias, "'.$this->getName().'" as plugin' )
				->from( '#__content tbl' )
				->where( 'tbl.id = '.(int)$id );
			
			$result = $db->setQuery( $query )->loadObject();
			
			if( $result )
			{
				$arr = array( $result );
				$arr = self::fixDates( $arr, 'start' );
				$result = $arr[0];
			}
			
			JForm::addFieldPath( JPATH_ADMINISTRATOR.'/components/com_categories/models/fields' );
			JForm::addFieldPath( JPATH_ADMINISTRATOR.'/components/com_pixpublish/models/fields' );
			JForm::addFormPath( __DIR__ . '/form' );
			$form->loadFile( 'form', false );
			
			return $result;
		}
	}

	public function onItemSave( $source, $id, $data  )
	{
		if( $source === $this->getName() )
		{
			$this->logThis( print_r( $data, true ) );
			
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
			$this->logThis( (string)$query );
			
			if( !$db->setQuery($query)->execute() )
			{
				return false;
			}
		}
		return true;
	}

	protected function getName()
	{
		return 'contentfree';
	}
	
	protected function getAuth( $id = 0 )
	{
		return PixPublishHelper::getActions( 'com_content', 'article', $id );
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
	
	public function onRegisterSearchFilters()
	{
		JHtmlSidebar::addFilter(
			JText::_('PLG_PIXPUBLISH_CONTENT_CATEGORY_LABEL'),
			'filter_category_id',
			JHtml::_('select.options', JHtml::_('category.options', 'com_content'), 'value', 'text', '' )
		);
		
		JFactory::getDocument()->addScriptDeclaration('PLUGIN["contentfree"] = "'.JText::_('PLG_PIXPUBLISH_CONTENT_TYPE_NAME').'";');
	}
	
	public function getInfoText()
	{
		return JText::_( 'PLG_PIXPUBLISH_CONTENTFREE_INFOTEXT' );
	}
	
	protected function logThis( $message )
	{
		/*jimport( 'joomla.log.log' );
	
		JLog::addLogger
		(
				array
				(
						'text_file' => 'com_pixpublish.log.php'
				),
				JLog::ALL,
				'com_pixpublish'
		);
		JLog::add( $message, JLog::WARNING, 'com_pixpublish' );*/
	}
}

