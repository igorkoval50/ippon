;(function ($) {
    'use strict';

    $.plugin('tlsNewsletterGroup', {
        defaults: {
            hiddenInput: '#newsletter',
            checkboxes: '.list--checkbox input'
        },
        init: function () {
            var me = this;

            me.applyDataAttributes();

            me.hiddenInput = me.$el.find(me.opts.hiddenInput);
            me.checkboxes = me.$el.find(me.opts.checkboxes);

            me._on(me.checkboxes, 'change', $.proxy(me.onChange, me));
        },
        onChange: function () {
            var me = this;
            me.hiddenInput.val(me.checkboxes.is(":checked") ? 1 : 0);
        }
    });

    window.StateManager.addPlugin('*[data-newsletter-group="true"]', 'tlsNewsletterGroup');
})(jQuery);
