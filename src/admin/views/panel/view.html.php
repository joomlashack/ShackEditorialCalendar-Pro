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

class PixPublishViewPanel extends JViewLegacy
{
    protected $infotexts = null;

    /**
     * Language strings to be added to javascripts
     *
     * @var string[]
     */
    protected $jsLanguageStrings = array(
        'COM_PIXPUBLISH_ADD_NEW',
        'COM_PIXPUBLISH_EDIT',
        'JSAVE',
        'JCANCEL',
        'JANUARY',
        'FEBRUARY',
        'MARCH',
        'APRIL',
        'MAY',
        'JUNE',
        'JULY',
        'AUGUST',
        'SEPTEMBER',
        'OCTOBER',
        'NOVEMBER',
        'DECEMBER',
        'JANUARY_SHORT',
        'FEBRUARY_SHORT',
        'MARCH_SHORT',
        'APRIL_SHORT',
        'MAY_SHORT',
        'JUNE_SHORT',
        'JULY_SHORT',
        'AUGUST_SHORT',
        'SEPTEMBER_SHORT',
        'OCTOBER_SHORT',
        'NOVEMBER_SHORT',
        'DECEMBER_SHORT',
        'MONDAY',
        'TUESDAY',
        'WEDNESDAY',
        'THURSDAY',
        'FRIDAY',
        'SATURDAY',
        'SUNDAY',
        'MON',
        'TUE',
        'WED',
        'THU',
        'FRI',
        'SAT',
        'SUN',
        'JLIB_HTML_BEHAVIOR_TODAY',
        'COM_PIXPUBLISH_MONTH'
    );

    /**
     * @param string $tpl
     *
     * @return void
     * @throws Exception
     */
    public function display($tpl = null)
    {
        JHtml::_('jquery.framework', true);
        JHtml::_('bootstrap.framework');
        JHtml::_('formbehavior.chosen', 'select');
        JHtml::_('behavior.keepalive');

        // Fullcalendar
        JHtml::_('script', 'administrator/components/com_pixpublish/media/lib/jquery/jquery-ui.custom.min.js');
        JHtml::_('script', 'administrator/components/com_pixpublish/media/lib/fullcalendar/fullcalendar.min.js');
        JHtml::_('stylesheet', 'administrator/components/com_pixpublish/media/lib/fullcalendar/fullcalendar.css');

        // Popup
        JHtml::_('script', 'administrator/components/com_pixpublish/media/lib/messi/messi.hacked.js');
        JHtml::_('stylesheet', 'administrator/components/com_pixpublish/media/lib/messi/messi.hacked.min.css');

        // Timepicker
        JHtml::_('script', 'administrator/components/com_pixpublish/media/lib/test/bootstrap-timepicker.min.js');
        JHtml::_('stylesheet', 'administrator/components/com_pixpublish/media/lib/test/bootstrap-timepicker.min.css');

        // Component
        JHtml::_('script', 'administrator/components/com_pixpublish/media/js/pixpublish.js');
        JHtml::_('stylesheet', 'administrator/components/com_pixpublish/media/css/pixpublish.css');
        JFactory::getDocument()->addScriptDeclaration('var PLUGIN = [];');

        // Language strings
        foreach ($this->jsLanguageStrings as $text) {
            JText::script($text);
        }

        $this->addToolbar();

        $dispatcher = $this->importPlugins();

        PixPublishHelper::addSubmenu($this->getName());

        $dispatcher->trigger('onRegisterSearchFilters');
        $this->sidebar = JHtmlSidebar::render();

        $this->infotexts = $dispatcher->trigger('getInfoText');


        parent::display($tpl);
    }

    protected function addToolbar()
    {
        JToolBarHelper::title(JText::_('COM_PIXPUBLISH_MANAGER_PANEL'), 'calendar');
    }

    /**
     * @return JEventDispatcher
     */
    protected function importPlugins()
    {
        JPluginHelper::importPlugin('pixpublish');
        $dispatcher = JEventDispatcher::getInstance();

        return $dispatcher;
    }
}
