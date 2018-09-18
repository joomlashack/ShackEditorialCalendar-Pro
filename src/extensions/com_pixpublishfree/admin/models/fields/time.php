<?php
/**
 * @package         ShackEditorialCalendarfree
 * @copyright       Copyright (C) 2018. All rights reserved.
 * @link            https://www.joomlashack.com/joomla-extensions/shack-editorial-calendar/
 * @author       	You Rock AB 2003-2017 All Rights Reserved
 * @author       	2018, Joomlashack <help@joomlashack.com> - https://www.joomlashack.com.
 * @license         GNU General Public License version 2 or later; see LICENSE.txt
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
		$start = JDate::getInstance( $this->value );
$html = <<<HTML
<div class="input-append bootstrap-timepicker">
	<input id="$this->id" name="$this->name" type="text" class="input-small timepicker" value="$start->hour:$start->minute">
	<!-- <span class="add-on"><i class="icon-calendar"></i></span>-->
	<span class="pp-btn"><i class="icon-clock"></i></span>
</div>
HTML;
		return $html;
	}
}