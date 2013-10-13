(function($){
	$(document).ready(function(){

        $('div.colourpicker').entwine({
            'onmatch' : function() {

                $('input.colourpicker:not(.colourpicker-active)').each(function() {

                    var $this = $(this);

	                $this.colorpicker({
						parts: 'full',
						showOn: 'both',
						buttonColorize: true,
						showNoneButton: true,
						alpha: true
					});

					$this.addClass('colourpicker-active');

                });

            }
        });		
		
	});
})(jQuery);