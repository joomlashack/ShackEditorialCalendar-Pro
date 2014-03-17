<?php
/**
* @copyright	Copyright (C) 2012 Johan Sundell. All rights reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;
?>
<tr>
	<td colspan="4">
		<?php echo $this->pagination->getListFooter(); ?>
		<p class="footer-tip">
			<?php if ($this->enabled) : ?>
				<span class="enabled"><?php echo JText::_('COM_PIXPUBLISH_PLUGIN_ENABLED'); ?></span>
			<?php else : ?>
				<span class="disabled"><?php echo JText::_('COM_PIXPUBLISH_PLUGIN_DISABLED'); ?></span>
			<?php endif; ?>
		</p>
	</td>
</tr>