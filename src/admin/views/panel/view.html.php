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

    public function display($tpl = null)
    {
        JHtml::_('jquery.framework', true);
        JHtml::_('bootstrap.framework');
        JHtml::_('formbehavior.chosen', 'select');
        JHtml::_('behavior.keepalive');


        $doc = JFactory::getDocument();
        // Fullcalendar
        $doc->addScript(JUri::root() . 'administrator/components/com_pixpublish/media/lib/jquery/jquery-ui.custom.min.js');
        $doc->addScript(JUri::root() . 'administrator/components/com_pixpublish/media/lib/fullcalendar/fullcalendar.min.js',
            'text/javascript');
        $doc->addStyleSheet(JUri::root() . 'administrator/components/com_pixpublish/media/lib/fullcalendar/fullcalendar.css');

        // Popup
        $doc->addScript(JUri::root() . 'administrator/components/com_pixpublish/media/lib/messi/messi.hacked.js');
        $doc->addStyleSheet(JUri::root() . 'administrator/components/com_pixpublish/media/lib/messi/messi.hacked.min.css');

        // Timepicker
        $doc->addScript(JUri::root() . 'administrator/components/com_pixpublish/media/lib/test/bootstrap-timepicker.min.js');
        $doc->addStyleSheet(JUri::root() . 'administrator/components/com_pixpublish/media/lib/test/bootstrap-timepicker.min.css');

        // Component
        $doc->addScript(JUri::root() . 'administrator/components/com_pixpublish/media/js/pixpublish.js');
        $doc->addStyleSheet(JUri::root() . 'administrator/components/com_pixpublish/media/css/pixpublish.css');
        $doc->addScriptDeclaration('var PLUGIN = [];');

        //JFactory::getLanguage()->load('com_pixpublish');
        //echo JText::_('COM_PIXPUBLISH_VIEW_PANEL'); die();

        // Language strings
        JText::Script('COM_PIXPUBLISH_ADD_NEW');
        JText::Script('COM_PIXPUBLISH_EDIT');
        JText::Script('JSAVE');
        JText::Script('JCANCEL');
        JText::Script('JANUARY');
        JText::Script('FEBRUARY');
        JText::Script('MARCH');
        JText::Script('APRIL');
        JText::Script('MAY');
        JText::Script('JUNE');
        JText::Script('JULY');
        JText::Script('AUGUST');
        JText::Script('SEPTEMBER');
        JText::Script('OCTOBER');
        JText::Script('NOVEMBER');
        JText::Script('DECEMBER');
        JText::Script('JANUARY_SHORT');
        JText::Script('FEBRUARY_SHORT');
        JText::Script('MARCH_SHORT');
        JText::Script('APRIL_SHORT');
        JText::Script('MAY_SHORT');
        JText::Script('JUNE_SHORT');
        JText::Script('JULY_SHORT');
        JText::Script('AUGUST_SHORT');
        JText::Script('SEPTEMBER_SHORT');
        JText::Script('OCTOBER_SHORT');
        JText::Script('NOVEMBER_SHORT');
        JText::Script('DECEMBER_SHORT');
        JText::Script('MONDAY');
        JText::Script('TUESDAY');
        JText::Script('WEDNESDAY');
        JText::Script('THURSDAY');
        JText::Script('FRIDAY');
        JText::Script('SATURDAY');
        JText::Script('SUNDAY');
        JText::Script('MON');
        JText::Script('TUE');
        JText::Script('WED');
        JText::Script('THU');
        JText::Script('FRI');
        JText::Script('SAT');
        JText::Script('SUN');
        JText::Script('JLIB_HTML_BEHAVIOR_TODAY');
        JText::Script('COM_PIXPUBLISH_MONTH');

        $this->addToolbar();

        $dispatcher = $this->importPlugins();

        PixPublishHelper::addSubmenu($this->getName());
        $results       = $dispatcher->trigger('onRegisterSearchFilters');
        $this->sidebar = JHtmlSidebar::render();

        $infotexts       = $dispatcher->trigger('getInfoText');
        $this->infotexts = $infotexts;

        parent::display($tpl);
    }

    protected function addToolbar()
    {
        JToolBarHelper::title(JText::_('COM_PIXPUBLISH_MANAGER_PANEL'), 'calendar');
    }

    /**
     * @return JDispatcher
     */
    protected function importPlugins()
    {
        JPluginHelper::importPlugin('pixpublish');
        $dispatcher = JDispatcher::getInstance();
        return $dispatcher;
    }
}
