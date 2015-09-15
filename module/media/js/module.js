PixPublishPlugins.push('newModule');

jQuery(function($) {
	$.noConflict();
	
	$.fn.newModule = function()
	{
		// add create new article-link
		$(this).find('.fc-day:not(.fc-past) .pp-day-head').prepend('<div class="pp-new pp-hide" data-plugin="module"><a href="javascript:void(0)" class="hasTooltip" data-original-title="<strong>Add new</strong><br />Module"><span class="icon-cube"></span></a></div>');

		return this;
	}
	
});