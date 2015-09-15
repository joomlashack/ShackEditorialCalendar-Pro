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
			
			JForm::addFormPath( __DIR__ . '/form' );
			$form->loadFile( 'form', false );
			
			if( $form->setFieldAttribute( 'articletext', 'id', time() ) )
				$this->logThis( 'field not found' );
			
			return $result;
		}
	}

	public function onItemSave( $source, $id, $data  )
	{
		if( $source === $this->getName() )
		{
			$canEdit = $this->getAuth( $id )->get( 'core.edit' );
			if( !$canEdit )
				$canEdit = $this->canEditOwn( $id );
			$canEditState = $this->canEditState( $id );
			
			if( !$canEdit && !$canEditState )
				return false;
			
			$db = JFactory::getDbo();
			$query = $db->getQuery( true );
			$query->update( '#__content' );
			
			$time = $data->start;
			$title = $data->title;

			if( $time )
			{
				$offset = JFactory::getConfig()->get('offset');
				$time = JFactory::getDate( $time, $offset )->format( 'H:i', false );
				if( $canEdit )
					$query->set( 'publish_up = TIMESTAMP( DATE( publish_up ),'.$query->q( $time ).' )' );
			}
			if( $title && $canEdit )
				$query->set( 'title = '.$query->q( $title ) );
			
			if( $canEditState )
				$query->set( 'state = '.(int)$data->state );
			
			$query->where( 'id = '.(int)$id );
			if( !$db->setQuery($query)->execute() )
				return false;
		}
		return true;
	}
	
	public function onCreateNew( $source, $id, $form )
	{
		return $this->onGetDialog( $source, $id, $form );
	}

	public function onCreateNewTEST( $source, $id, $form )
	{
/*
		JForm::addFieldPath( JURI::Base().'components/com_categories/models/fields/categoryedit.php' );
		Not working, using custom class with require_once()
*/
		if( $source === $this->getName() )
		{
			JForm::addFormPath( __DIR__ . '/form' );
			$form->loadFile( 'create', false );
	
			$html = '';
			$html .= '<form action="" method="post" id="pixsubmit_form">';
	
			$fieldsets = $form->getFieldsets();
	
			if( !empty($fieldsets) && $form->getAttribute('tabs') == "true" )
			{
				$active_fieldset = reset($fieldsets);
				$active_fieldset = 'pp-'.$active_fieldset->name;
				$active_fieldset = $form->getAttribute('active-tab', $active_fieldset);
				$html .= JHtml::_('bootstrap.startTabSet', 'pixTab', array('active' => $active_fieldset));
			}
	
			foreach ($form->getFieldsets() as $fieldset)
			{
				$fields = $form->getFieldset($fieldset->name);
	
				if( $form->getAttribute('tabs') == "true" )
				{
					$html .= JHtml::_('bootstrap.addTab', 'pixTab', 'pp-'.$fieldset->name, $fieldset->label);
				}
	
				if (isset($fieldset->class))
				{
					$class = 'class="' . $fieldset->class . '"';
				}
				else
				{
					$class = '';
				}
	
				$html .= "\t" . '<div ' . $class . '>' . PHP_EOL;
	
				if (isset($fieldset->label) && !empty($fieldset->label) && $form->getAttribute('tabs') != "true" )
				{
					$html .= "\t\t" . '<h3>' . JText::_($fieldset->label) . '</h3>' . PHP_EOL;
				}
	
				foreach ($fields as $field)
				{
					$required	 = $field->required;
					$labelClass	 = $field->labelClass;
					$groupClass	 = $form->getFieldAttribute($field->fieldname, 'groupclass', '', $field->group);
	
					// Auto-generate label and description if needed
					// Field label
					$title 		 = $form->getFieldAttribute($field->fieldname, 'label', '', $field->group);
					$emptylabel  = $form->getFieldAttribute($field->fieldname, 'emptylabel', false, $field->group);
	
					if ($field->type == 'editor' || $field->type == 'Editor')
					{
	/*
						TODO
						Editor (TinyMCE, CodeMirror is activated!) doesn't activate on load (need to toggle manually)
						 - Maybe due to AJAX load so document ready doesn't trigger it?
						Text entered in the editor (any) will not save unless editor is toggled off before saving
						 - Look into JS triggering data copy from editor to form field
	*/
						// Load editor field
						$editor = JFactory::getEditor();
						$params = array( 'smilies'=> '0' ,
										 'style'  => '1' ,
										 'layer'  => '0' ,
										 'table'  => '0' ,
										 'clear_entities'=>'0'
									 );
						$inputField = $editor->display( $field->name,
														'',
														$field->width,
														$field->height,
														( is_int($field->columns) ? $field->columns : 0 ),
														$field->rows,
														$field->buttons,
														time(),
														'',
														'',
														'');
					}
					elseif (false)//$formType == 'read')
					{
						$inputField = $field->static;
					}
					elseif (true)//$formType == 'edit')
					{
						$inputField = $field->input;
					}
	
					if (empty($title))
					{
						$html .= "\t\t\t" . $inputField . PHP_EOL;
	
						if (!empty($description) && $formType == 'edit')
						{
							$html .= "\t\t\t\t" . '<span class="help-block">';
							$html .= JText::_($description) . '</span>' . PHP_EOL;
						}
					}
					else
					{
						$html .= "\t\t\t" . '<div class="control-group ' . $groupClass . '">' . PHP_EOL;
						$html .= "\t\t\t\t" . '<label class="control-label ' . $labelClass . '" for="' . $field->id . '">' . PHP_EOL;
						$html .= "\t\t\t\t" . JText::_($title) . PHP_EOL;
	
						if ($required)
						{
							$html .= ' *';
						}
	
						$html .= "\t\t\t\t" . '</label>' . PHP_EOL;
						$html .= "\t\t\t\t" . '<div class="controls">' . PHP_EOL;
						$html .= "\t\t\t\t" . $inputField . PHP_EOL;
	
						if (!empty($description))
						{
							$html .= "\t\t\t\t" . '<span class="help-block">';
							$html .= JText::_($description) . '</span>' . PHP_EOL;
						}
	
						$html .= "\t\t\t\t" . '</div>' . PHP_EOL;
						$html .= "\t\t\t" . '</div>' . PHP_EOL;
					}
				}
	
				$html .= "\t" . '</div>' . PHP_EOL;
	
				if( $form->getAttribute('tabs')=="true" )
				{
					$html .= JHtml::_('bootstrap.endTab');
				}
			}
	
			if( !empty($fieldsets) && $form->getAttribute('tabs') == "true" )
			{
				$html .= JHtml::_('bootstrap.endTabSet');
			}
	
			$html .= '</form>';
		
			echo $html;
		}
/*
		JFactory::getApplication()->close();
*/
//		return $html;
	}
	
	public function onNewSave( $source, $id, $date, $data  )
	{
		
	}
	
	public function onNewSaveOLD( $source, $id, $date, $data  )
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
	}

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

