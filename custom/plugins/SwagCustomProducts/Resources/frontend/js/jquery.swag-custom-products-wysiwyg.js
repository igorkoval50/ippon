;(function($) {
    'use strict';

    $.plugin('swagCustomProductsWysiwyg', {

        /** @object Default plugin configuration */
        defaults: {

            /** @string Language name */
            lang: 'en',

            /** @object editorSettings - see http://alex-d.github.io/Trumbowyg/documentation.html */
            editorSettings: {
                resetCss: true,
                removeformatPasted: true,
                autogrow: true,
                svgPath: '',
                fullscreenable: false,
                btns: [
                    'bold', 'italic', 'underline', 'strikethrough',
                    '|', 'btnGrp-lists'
                ]
            }
        },

        /**
         * Initializes the plugin and adds listeners to the editor plugin.
         *
         * @returns void
         */
        init: function() {
            var me = this,
                $form = me.$el.parents('form'),
                $modal = me.$el.parents('.custom-products--wizard-container'),
                editor;

            me.applyDataAttributes();

            $.publish('plugin/swagCustomProductsWysiwyg/init', [ me, $form, me.opts ]);

            me.$el.trumbowyg(me.opts.editorSettings).on('tbwblur', function () {
                // Removes html tags if the user didn't passed any text.
                var content = editor.$ed.text();
                if (content.length === 0) {
                    me.$el.trumbowyg('empty');
                }

                // Trigger the step-by-step mode change event
                if ($modal.length) {
                    $modal.trigger('change');
                    return;
                }

                $form.trigger('change');
            });

            editor = me.$el.data('trumbowyg');
        },

        /**
         * Set a value to the editor. Sets the value to empty if no value was passed.
         *
         * @returns void
         */
        setValue: function(val) {
            var me = this;

            if (!val.length) {
                me.$el.trumbowyg('empty');
            } else {
                me.$el.trumbowyg('html', val);
            }

            $.publish('plugin/swagCustomProductsWysiwyg/setValue', [ me, val, me.$el ]);
        },

        /**
         * Returns the value of the editor.
         *
         * @returns {String}
         */
        getValue: function() {
            var me = this;
            return me.$el.trumbowyg('html');
        },

        /**
         * Destroys the plugin and destroys the WYSIWYG editor.
         *
         * @returns void
         */
        destroy: function() {
            var me = this;

            me.$el.trumbowyg('destroy');
            me._destroy();
        }
    });

    $(function() {
        StateManager.addPlugin('*[data-swag-custom-products-wysiwyg="true"]', 'swagCustomProductsWysiwyg');
    });
})(jQuery);
