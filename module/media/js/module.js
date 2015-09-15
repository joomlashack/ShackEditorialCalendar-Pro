PixPublishPlugins.push('newModule');

jQuery(function($) {
	$.noConflict();
	
	$.fn.newModule = function()
	{
		// add create new article-link
		$(this).find('.fc-day:not(.fc-past) .pp-day-head').prepend('<div class="pp-new pp-hide" data-plugin="module"><a href="javascript:void(0)"><span class="icon-cube"></span></a></div>');

		return this;
	}
	
});