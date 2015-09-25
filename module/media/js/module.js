PixPublishPlugins.push('newModule');

jQuery(function($) {
	$.noConflict();
	
	$.fn.newModule = function()
	{
		var thisPlugin	= 'module';
		var thisIcon	= 'cube';
		var $headers	= $(this).find('.fc-day:not(.fc-past) .pp-day-head');

		// add create new article-link
		$headers.prepend('<div class="pp-new" data-plugin="' + thisPlugin + '"><a href="javascript:void(0)" class="hasTooltip" data-original-title="<strong>' + Joomla.JText._('COM_PIXPUBLISH_ADD_NEW') + '</strong><br />' + PLUGIN[thisPlugin] + '"><span class="icon-' + thisIcon + '"></span></a></div>');
		
		return this;
	}
	
});