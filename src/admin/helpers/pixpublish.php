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

// No direct access.
defined('_JEXEC') or die;

abstract class PixPublishHelper
{
    public static function isEnabled()
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('enabled')
            ->from('#__extensions')
            ->where('folder = "system"')
            ->where('element = "pixpublish"');
        $db->setQuery($query);

        $result = (boolean)$db->loadResult();
        return $result;
    }

    public static function addSubmenu($view)
    {
        if (JVERSION >= 3.0) {
            JHtmlSidebar::addEntry(JText::_('COM_PIXPUBLISH_VIEW_PANEL'), 'index.php?option=com_pixpublish&view=panel',
                $view == 'panel');

            JHtmlSidebar::addFilter(
                JText::_('JOPTION_SELECT_PUBLISHED'),
                'filter_state',
                JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('all' => false)), 'value', 'text',
                    '', true)
            );

            JHtmlSidebar::addFilter(
                JText::_('JOPTION_SELECT_ACCESS'),
                'filter_access',
                JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', '')
            );

            JHtmlSidebar::addFilter(
                JText::_('JOPTION_SELECT_LANGUAGE'),
                'filter_language',
                JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', '')
            );
        }
    }

    public static function getActions($component = '', $section = '', $id = 0)
    {
        jimport('joomla.access.access');

        $user   = JFactory::getUser();
        $result = new JObject;

        if ($section && $id) {
            $assetName = $component . '.' . $section . '.' . (int)$id;
        } else {
            $assetName = $component;
        }

        $actions = JAccess::getActions($component, 'component');

        foreach ($actions as $action) {
            $result->set($action->name, $user->authorise($action->name, $assetName));
        }
        return $result;
    }
}
