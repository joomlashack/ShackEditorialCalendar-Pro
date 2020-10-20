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

use Joomla\Utilities\ArrayHelper;

defined('_JEXEC') or die();

class SecFormFieldTime extends JFormField
{
    protected $type = 'time';

    protected function getInput()
    {
        $start = JDate::getInstance($this->value);

        $attributes = array(
            'id'    => $this->id,
            'name'  => $this->name,
            'type'  => 'text',
            'class' => 'input-small timepicker',
            'value' => $start->hour . ':' . $start->minute
        );

        $html = array(
            '<div class="input-append bootstrap-timepicker">',
            sprintf('<input %s/>', ArrayHelper::toString($attributes)),
            '<span class="pp-btn"><i class="icon-clock"></i></span>',
            '</div>'
        );

        return join("\n", $html);
    }
}
