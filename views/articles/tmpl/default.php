<?php
/**
* @copyright	Copyright (C) 2012 Johan Sundell. All rights reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

JHtml::_( 'behavior.tooltip' );
JHtml::_( 'behavior.multiselect' );
?>
<form action="<?php echo JRoute::_( 'index.php?option=com_pixpublish'); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead><?php echo $this->loadTemplate( 'head' ); ?></thead>
		<tbody><?php echo $this->loadTemplate( 'body' ); ?></tbody>
		<tfoot><?php echo $this->loadTemplate( 'foot' ); ?></tfoot>
	</table>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_( 'form.token' ); ?>
	</div>
</form>
