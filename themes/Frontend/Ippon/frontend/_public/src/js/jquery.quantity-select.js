;(function($, window) {
    'use strict';

    $.plugin('quantitySelect', {

        defaults: {

            quantityPlus: '.quantity--plus',
            quantityMinus: '.quantity--minus',
            quantitySubmit: '.quantity--submit',
            quantityInput: '.quantity--select'

        },

        init: function () {
            var me = this;

            me.applyDataAttributes();
            me.$quantityPlus = me.$el.find(me.opts.quantityPlus);
            me.$quantityMinus = me.$el.find(me.opts.quantityMinus);
            me.$quantitySubmit = me.$el.find(me.opts.quantitySubmit);
            me.$quantityInput = me.$el.find(me.opts.quantityInput);
            me.quantityStart = parseInt(me.$quantityInput.data('start'));
            me.quantityStep = parseInt(me.$quantityInput.data('step'));
            me.quantityFinish = parseInt(me.$quantityInput.data('finish')) - 1;

            me._on(me.$quantityPlus, 'click', $.proxy(me.quantityPlus, me));
            me._on(me.$quantityMinus, 'click', $.proxy(me.quantityMinus, me));
            me._on(me.$quantitySubmit, 'click', $.proxy(me.onSumbitForm, me));
            me._on(me.$quantityInput, 'change', $.proxy(me.quantityInputChange, me));
            me._on(me.$quantityInput, 'blur', $.proxy(me.quantityInputChangeArround, me));


        },
        onSumbitForm: function() {
            var me = this;
            me.$el.parent().submit();

        },
        checkQuantity: function(quantity) {
            var me = this;
            if (quantity > me.quantityFinish) {
                quantity = me.quantityFinish;
            }
            else if (quantity < me.quantityStart){
                quantity = me.quantityStart;
            }
            return quantity;
        },

        quantityPlus: function(e) {
            var me = this,
                newQuantity,
                currentQuantity = parseInt(me.$quantityInput.val());

            e.preventDefault();
            if (me.quantityStep > 1) {
                newQuantity = currentQuantity + me.quantityStep;
            }
            else {
                newQuantity = currentQuantity + 1;
            }

            currentQuantity = me.checkQuantity(currentQuantity);
            if (newQuantity !== currentQuantity) {
                me.$quantityInput.val(newQuantity).trigger('change');
            }

            return false;
        },
        quantityMinus: function(e) {
            var me = this,
                newQuantity,
                currentQuantity = parseInt(me.$quantityInput.val());

            e.preventDefault();
            if (me.quantityStep > 1) {
                newQuantity = currentQuantity - me.quantityStep;
            }
            else {
                newQuantity = currentQuantity - 1;
            }

            currentQuantity = me.checkQuantity(currentQuantity);
            if (newQuantity !== currentQuantity) {
                me.$quantityInput.val(newQuantity).trigger('change');
            }
            return false;
        },
        quantityInputChangeArround: function () {
            var me = this,
                currentQuantity = parseInt(me.$quantityInput.val()),
                quantityInteger = Math.trunc(currentQuantity/me.quantityStep),
                quantityFull = (currentQuantity/me.quantityStep).toFixed(2),
                quantityResult = quantityFull - quantityInteger;

            if ( 0 < quantityResult && quantityResult < 0.5) {
                var result = me.quantityStep*quantityInteger;
                me.$quantityInput.val(result).trigger('change');
            }
            else if ( 0.5 <= quantityResult && quantityResult < 1) {
                var result = me.quantityStep*quantityInteger + me.quantityStep;
                me.$quantityInput.val(result).trigger('change');
            }
        },
        quantityInputChange: function() {
            var me = this,
                currentQuantity = parseInt(me.$quantityInput.val());

            if (currentQuantity < me.quantityStart) {
                me.$quantityInput.val(me.quantityStart).trigger('change');
            }
            if (currentQuantity > me.quantityFinish) {
                me.$quantityInput.val(me.quantityFinish).trigger('change');
            }
            if (!currentQuantity) {
                me.$quantityInput.val(me.quantityStart).trigger('change');
            }
        },

    });
    $.subscribe('plugin/swProductSlider/onLoadItemsSuccess', function() {
        window.StateManager.addPlugin('[data-quantitySelect]', 'quantitySelect');
    });
    $.subscribe('plugin/swAjaxVariant/onRequestData', function () {
        window.StateManager.addPlugin('[data-quantitySelect]', 'quantitySelect');
    });

    window.StateManager.addPlugin('[data-quantitySelect]', 'quantitySelect');

}(jQuery, window));
