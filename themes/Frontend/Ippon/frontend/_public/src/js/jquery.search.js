
    /**
     * Shopware Search Plugin.
     *
     * The plugin controlling the search field behaviour in all possible states
     */
    $.overridePlugin('swSearch', {
        
        onClickSearchEntry: function (event) {
            var me = this,
                $el = me.$el,
                opts = me.opts;

            $.publish('plugin/swSearch/onClickSearchEntry', [ me, event ]);

            if (!StateManager.isCurrentState([ "xs", "s", "m", "l" ])) {
                return;
            }
            
            event.preventDefault();
            event.stopPropagation();

            $el.hasClass(opts.activeCls) ? me.closeMobileSearch() : me.openMobileSearch();
        }

    });


