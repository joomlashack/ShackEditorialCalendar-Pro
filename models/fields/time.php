<?php
/**
* @copyright	Copyright (C) 2014 Johan Sundell. All rights reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
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
	<i class="icon-calendar"></i>
</div>
HTML;
		return $html;
	}
}