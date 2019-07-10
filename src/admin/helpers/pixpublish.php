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

defined('_JEXEC') or die();

abstract class PixPublishHelper
{
    /**
     * @var bool
     */
    protected static $enabled = null;

    /**
     * @return bool
     */
    public static function isEnabled()
    {
        if (static::$enabled === null) {
            $db = JFactory::getDbo();

            $query = $db->getQuery(true)
                ->select('enabled')
                ->from('#__extensions')
                ->where('folder = "system"')
                ->where('element = "pixpublish"');

            static::$enabled = (boolean)$db->setQuery($query)->loadResult();
        }

        return static::$enabled;
    }

    /**
     * @param string $view
     *
     * @return void
     */
    public static function addSubmenu($view)
    {
        JHtmlSidebar::addEntry(
            JText::_('COM_PIXPUBLISH_VIEW_PANEL'),
            'index.php?option=com_pixpublish&view=panel',
            $view == 'panel'
        );

        JHtmlSidebar::addFilter(
            JText::_('JOPTION_SELECT_PUBLISHED'),
            'filter_state',
            JHtml::_(
                'select.options',
                JHtml::_('jgrid.publishedOptions', array('all' => false)),
                'value',
                'text',
                '',
                true
            )
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
