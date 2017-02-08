;(function($) {

    $.fn.counter = function(length, text) {
        // just in case
        text = text || " caractère(s) restant(s)"
        // loop on elements
        return this.each(function() {
            if(!$(this).data('txt_count')) {
                // init html element for counter
                var counter = $('<p class="textarea_counter"><span style="margin-top: 2px;"></span>&nbsp;'+text+'</p>');
                // place the counter element after textarea element or its wrapper if it is resizeable
                $(this).data('txt_count',1).after(counter);
                // everytime we use keyboard on textarea element
                $(this).keyup(function() {
                    // textarea caracters number calculation
                    var left = size = $(this).val().length;
                    // if a limit is defined
                    if(length > 0) {
                        left = (length - size > 0) ? length - size : 0;
                        $(this).val(($(this).val()).substring(0,length));
                    }
                    counter.children('span:first').html(left);
                }).keyup();
            }
        });
    }

})(jQuery)