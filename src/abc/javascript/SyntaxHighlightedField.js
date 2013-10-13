(function($){
    $(document).ready(function(){

        $('div.syntax-highlighted').entwine({
            'onmatch' : function() {

                $('textarea.syntax-highlighted:not(.code-mirror-active)').each(function() {

                    var cm_editor,
                        $this = $(this),
                        conf = {
                            value:          $this.val(),
                            mode:           $this.attr('data-type'),
                            lineNumbers:    true
                        };

                    if (typeof conf.mode != 'undefined' && conf.mode) {
                        conf.onKeyEvent = function(editor, data){
                            $this.val(editor.getValue());
                        };
                        cm_editor = CodeMirror.fromTextArea(this, conf);
                        $this.addClass('code-mirror-active');
                    }


                });

            }
        });

    });
})(jQuery);