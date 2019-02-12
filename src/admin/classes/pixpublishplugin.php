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

interface iPixPublishPlugin
{
    public function onDataFetch($start, $stop, $data);

    public function onItemMove($source, $id, $dayd, $mind);

    public function onGetDialog($source, $id, $form);

    public function onItemSave($source, $id, $data);

    public function onRegisterSearchFilters();
}

abstract class PixPublishPlugin extends JPlugin
{
    protected static function fixDates(&$arr, $fieldname)
    {
        foreach ($arr as $row) {
            $row->$fieldname = JFactory::getDate($row->start, 'UTC')
                ->setTimezone(new DateTimeZone(self::getUserTimeoffset()))
                ->format('Y-m-d H:i:s', true, false);
        }
        return $arr;
    }

    public function getInfoText()
    {
        return '';
    }

    protected static function getUserTimeoffset()
    {
        $config = JFactory::getConfig();
        $user   = JFactory::getUser();
        return $user->getParam('timezone', $config->get('offset', 'UTC'));
    }
}

class ColorFixer
{
    public static $st_color = '';
    public        $color;

    public function __construct()
    {
        $this->color = self::$st_color;
    }
}
