<?php
/**
* @copyright	Copyright (C) 2012 Johan Sundell. All rights reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;
?>
<?php foreach( $this->items as $i => $item ){ ?>
<?php $editLink = JRoute::_('index.php?option=com_pixpublish&task=article.edit&pixpublish_article_id='.(int) $item->pixpublish_article_id); ?>
<tr class="row<?php echo $i % 2; ?>">
	<td>
		<?php echo $item->pixpublish_article_id; ?>
	</td>
	<td>
		<?php echo JHtml::_('grid.id', $i, $item->pixpublish_article_id); ?>
	</td>
	<td>
		<?php echo JHtml::_('jgrid.published', $item->published, $i, 'articles.' ); ?>
	</td>
	<td>
		<a href="<?php echo $editLink;?>">
			<?php echo $item->name; ?>
		</a>
	</td>
</tr>
<?php } ?>

