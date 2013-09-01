(function($){
	$(document).ready(function(){
	
		$('.syntax-highlighted').each(function() {

			var $this = $(this);
			var myCodeMirror = CodeMirror.fromTextArea(this, {
				value: $this.val(),
				mode:  $this.attr('data-type')
			});

		});

	});
})(jQuery);