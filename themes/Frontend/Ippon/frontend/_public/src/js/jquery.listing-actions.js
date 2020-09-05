;(function ($, window, StateManager, undefined) {
    'use strict';

    var $body = $('body');

    /**
     * Plugin for handling the filter functionality and
     * all other actions for changing the product listing.
     * It handles the current set of category parameters and applies
     * them to the current top location url when something was
     * changed by the user over the filter form, action forms or
     * the action links.
     *
     * ** Filter Form **
     * The filter form exists of different filter components,
     * the filter submit button and the labels for active filters.
     * Each component is rendered in a single panel and has its own functionality.
     * All single components are handled by the "filterComponent" plugin.
     * The plugin for the components fires correct change events for each type
     * of component, so the "listingActions" plugin can listen on the changes
     * of the user. A filter form has to be a normal form with the selector,
     * which is set in the plugin options, so the form can be found by the plugin.
     * The actual submitting of the form will always be prevented to build the complex
     * category parameters out of the serialized form data.
     *
     * Example:
     * <form id="filter" method="get" data-filter-form="true">
     *
     * ** Action Forms **
     * You can apply different category parameters over additional action forms.
     * In most cases these forms are auto submitting forms using the "autoSubmit" plugin,
     * which change just one parameter via a combo- or checkbox. So with these
     * action forms you have the possibility to apply all kind of category parameters
     * like sorting, layout type, number of products per page etc.
     *
     * Example:
     * <form method="get" data-action-form="true">
     *  <select name="{$shortParameters.sSort}" data-auto-submit="true">
     *      {...}
     *  </select>
     * </form>
     *
     * ** Action Links **
     * You can also apply different category parameter via direct links.
     * Just use the corresponding get parameters in the href attribute of the link.
     * The new parameter will be added to the existing category parameters.
     * If the parameter already exists the value will be updated with the new one.
     *
     * Example:
     * <a href="?p=1&l=list" data-action-link="true">list view</a>
     *
     */
    $.overridePlugin('swListingActions', {

        defaults: {

            /**
             * The selector for the filter panel form.
             */
            filterFormSelector: '*[data-filter-form="true"]',

            /**
             * The selector for the single filter components.
             */
            filterComponentSelector: '*[data-filter-type]',

            /**
             * The selector for the button which shows and hides the filter panel.
             */
            filterTriggerSelector: '*[data-filter-trigger="true"]',

            /**
             * The selector for the icon inside the filter trigger button.
             */
            filterTriggerIconSelector: '.action--collapse-icon',

            /**
             * The selector for the filter panel element.
             */
            filterContainerSelector: '.action--filter-options',

            /**
             * The selector for the inner filter container which used to for the loading indicator
             * if the off canvas menu is active
             */
            filterInnerContainerSelector: '.filter--container',

            /**
             * The selector for additional listing action forms.
             */
            actionFormSelector: '*[data-action-form="true"]',

            /**
             * The selector for additional listing action links.
             */
            actionLinkSelector: '*[data-action-link="true"]',

            /**
             * The selector for the container where the active filters are shown.
             */
            activeFilterContSelector: '.filter--active-container',

            /**
             * The selector for the button which applies the filter changes.
             */
            applyFilterBtnSelector: '.filter--btn-apply',

            /**
             * The css class for active filter labels.
             */
            activeFilterCls: 'filter--active',

            /**
             * The close icon element which is used for the active filter labels.
             */
            activeFilterIconCls: 'filter--active-icon',

            /**
             * The css class for the filter panel when it is completely collapsed.
             */
            collapsedCls: 'is--collapsed',

            /**
             * The css class for the filter container when it shows only the preview of the active filters.
             */
            hasActiveFilterCls: 'is--active-filter',

            /**
             * The css class for active states.
             */
            activeCls: 'is--active',

            /**
             * The css class for disabled states.
             */
            disabledCls: 'is--disabled',

            /**
             * Selector for the element that contains the found product count.
             */
            filterCountSelector: '.filter--count',

            /**
             * Class that will be added to the apply filter button
             * when loading the results.
             */
            loadingClass: 'is--loading',

            /**
             * The characters used as a prefix to identify property field names.
             * The properties will be merged in one GET parameter.
             * For example properties with field names beginning with __f__"ID"
             * will be merged to &f=ID1|ID2|ID3|ID4 etc.
             *
             */
            propertyPrefixChar: '__',

            /**
             * The buffer time in ms to wait between each action before firing the ajax call.
             */
            bufferTime: 850,

            /**
             * The time in ms for animations.
             */
            animationSpeed: 400,

            /** Css class which will be added when the user uses instant filter results */
            instantFilterActiveCls: 'is--instant-filter-active',

            /**
             * class to select the listing div
             */
            listingSelector: '.listing--container > .listing',

            /**
             * class to select the pagination bars
             */
            paginationSelector: '.listing--paging.panel--paging',

            /**
             * data attribute which indicates whether infinite scrolling is used or not
             */
            infiniteScrollingAttribute: 'data-infinite-scrolling',

            /**
             * selector for the page size select box
             */
            paginationBarPerPageSelector: '.per-page--field.action--field',

            /**
             * selector for the hidden input field of the filter form which stores the current page
             */
            pageInputSelector: 'input[name=p]',

            /**
             * selector for the hidden input field of the filter form which stores the current sorting
             */
            sortInputSelector: 'input[name=o]',

            /**
             * selector for the hidden input field of the filter form which stores the current amount of products per page
             */
            perPageInputSelector: 'input[name=n]',

            /**
             * selector for the sorting select box
             */
            sortActionFormSelector: '.action--sort',

            sortActionFormSelectorItem: '.sort--field.action--field',

            /**
             * selector for the products per page select box
             */
            perPageActionFormSelector: '.action--per-page',

            /**
             * selector for the wrapper of the whole listing
             */
            listingWrapperSelector: '.listing--wrapper',

            /**
             * The selector for the element which get the loading indicator after customer activates a filter
             */
            loadingIndSelector: '.listing--wrapper',

            /**
             * The selector for "no filter result found" container
             */
            noResultContainerSelector: '.listing-no-filter-result .alert',

            /**
             * Class for loading indicator, added and removed on the configurable `listingSelector` element
             */
            isLoadingCls: 'is--loading',

            /**
             * Configuration for the loading indicator
             */
            loadingIndConfig: {
                theme: 'light',
                animationSpeed: 100,
                closeOnClick: false
            },

            /**
             * selector for the filter close button, which is only visible in off canvas
             */
            filterCloseBtnSelector: '.filter--close-btn',

            /**
             * icon for the filter close button
             */
            closeFilterOffCanvasBtnIcon: '<i class="icon--arrow-right"></i>',

            /**
             * selector for the search page headline
             */
            searchHeadlineProductCountSelector: '.search--headline .headline--product-count',

            /**
             * selector for the filter facet container
             */
            filterFacetContainerSelector: '.filter--facet-container',

            /**
             * selector for the filter action button bottom
             */
            filterActionButtonBottomSelector: '.filter--actions.filter--actions-bottom',

            /**
             * selector for the parent of the loading indicator in if the filters in sidebar mode
             */
            sidebarLoadingIndicatorParentSelector: '.content-main--inner',

            /**
             * selector for the jquery.add-article plugin to enable support for the off canvas cart
             */
            addArticleSelector: '*[data-add-article="true"]',

            /**
             * Threshold for the scroll position when the user switches pages (in both modes e.g. infinite scrolling & page change)
             */
            listingScrollThreshold: -10
        },

        /**
         * Initializes the plugin.
         */
        init: function () {
            var me = this,
                filterCount;

            me.applyDataAttributes();

            $('.sidebar-filter--loader').appendTo('.sidebar-filter--content');
            me.$filterForm = $(me.opts.filterFormSelector);
            me.$filterComponents = me.$filterForm.find(me.opts.filterComponentSelector);
            me.$filterTrigger = me.$el.find(me.opts.filterTriggerSelector);
            me.$filterTriggerIcon = me.$filterTrigger.find(me.opts.filterTriggerIconSelector);
            me.$filterCont = $(me.opts.filterContainerSelector);
            me.$actionForms = $(me.opts.actionFormSelector);
            me.$actionLinks = $(me.opts.actionLinkSelector);
            me.$activeFilterCont = me.$filterForm.find(me.opts.activeFilterContSelector);
            me.$applyFilterBtn = me.$filterForm.find(me.opts.applyFilterBtnSelector);
            me.$listing = $(me.opts.listingSelector);
            me.$pageInput = $(me.$filterForm.find(me.opts.pageInputSelector));
            me.$sortInput = $(me.$filterForm.find(me.opts.sortInputSelector));
            me.$perPageInput = $(me.$filterForm.find(me.opts.perPageInputSelector));
            me.$listingWrapper = me.$el.parent(me.opts.listingWrapperSelector);
            me.$closeFilterOffCanvasBtn = $(me.opts.filterCloseBtnSelector);
            me.$filterFacetContainer = me.$filterForm.find(me.opts.filterFacetContainerSelector);
            me.$filterActionButtonBottom = me.$filterForm.find(me.opts.filterActionButtonBottomSelector);
            me.$sidebarModeLoadionIndicator = $(me.opts.sidebarLoadingIndicatorParentSelector);
            me.$noFilterResultContainer = $(me.opts.noResultContainerSelector);

            me.searchHeadlineProductCount = $(me.opts.searchHeadlineProductCountSelector);
            me.listingUrl = me.$filterForm.attr('data-listing-url');
            me.loadFacets = me.$filterForm.attr('data-load-facets') === 'true';
            me.showInstantFilterResult = me.$filterForm.attr('data-instant-filter-result') === 'true';
            me.isInfiniteScrolling = me.$listing.attr(me.opts.infiniteScrollingAttribute);
            me.isFilterpanelInSidebar = me.$filterForm.attr('data-is-in-sidebar') === 'true';

            me.controllerURL = window.location.href.split('?')[0];
            me.resetLabel = me.$activeFilterCont.attr('data-reset-label');
            me.propertyFieldNames = [];
            me.activeFilterElements = {};
            me.categoryParams = {};
            me.urlParams = '';
            me.bufferTimeout = 0;
            me.closeFilterOffCanvasBtnText = me.$closeFilterOffCanvasBtn.html();
            me.closeFilterOffCanvasBtnTextWithProducts = me.$closeFilterOffCanvasBtn.attr('data-show-products-text');

            me.getPropertyFieldNames();
            me.setCategoryParamsFromTopLocation();
            me.createActiveFiltersFromCategoryParams();
            me.createUrlParams();

            filterCount = Object.keys(me.activeFilterElements).length;

            me.updateFilterTriggerButton(filterCount > 1 ? filterCount - 1 : filterCount);
            me.initStateHandling();
            me.registerEvents();

            me.$loadingIndicatorElement = $(me.opts.loadingIndSelector);
            me.$offCanvasLoadingIndicator = $(me.opts.filterInnerContainerSelector);

            $.subscribe('action/fetchListing', $.proxy(me.onSendListingRequest, me));

            me.disableActiveFilterContainer(true);
            if ($( window ).width() <= 1259) {
                $(this.opts.sortActionFormSelectorItem).val('0');
            }
            var isFiltered = me.$filterForm.attr('data-is-filtered');
            if (isFiltered > 0 && me.loadFacets) {
                me.getFilterResult(me.urlParams, true, false);
            }
        },

        updateListing: function (response) {
            var html,
                listing = this.$listing,
                pages;

            if (!response.hasOwnProperty('listing')) {
                listing.removeClass(this.opts.isLoadingCls);
                return;
            }

            this.updateFilterCloseButton(response.totalCount);
            this.updateSearchHeadline(response.totalCount);
            this.updateNoResultContainer(response.totalCount);

            html = response.listing.trim();

            listing.html(html);

            window.picturefill();

            listing.removeClass(this.opts.isLoadingCls);

            if ($( window ).width() <= 1259) {
                $(this.opts.sortActionFormSelectorItem).val('0');
            }

            window.history.pushState('data', '', window.location.href.split('?')[0] + this.urlParams);

            $.publish('plugin/swListingActions/updateListing', [this, html]);

            StateManager.updatePlugin(this.opts.addArticleSelector, 'swAddArticle');

            if (this.isInfiniteScrolling) {
                pages = Math.ceil(response.totalCount / this.$perPageInput.val());

                // update infinite scrolling plugin and data attributes for infinite scrolling
                listing.attr('data-pages', pages);
                listing.data('plugin_swInfiniteScrolling').destroy();
                StateManager.addPlugin(this.opts.listingSelector, 'swInfiniteScrolling');
                $.publish('plugin/swListingActions/updateInfiniteScrolling', [this, html, pages]);
            } else {
                this.updatePagination(response);
                this.scrollToTopPagination();
            }
        },


    });
})(jQuery, window, StateManager, undefined);
