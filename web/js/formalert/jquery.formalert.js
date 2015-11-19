(function($) {

    $.fn.formalert = function(params) {

        var default_params = {
            message:'',
            except:['[type="submit"]'],
            formcheck:false
        };

        //param fusion
        params = $.extend(default_params, params);

        var is_dirty = default_params.formcheck;

        //Form element binding
        $(this).find(":input").live("change", function(event) {
            if (event.target.value != event.target.defaultValue) {
                is_dirty = true;
            }
        });
                
        $(this).find(":checkbox,:radio").live("click", function(event) {
            if (event.target.checked != event.target.defaultChecked) {
                is_dirty = true;
            }
        });

        //exception managment
        for (except in params.except) {
            $(this).find(params.except[except]).live("click", function(event) {
                is_dirty = false;
            });
        }
        
        window.onbeforeunload = function () {
            if (is_dirty) {
                return params.message;
            }
        }
    }

})(jQuery);