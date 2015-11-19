(function($) {

// Variables for cached values or use across multiple functions
var item = false,
content,
overlay = false,
gblAction,
opt,
offLeft = 0,
offTop = 0,
isIE6 = !$.support.opacity && !window.XMLHttpRequest,
$pg = $(window);

// private functions

function setOverlay() {
    if(!!overlay) overlay.remove();
    overlay = $('<div>').addClass(opt.overlay).appendTo('body').css({width:$pg.width()+'px',height:$pg.height()+'px',top:$pg.scrollTop(),left:$pg.scrollLeft(),zIndex:'9998'}).show();
};

function setPosition(withOffset) {
    if(withOffset) {
        offLeft = $pg.scrollLeft();
        offTop = $pg.scrollTop();
    }
    if(opt.width) item.width(opt.width);
    if(opt.height) item.height(opt.height);
    if(opt.center) item.css({marginLeft:(-1*item.width()/2)+offLeft+'px',marginTop:(-1*item.height()/2)+offTop+'px'});
};

// public methods

$.extend({
    notice: function(element, options) {
    
        opt = $.extend(true,{
            close:      1,
            duration:   0,
            height:     false,
            width:      false,
            center:     true,
            overlay:    false,
            className:  'notice-item'
        },options);

        $.noticeRemove();
        
        content = $('<div>').html(element);
        content.find('*').show();
        item = $('<div>').hide().addClass(opt.className).css('zIndex','9999').appendTo('body').html(content).fadeIn('fast');

        if(typeof gblAction == 'function') {
            var lclAction = gblAction,
                action = $('<div>').addClass('action').appendTo(item);

            $.each({Ok: true,Cancel: false}, function(i, response) {
                $('<button class="btn btn-default"><span>'+opt['str'+i]+'</span></button>').appendTo(action).click(function() {
                    var ret = lclAction(response, item);
                    if(typeof ret == "undefined" || ret)
                        $.noticeRemove();
                });
            });

            gblAction = false;
        }

        if(opt.overlay) {
            setOverlay();
            $pg.bind('resize.notice scroll.notice', function() {
                setOverlay();
            });
        }

        if(opt.close)
            $('<div>',{text:'x'}).addClass('close').prependTo(item).click($.noticeRemove);

        setPosition(isIE6);

        if(opt.duration > 0) setTimeout($.noticeRemove, opt.duration);

        // ie6... we will miss it
        if(isIE6) {
            // it doesn't fixed position, so use absolute
            item.css('position','absolute');
            // select items are @#$!, so we hide them
            $('select:visible').addClass('hideForNotice').css('visibility','hidden');
            // add event on scroll & resize to simulate fixed position
            $pg.bind('resize.notice scroll.notice',function() {
                setPosition(true);
            });
        }

        return item;
    },

    confirm: function(message, callback, options) {
        var settings = $.extend(true,{
            close:false,
            strOk:'Ok',
            strCancel: 'Annuler'
        },options);
        if(typeof callback == "function") {
            gblAction = callback;
        } else {
            gblAction = false;
        }
        $.notice(message, settings);
        return false;
    },

    noticeRemove: function() {
        if(!!item) item.fadeOut('fast');
        if(!!overlay) overlay.fadeOut('fast').remove();
        $('.hideForNotice').removeClass('hideForNotice').css('visibility','');
        $pg.unbind('.notice');
    }
});

$.fn.extend({
    notice : function(opt) {
        return this.map(function() {
            return $.notice(this, opt || {});
        });
    }
});

})(jQuery);