<?php
/**
 * @package   ShackEditorialCalendar-Pro
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @author    2003-2017 You Rock AB. All Rights Reserved
 * @copyright 2018-2019 Joomlashack.com. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
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
 * along with ShackEditorialCalendar-Pro.  If not, see <http://www.gnu.org/licenses/>.
 */

use Joomla\CMS\Date\Date;
use Joomla\CMS\Form\Form;

defined('_JEXEC') or die();

require_once JPATH_ADMINISTRATOR . '/components/com_pixpublish/include.php';

// Register required core classes/paths
JLoader::register('ModulesHelper', JPATH_ADMINISTRATOR . '/components/com_modules/helpers/modules.php');
JLoader::register('TemplatesHelper', JPATH_ADMINISTRATOR . '/components/com_templates/helpers/templates.php');
JHtml::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_modules/' . '/helpers/html');

class PlgPixPublishModule extends PixPublishPlugin implements InterfacePixPublishPlugin
{
    protected $autoloadLanguage = true;

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
                    'tbl.published',
                    $db->quote($this->getName()) . ' AS plugin'
                )
            )
            ->from('#__modules tbl')
            ->where(
                array(
                    'tbl.publish_up >= ' . $db->quote($start->toSql()),
                    'tbl.publish_up <= ' . $db->quote($stop->toSql())
                )
            );

        if ($data->filter_position != '') {
            $query->where('tbl.position = ' . $db->quote($data->filter_position));
        }

        ColorFixer::$st_color = $this->params->get('background_colour', 'green');

        /** @var ColorFixer[] $result */
        $result = $db->setQuery($query)->loadObjectList('', 'ColorFixer');
        $result = static::fixDates($result, 'start');

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
        $id = (int)$id;

        if ($source === $this->getName() && $id > 0) {
            $db = JFactory::getDbo();

            $query = $db->getQuery(true)
                ->update('#__modules')
                ->set(
                    array(
                        sprintf(
                            'publish_up = DATE_ADD(ADDDATE(publish_up, %s), INTERVAL %s MINUTE)',
                            (int)$dayd,
                            $db->quote((int)$mind)
                        ),
                        sprintf(
                            'publish_down = DATE_ADD(ADDDATE(publish_down, %s ), INTERVAL %s MINUTE)',
                            (int)$dayd,
                            $db->quote((int)$mind)
                        )
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
        $id = (int)$id;

        if ($source === $this->getName() && $id > 0) {
            $db = JFactory::getDbo();

            $query = $db->getQuery(true)
                ->select(
                    array(
                        'tbl.id AS id',
                        'tbl.title AS title',
                        'tbl.publish_up AS start',
                        'tbl.published AS state',
                        $db->quote($this->getName()) . ' AS plugin',
                        'tbl.position'
                    )
                )
                ->from('#__modules tbl')
                ->where('tbl.id = ' . (int)$id);

            $result = $db->setQuery($query)->loadObject();

            $arr    = array($result);
            $arr    = self::fixDates($arr, 'start');
            $result = $arr[0];

            JForm::addFormPath(__DIR__ . '/form');
            $form->loadFile('form', false);

            return $result;
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
        if ($source === $this->getName()) {
            $db = JFactory::getDbo();

            $query = $db->getQuery(true)
                ->update('#__modules')
                ->where('id = ' . (int)$id);

            $time  = $data->pixtest_start;
            $title = $data->pixtest_title;

            if ($time) {
                $offset = JFactory::getConfig()->get('offset');
                $time   = JFactory::getDate($time, $offset)->format('H:i', false);

                $query->set(
                    sprintf('publish_up = TIMESTAMP(DATE(publish_up), %s)', $db->quote($time))
                );
            }

            if ($title) {
                $query->set('title = ' . $db->quote($title));
            }

            if ((int)$data->filter_status == 1) {
                $query->set('published = ' . (int)$data->filter_status);

            } else {
                $query->set('published = 0');
            }

            if ($data->filter_edit_position != '') {
                $query->set('position = ' . $db->quote($data->filter_edit_position));
            }

            return (bool)$db->setQuery($query)->execute();
        }

        return true;
    }

    /**
     * @return void
     */
    public function onRegisterSearchFilters()
    {
        JHtmlSidebar::addFilter(
            JText::_('PLG_PIXPUBLISH_MODULE_SEARCH_POSITION'),
            'filter_position',
            JHtml::_('select.options', ModulesHelper::getPositions(0), 'value', 'text')
        );

        JFactory::getDocument()
            ->addScriptDeclaration(
                sprintf("PLUGIN['module'] = '%s';", JText::_('PLG_PIXPUBLISH_MODULE_TYPE_NAME'))
            );

        JHtml::_('script', 'plugins/pixpublish/module/media/js/module.js');
    }

    /**
     * @return string
     */
    protected function getName()
    {
        return 'module';
    }
}


