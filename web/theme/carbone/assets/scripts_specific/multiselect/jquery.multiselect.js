;(function($) {

var multiselect = function( element, options ) {

    // settings are defined from default and given options
    this.settings = $.extend(true, {}, this.defaults, options);
    // original select element
    this.select = $(element);
    // cloned element
    this.clone = false;

}

multiselect.prototype = {

    // plugin version
    version: '0.4.0',

    // default settings
    defaults: {
        event: 'click',
        order: true,
        selectClass: 'multiselect',
        cloneClass: 'multiselect_clone',
        width: false,
        height: false,
        delay: 0
    },

    // called for each new instance of multiselect
    init: function() {

        var self = this;

        /*
         * IE6 Hack - apply a small delay to the movement
         */
        if( $.browser.msie && $.browser.version < 7 ) {
            self.settings.delay = 250;
        }

        // keep initial index order
        $('option',this.select).each(function(i) {
            $(this).data('index.msel',i);
        });

        // original select element
        this.select.addClass(this.settings.selectClass);

        // add default size if no specify
        if(!this.settings.width)
            this.settings.width = this.select.width();

        if(!this.settings.height)
            this.settings.height = this.select.height();

        this.select.width(this.settings.width).height(this.settings.height);

        this._clone();

        this.clone.bind(self.settings.event+'.msel', function() {
            self.delay(function() {
                self._move($(':selected',self.clone), self.select, self.settings.order);
            }, self.settings.delay);
        });

        this.select.bind(self.settings.event+'.msel', function() {
            self.delay(function() {
                self._move($(':selected',self.select), self.clone, self.settings.order);
            }, self.settings.delay);
        });

        this.select.closest('form').bind('submit.msel', function() {
            $('option',self.select).attr('selected', 'selected');
        });

    },

    _clone: function() {

        var self = this;

        // Create select
        // Can't use jQuery.clone because of IE lt 8 weirdenesses
        this.clone = $('<select/>').addClass(self.settings.cloneClass);

        // Copy some select attributes
        $.each(['id', 'size', 'multiple', 'width', 'height', 'class', 'style'], function(i, attribute) {
            var val = self.select.attr( attribute );
            if( attribute === 'id' ) {
                val += '_clone';
            }
            self.clone.attr( attribute , val );
        });

        this.select.find(':not(:selected)').appendTo(this.clone);

        this.clone.insertBefore(this.select);

    },

    _move: function( element, to, order ) {

        var it = $(element).data('index.msel')

        var next = $('option',to).map(function(i, opt) {
            if($.data(opt, 'index.msel') > it - 0 && order) {
                return opt;
            }
        }).get(0) || false;

        next ? $(next).before(element) : $(element).appendTo(to);

        $(element).removeAttr('selected');
    },

    delay: function(fn,time) {
        setTimeout(fn || $.noop,time || 0);
    }


}

// create plugin method
$.fn.multiselect = function( options ) {
    return this.each(function() {
        ( $.data(this, 'multiselect') || $.data(this, 'multiselect', new multiselect(this,options)).init() );
    });
}

})(jQuery)