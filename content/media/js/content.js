PixPublishPlugins.push('newContent');

jQuery(function($) {
	$.noConflict();
	
	$.fn.newContent = function()
	{
		// add create new article-link
		$(this).find('.fc-day:not(.fc-past) .pp-day-head').prepend('<div class="pp-new pp-hide" data-plugin="content"><a href="javascript:void(0)"><span class="icon-stack"></span></a></div>');

		return this;
	}
	
});