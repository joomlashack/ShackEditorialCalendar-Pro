<?php
/**
* @copyright	Copyright (C) 2012 Johan Sundell. All rights reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

JHtml::_( 'behavior.tooltip' );
JHtml::_( 'behavior.keepalive' );
?>
<form action="<?php echo JRoute::_('index.php?option=com_pixpublish&layout=edit&pixpublish_article_id='.(int)$this->item->pixpublish_article_id ); ?>" method="post" name="adminForm" id="article-form" class="form-validate">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'COM_PIXPUBLISH_SITE_DETAILS' ); ?></legend>
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset() as $field) {  /*print_r( $field );*/  if( $field->type == 'Rules' ) continue; ?>
				<li><?php echo $field->label;echo $field->input;?></li>
			<?php }?>
		</ul>
	</fieldset>
	
	<!-- begin ACL definition-->
	<div class="clr"></div>
	<?php if ($this->access->get('core.admin')): ?>
		<div class="width-100 fltlft">
		<?php echo JHtml::_('sliders.start', 'permissions-sliders-'.$this->item->pixpublish_article_id, array('useCookie'=>1)); ?>
		<?php echo JHtml::_('sliders.panel', JText::_('COM_PIXPUBLISH_ARTICLES_FIELDSET_RULES'), 'access-rules'); ?>
			<fieldset class="panelform">
				<?php echo $this->form->getLabel('rules'); ?>
				<?php echo $this->form->getInput('rules'); ?>
            </fieldset>
            <?php echo JHtml::_('sliders.end'); ?>
		</div>
	<?php endif; ?>
	<!-- end ACL definition-->

	<div>
		<input type="hidden" name="task" value="article.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
