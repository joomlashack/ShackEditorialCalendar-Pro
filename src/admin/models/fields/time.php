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

jimport('joomla.form.formfield');

class JFormFieldTime extends JFormField
{
    protected $type = 'time';

    protected function getInput()

    {
        // TODO: Add check that we have a time/date
        $start = JDate::getInstance($this->value);
        $html  = <<<HTML
<div class="input-append bootstrap-timepicker">
	<input id="$this->id" name="$this->name" type="text" class="input-small timepicker" value="$start->hour:$start->minute">
	<!-- <span class="add-on"><i class="icon-calendar"></i></span>-->
	<span class="pp-btn"><i class="icon-clock"></i></span>
</div>
HTML;
        return $html;
    }
}
