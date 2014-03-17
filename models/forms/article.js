window.addEvent( 'domready', function() 
{
	document.formvalidator.setHandler( 'article',
		function( value )
		{
			regex = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?$/;
			return regex.test(value);
		});
});
