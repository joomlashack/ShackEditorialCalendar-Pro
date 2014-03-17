<?php
/**
* @copyright	Copyright (C) 2012 Johan Sundell. All rights reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;
?>
<tr>
	<th width="5">
		<?php echo JText::_('COM_PIXPUBLISH_ARTICLES_HEADING_ID' ); ?>
	</th>
	<th width="20">
		<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
	</th>
	<th width="20">
		<?php echo JText::_( 'JSTATUS' ); ?>
	</th>
	<th>
		<?php echo JText::_( 'COM_PIXPUBLISH_ARTICLES_HEADING_NAME' ); ?>
	</th>
</tr>