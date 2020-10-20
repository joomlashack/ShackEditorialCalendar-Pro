<?php
/**
 * @package   ShackEditorialCalendar-Pro
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @author    2003-2017 You Rock AB. All Rights Reserved
 * @copyright 2018-2020 Joomlashack.com. All rights reserved
 * @license   https://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of ShackEditorialCalendar-Pro.
 *
 * ShackEditorialCalendar-Pro is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * ShackEditorialCalendar-Pro is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ShackEditorialCalendar-Pro.  If not, see <https://www.gnu.org/licenses/>.
 */

use Joomla\CMS\Date\Date;
use Joomla\CMS\Form\Form;

defined('_JEXEC') or die();

require_once JPATH_ADMINISTRATOR . '/components/com_pixpublish/include.php';

class PlgPixPublishContent extends PixPublishPlugin implements InterfacePixPublishPlugin
{
    /**
     * @var bool
     */
    protected $autoloadLanguage = true;

    /**
     * @var int
     */
    protected $item = null;

    /**
     * @param Date   $start
     * @param Date   $stop
     * @param object $data
     *
     * @return ColorFixer[]
     */
    public function onDataFetch($start, $stop, $data)
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true)
            ->select(
                array(
                    'tbl.id AS id',
                    'tbl.title AS title',
                    'tbl.publish_up AS start',
                    'tbl.state',
                    $db->quote($this->getName()) . ' AS plugin'
                )
            )
            ->from('#__content AS tbl')
            ->where(
                array(
                    'tbl.publish_up >= ' . $db->quote($start->toSql()),
                    'tbl.publish_up <= ' . $db->quote($stop->toSql())
                )
            );

        if (!empty($data->filter_state)) {
            $query->where('state = ' . (int)$data->filter_state);
        }

        if (!empty($data->filter_category_id)) {
            $query->where('catid = ' . (int)$data->filter_category_id);
        }

        if (!empty($data->filter_access)) {
            $query->where('access = ' . (int)$data->filter_access);
        }

        if (!empty($data->filter_language)) {
            $query->where('language = ' . $db->quote($data->filter_language));
        }

        ColorFixer::$st_color = $this->params->get('background_colour', '#08C');

        /** @var ColorFixer[] $result */
        $result = $db->setQuery($query)->loadObjectList('', 'ColorFixer');
        $result = self::fixDates($result, 'start');

        return $result;
    }

    /**
     * @param string $source
     * @param string $id
     * @param int    $dayd
     * @param int    $mind
     *
     * @return bool
     */
    public function onItemMove($source, $id, $dayd, $mind)
    {
        if ($source === $this->getName()) {
            if (!$this->getAuth('core.edit', $id)
                && !$this->canEditOwn($id)
            ) {
                return false;
            }

            $db = JFactory::getDbo();

            $query = $db->getQuery(true)
                ->update('#__content')
                ->set(
                    sprintf(
                        'publish_up = DATE_ADD(ADDDATE(publish_up, %s), INTERVAL %s MINUTE )',
                        (int)$dayd,
                        $db->quote((int)$mind)
                    )
                )
                ->where('id = ' . (int)$id);

            return (bool)$db->setQuery($query)->execute();
        }

        return true;
    }

    /**
     * @param string $source
     * @param int    $id
     * @param Form   $form
     *
     * @return object
     */
    public function onGetDialog($source, $id, $form)
    {
        if ($source === $this->getName()) {
            JForm::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_categories/models/fields');
            JForm::addFormPath(__DIR__ . '/form');

            $form->loadFile('form', false);

            $form->setFieldAttribute('articletext', 'id', time());

            $id = (int)$id;
            if ($id > 0) {
                $db = JFactory::getDbo();

                $query = $db->getQuery(true)
                    ->select(
                        array(
                            'tbl.id AS id',
                            'tbl.title AS title',
                            'tbl.publish_up AS start',
                            'tbl.state',
                            'tbl.introtext',
                            'tbl.fulltext',
                            'tbl.language',
                            'tbl.access',
                            'tbl.catid',
                            'tbl.alias',
                            $db->quote($this->getName()) . 'AS plugin'
                        )
                    )
                    ->from('#__content tbl')
                    ->where('tbl.id = ' . $id);

                $result = $db->setQuery($query)->loadObject();

                $result->articletext = trim($result->fulltext) != ''
                    ? $result->introtext . "<hr id=\"system-readmore\" />" . $result->fulltext
                    : $result->introtext;

                if ($result) {
                    $arr = array($result);
                    $arr = self::fixDates($arr, 'start');

                    return array_shift($arr);
                }
            }
        }

        return null;
    }

    /**
     * @param string $source
     * @param int    $id
     * @param object $data
     *
     * @return bool
     */
    public function onItemSave($source, $id, $data)
    {
        $title = empty($data->title) ? null : trim($data->title);

        if ($source === $this->getName() && $title) {
            $id = (int)$id;

            $canEdit = $this->getAuth('core.edit', $id);
            if (!$canEdit) {
                $canEdit = $this->canEditOwn($id);
            }
            $canEditState = $this->canEditState($id);

            if (!$this->getAuth('core.create', $id)) {
                return false;
            }

            if (!$canEdit && !$canEditState && (int)$id > 0) {
                return false;
            }

            $db = JFactory::getDbo();

            $query = $db->getQuery(true);
            if ($id > 0) {
                $query->update('#__content');

            } else {
                $query->insert('#__content');
            }

            $time        = $data->start;
            $articletext = $data->articletext;
            $alias       = $data->alias;

            // Search for the {readmore} tag and split the text up accordingly.
            $allContent = preg_split('#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i', $articletext);

            $introtext = array_shift($allContent);
            $fulltext  = array_shift($allContent);

            if ($time) {
                $time = JFactory::getDate($time, self::getUserTimeoffset())->format('H:i', false);
                if ($canEdit && $id > 0) {
                    $query->set(
                        sprintf(
                            'publish_up = TIMESTAMP(DATE(publish_up ), %s)',
                            $db->quote($time)
                        )
                    );
                }
            }

            if ($canEdit || $id == 0) {
                if (trim($alias) == '') {
                    $alias = $title;
                }

                if (JFactory::getConfig()->get('unicodeslugs') == 1) {
                    $alias = JFilterOutput::stringURLUnicodeSlug($alias);

                } else {
                    $alias = JFilterOutput::stringURLSafe($alias);
                }

                $query->set(
                    array(
                        $db->quoteName('title') . ' = ' . $db->quote($title),
                        $db->quoteName('introtext') . ' = ' . $db->quote($introtext),
                        $db->quoteName('fulltext') . ' = ' . $db->quote($fulltext),
                        $db->quoteName('alias') . ' = ' . $db->quote($alias),
                        $db->quoteName('language') . ' = ' . $db->quote($data->language),
                        $db->quoteName('access') . ' = ' . $db->quote($data->access),
                        $db->quoteName('catid') . ' = ' . $db->quote($data->catid)
                    )
                );
            }

            $user         = JFactory::getUser();
            $userDatetime = JFactory::getDate('now', static::getUserTimeoffset())->toSql();
            $query->set(
                array(
                    'modified = ' . $db->quote($userDatetime),
                    'modified_by = ' . (int)$user->id
                )
            );

            if ($canEditState) {
                $query->set('state = ' . (int)$data->state);
            }

            if ($id > 0) {
                $query->where('id = ' . $id);

            } else {
                $query->set(
                    array(
                        'publish_up = ' . $db->quote($data->publish_up . ' ' . $time),
                        'created_by = ' . (int)$user->id,
                        'created = ' . $db->quote($userDatetime)
                    )
                );
            }

            return (bool)$db->setQuery($query)->execute();
        }

        return true;
    }

    /**
     * @return string
     */
    protected function getName()
    {
        return 'content';
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    protected function canEditOwn($id)
    {
        if ($this->getAuth('core.edit.own', $id)) {
            $user      = JFactory::getUser();
            $createdBy = (int)$this->getItem($id)->created_by;

            return ($user->id && $user->id == $createdBy);
        }

        return false;
    }

    protected function canEditState($id)
    {
        return $this->getAuth('core.edit.state', $id);
    }

    protected function getAuth($id, $action)
    {
        return JFactory::getUser()->authorise($action, 'com_content.article.' . (int)$id);
    }

    protected function getItem($id)
    {
        if ($this->item == null) {
            $db = JFactory::getDbo();

            $query = $db->getQuery(true);

            $query->select('*')
                ->from('#__content')
                ->where('id = ' . (int)$id);

            $this->item = $db->setQuery($query)->loadObject();
        }
        return $this->item;
    }

    public function onRegisterSearchFilters()
    {
        JHtmlSidebar::addFilter(
            JText::_('PLG_PIXPUBLISH_CONTENT_CATEGORY_LABEL'),
            'filter_category_id',
            JHtml::_('select.options', JHtml::_('category.options', 'com_content'), 'value', 'text', '')
        );

        JFactory::getDocument()
            ->addScriptDeclaration(
                sprintf("PLUGIN['content'] = '%s';", JText::_('PLG_PIXPUBLISH_CONTENT_TYPE_NAME'))
            );

        JHtml::_('script', 'plugins/pixpublish/content/media/js/content.js');
    }
}

