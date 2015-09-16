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

class PlgPixPublishContent extends PixPublishPlugin implements iPixPublishPlugin
{
	protected $autoloadLanguage = true;
	protected $item = null;
	
	/* Seems not to be triggered at all; see onRegisterSearchFilters */
	public function onPageLoad()
	{
		$doc->addScript( 'media/js/content.js' );
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
		
		if( $data->filter_access != '' )
		{
			$query->where( 'access = '.(int)$data->filter_access );
		}
		
		if( $data->filter_language != '' )
		{
			$query->where( 'language = '.$query->q( $data->filter_language ) );
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
			JForm::addFormPath( __DIR__ . '/form' );
			$form->loadFile( 'form', false );
				
			$form->setFieldAttribute( 'articletext', 'id', time() );
			
			if( (int)$id > 0 )
			{
				$db = JFactory::getDbo();
				$query = $db->getQuery( true );
				$query->select( 'tbl.id AS id, tbl.title AS title, tbl.publish_up AS start, tbl.state, tbl.introtext as articletext, tbl.alias, "'.$this->getName().'" as plugin' )
					->from( '#__content tbl' )
					->where( 'tbl.id = '.(int)$id );
				
				$result = $db->setQuery( $query )->loadObject();
			}
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
			$this->logThis( print_r( $data, true ) );
			
			$canEdit = $this->getAuth( $id )->get( 'core.edit' ); // create
			if( !$canEdit )
				$canEdit = $this->canEditOwn( $id );
			$canEditState = $this->canEditState( $id );
			
			if( !$this->getAuth()->get( 'core.create' ) && (int)$id == 0 )
				return false;
			
			if( !$canEdit && !$canEditState  && (int)$id > 0 )
				return false;
			
			$db = JFactory::getDbo();
			$query = $db->getQuery( true );
			if( (int)$id > 0 )
				$query->update( '#__content' );
			else
				$query->insert( '#__content' );
			
			$time = $data->start;
			$title = $data->title;
			$articletext = $data->articletext;
			$alias = $data->alias;

			if( $time )
			{
				$offset = JFactory::getConfig()->get('offset');
				$time = JFactory::getDate( $time, $offset )->format( 'H:i', false );
				if( $canEdit && (int)$id > 0 )
					$query->set( 'publish_up = TIMESTAMP( DATE( publish_up ),'.$query->q( $time ).' )' );
			}
			if( ($title && $canEdit) || ($title && (int)$id == 0) )
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
			}
			
			if( $canEditState )
				$query->set( 'state = '.(int)$data->state );
			
			if( (int)$id > 0 )
				$query->where( 'id = '.(int)$id );
			else
			{
				$query->set( 'publish_up = '.$query->q( $data->publish_up.' '.$time ) );
			}
			
			$this->logThis( (string)$query );
			
			if( !$db->setQuery($query)->execute() )
				return false;
		}
		return true;
	}
	
	/*public function onNewSaveOLD( $source, $id, $date, $data  )
	{

		if( $source === $this->getName() )
		{
			$article = JTable::getInstance('content');
			
			$article->id				= $data->id;
			$article->asset_id			= $data->asset_id;
			$article->title				= $data->title;
			$article->alias				= $data->alias;
			$article->introtext			= $data->articletext;
//			$article->fulltext			= $data->articletext;
			$article->state				= $data->state;
			$article->catid				= $data->catid;
//			$article->created			= $data->created;
//			$article->created_by		= $data->created_by;
//			$article->created_by_alias	= $data->created_by_alias;
			$article->publish_up		= $date.' '.$data->start;
//			$article->publish_down		= $data->publish_down;
			$article->images			= '\'{"image_intro":'.( !empty($data->images['image_intro']) ? $data->images['image_intro'] : '""' )
										.',"image_intro_alt":'.( !empty($data->images['image_intro_alt']) ? $data->images['image_intro_alt'] : '""' )
										.',"image_intro_caption":'.( !empty($data->images['image_intro_caption']) ? $data->images['image_intro_caption'] : '""' )
										.',"image_fulltext":'.( !empty($data->images['image_fulltext']) ? $data->images['image_fulltext'] : '""' )
										.',"image_fulltext_alt":'.( !empty($data->images['image_fulltext_alt']) ? $data->images['image_fulltext_alt'] : '""' )
										.',"image_fulltext_caption":'.( !empty($data->images['image_fulltext_caption']) ? $data->images['image_fulltext_caption'] : '""' )
										.',"float_fulltext":'.( !empty($data->images['float_fulltext']) ? $data->images['float_fulltext'] : '""' )
										.'}\'';
//			$article->urls				= $data->urls;
			$article->metakey			= $data->metakey;
			$article->metadesc			= $data->metadesc;
//			$article->access			= $data->access;
			$article->metadata			= '\'{"robots":'.( !empty($data->metadata['robots']) ? $data->metadata['robots'] : '""' )
									   	.',"author":'.( !empty($data->metadata['author']) ? $data->metadata['author'] : '""' )
										.',"rights":'.( !empty($data->metadata['rights']) ? $data->metadata['rights'] : '""' )
										.',"xreference":'.( !empty($data->metadata['xreference']) ? $data->metadata['xreference'] : '""' )
										.'}\'';
			$article->featured			= $data->featured;
			
			$user = JFactory::getUser();
			$article->language			= $user->getParam('language', '*');

			// Check to make sure our data is valid, raise notice if it's not.
			if (!$article->check()) {
				JError::raiseNotice(500, $article->getError());

				return false;
			}
			
			// Now store the article, raise notice if it doesn't get stored.
			if (!$article->store(TRUE)) {
				JError::raiseNotice(500, $article->getError());
 
				return false;
			}
			
			return true;
		}

		return false;
	}*/

	protected function getName()
	{
		return 'content';
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
		
		JFactory::getDocument()->addScriptDeclaration('var contentTypeName = "'.JText::_('PLG_PIXPUBLISH_CONTENT_TYPE_NAME').'";');
		JFactory::getDocument()->addScript( JUri::root().'plugins/pixpublish/content/media/js/content.js' );
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

