(function($){
	$(document).ready(function(){
	
		$('.syntax-highlighted').each(function( {
			var $this = $(this);
			var myCodeMirror = CodeMirror(document.body, {
				value: $this.val(),
				mode:  $this.attr('data-type')
			});
		});

	});
})(jQuery);