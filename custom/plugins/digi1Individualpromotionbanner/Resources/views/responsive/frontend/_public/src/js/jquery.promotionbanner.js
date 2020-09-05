(function($, window) {
    var $window = $(window);

    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+ d.toUTCString();
        
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function getCookie(cname) {
        var name = cname + "=", decodedCookie = decodeURIComponent(document.cookie), ca = decodedCookie.split(';');
        
        for(var i = 0; i <ca.length; i++) {
            var c = ca[i];
            
            while (c.charAt(0) === ' ') {
                c = c.substring(1);
            }
            
            if (c.indexOf(name) === 0) {
                return c.substring(name.length, c.length);
            }
        }
        
        return "";
    }
    
    function createCheckCookieTimer(promotionbannerid, collapsiblecookielifetime, cookiepermissioncheck, namecookiecookienote) {
        checkCookieInterval = window.setInterval(checkCookie, 1000, promotionbannerid, collapsiblecookielifetime, cookiepermissioncheck, namecookiecookienote);
    }
    
    function checkCookie(p_id, cookielifetime, cookiepermissioncheck, namecookiecookienote){
        if(cookiepermissioncheck == 0){
            var cookieValue = getCookie(namecookiecookienote);

            if (cookieValue) {            
                window.clearInterval(checkCookieInterval);

                setCookie(p_id, 1, cookielifetime);
            }
        }else if(cookiepermissioncheck == 1){
            if($.getCookiePreference("promotionbanner-")) {
                window.clearInterval(checkCookieInterval);

                setCookie(p_id, 1, cookielifetime);
            } 
        }
    }

    $('.promotionbanner-collapse--header').on('click', function() {
        var promotionbannerid = $(this).attr("data-promotionbannerid"),
            collapsiblecookielifetime = $(this).attr("data-collapsiblecookielifetime"),
            collapseicon = $(this).attr("data-collapseicon"),
            cookiepermissioncheck = $(this).attr("data-cookiepermissioncheck"),
            namecookiecookienote = $(this).attr("data-namecookiecookienote");
            
        if($(this).hasClass("collapse-is-open")){            
            createCheckCookieTimer("promotionbanner-"+promotionbannerid, collapsiblecookielifetime, cookiepermissioncheck, namecookiecookienote);

            $(".promotionbanner-outer-"+promotionbannerid+" .promotionbanner-container").addClass("is--hidden");
            $(".promotionbanner-outer-"+promotionbannerid+" .promotionbanner-collapse--header").removeClass("collapse-is-open").addClass("collapse-is-closed");
            
            if(collapseicon == 1){
                $(".promotionbanner-outer-"+promotionbannerid+" .promotionbanner-collapse--header").addClass("is--hidden");
            }
        }else if($(this).hasClass("collapse-is-closed")){            
            createCheckCookieTimer("promotionbanner-"+promotionbannerid, -collapsiblecookielifetime, cookiepermissioncheck, namecookiecookienote);

            $(".promotionbanner-outer-"+promotionbannerid+" .promotionbanner-container").removeClass("is--hidden");
            $(".promotionbanner-outer-"+promotionbannerid+" .promotionbanner-collapse--header").removeClass("collapse-is-closed").addClass("collapse-is-open");
        }
    });

    $(".promotionbanner-outer.promotionbanner-modalbox .promotionbanner-container").each(function(index){  
        var thisVal = $(this),
            promotionbanner_id = thisVal.attr("data-promotionbannerid"),
            promotionbanner_cookie = getCookie("promotionbanner-"+promotionbanner_id),
            timedelay = thisVal.attr('data-timedelay'),
            collapsiblecookielifetime = thisVal.attr('data-collapsiblecookielifetime'),
            collapse = thisVal.attr('data-collapse'),
            cookiepermissioncheck = thisVal.attr('data-cookiepermissioncheck'),
            namecookiecookienote = thisVal.attr('data-namecookiecookienote');

        if (!promotionbanner_cookie) {
            modalboxOpen = function() {
                var parent = $(thisVal).parent(),
                    modalboxcontent = $.parseHTML((parent.parent()).html());

                if (!promotionbanner_cookie) {
                    $.modal.open(
                        modalboxcontent, {
                            sizing: 'content',
                            width: thisVal.attr('data-width'),
                            closeOnOverlay: true,
                            additionalClass: 'promotionbanner--modal',
                            onClose: function () {
                                if(collapse == 1){
                                    if (!promotionbanner_cookie) {
                                        createCheckCookieTimer("promotionbanner-"+promotionbanner_id, collapsiblecookielifetime, cookiepermissioncheck, namecookiecookienote);
                                    }
                                }
                            }
                        }
                    );
                }
            }

            if (!promotionbanner_cookie) {
                modalboxCounter = 0;
                modalboxTimedelay = setInterval(modalboxTimedelayFunction, 1000, thisVal, timedelay);
            }
         }
    });

    function modalboxTimedelayFunction(thisVal, timedelay){
        modalboxCounter++;

        if (modalboxCounter >= timedelay) {
            if(!(thisVal.hasClass("is--hidden"))){
                if($(".js--modal.sizing--content").length > 0) {
                    if($(".js--modal.sizing--content").is(":hidden")){
                        modalboxOpen();

                        clearInterval(modalboxTimedelay);
                    }else{
                        clearInterval(modalboxTimedelay);

                        modalboxCounter = 0;
                        modalboxTimedelay = setInterval(modalboxTimedelayFunction, 1000, thisVal, timedelay);
                    }
                }else{
                    modalboxOpen();

                    clearInterval(modalboxTimedelay);
                }
            }
        }
    }

    $.plugin('digi1PromotionbannerShowInSmartphoneportrait', {
        defaults: {
        },
        init: function() {
            var me = this;

            me.applyDataAttributes();

            me.createElement();
            me.registerEvents();
        },
        createElement: function() {
            var me = this,
                element = $(me.$el),
                collapse = element.attr("data-collapse"),
                collapseicon = element.attr("data-collapseicon"),
                promotionbanner_id = element.attr("data-promotionbannerid");
            
            if(collapse == 1){
                var promotionbanner_cookie = getCookie("promotionbanner-"+promotionbanner_id);

                if (!promotionbanner_cookie) {
                    $(".promotionbanner-outer-"+promotionbanner_id+".show-in-smartphoneportrait .promotionbanner-collapse--header").removeClass("collapse-is-closed").addClass("collapse-is-open").removeClass("is--hidden");                        
                    
                    $(".promotionbanner-outer-"+promotionbanner_id+".show-in-smartphoneportrait .promotionbanner-container").removeClass("is--hidden");
                }else{
                    $(".promotionbanner-outer-"+promotionbanner_id+".show-in-smartphoneportrait .promotionbanner-collapse--header").removeClass("collapse-is-open").addClass("collapse-is-closed");
     
                    if(collapseicon == 0){
                        $(".promotionbanner-outer-"+promotionbanner_id+".show-in-smartphoneportrait .promotionbanner-collapse--header").removeClass("is--hidden");
                    }
                }
            }else{
                $(".promotionbanner-outer-"+promotionbanner_id+".show-in-smartphoneportrait .promotionbanner-container").removeClass("is--hidden");
                $(".promotionbanner-outer-"+promotionbanner_id+".show-in-smartphoneportrait .promotionbanner-collapse--header").removeClass("collapse-is-closed").addClass("collapse-is-open");
            }

            $.publish('plugin/digi1PromotionbannerShowInSmartphoneportrait/onCreateElement', [ me ]);
        },
        registerEvents: function() {
            var me = this;
        },
        destroy: function() {
            var me = this;

            me._destroy();

            $(".show-in-smartphoneportrait .promotionbanner-collapse--header").addClass("is--hidden");
            $(".show-in-smartphoneportrait .promotionbanner-container").addClass("is--hidden");
        }
    });

    $.plugin('digi1PromotionbannerShowInSmartphonelandscape', {
        defaults: {
        },
        init: function() {
            var me = this;

            me.applyDataAttributes();

            me.createElement();
            me.registerEvents();
        },
        createElement: function() {
            var me = this,
                element = $(me.$el),
                collapse = element.attr("data-collapse"),
                collapseicon = element.attr("data-collapseicon"),
                promotionbanner_id = element.attr("data-promotionbannerid");
            
            if(collapse == 1){
                var promotionbanner_cookie = getCookie("promotionbanner-"+promotionbanner_id);

                if (!promotionbanner_cookie) {
                    $(".promotionbanner-outer-"+promotionbanner_id+".show-in-smartphonelandscape .promotionbanner-collapse--header").removeClass("collapse-is-closed").addClass("collapse-is-open").removeClass("is--hidden");                        
                    
                    $(".promotionbanner-outer-"+promotionbanner_id+".show-in-smartphonelandscape .promotionbanner-container").removeClass("is--hidden");
                }else{
                    $(".promotionbanner-outer-"+promotionbanner_id+".show-in-smartphonelandscape .promotionbanner-collapse--header").removeClass("collapse-is-open").addClass("collapse-is-closed");
     
                    if(collapseicon == 0){
                        $(".promotionbanner-outer-"+promotionbanner_id+".show-in-smartphonelandscape .promotionbanner-collapse--header").removeClass("is--hidden");
                    }
                }
            }else{
                $(".promotionbanner-outer-"+promotionbanner_id+".show-in-smartphonelandscape .promotionbanner-container").removeClass("is--hidden");
                $(".promotionbanner-outer-"+promotionbanner_id+".show-in-smartphonelandscape .promotionbanner-collapse--header").removeClass("collapse-is-closed").addClass("collapse-is-open");
            }

            $.publish('plugin/digi1PromotionbannerShowInSmartphonelandscape/onCreateElement', [ me ]);
        },
        registerEvents: function() {
            var me = this;
        },
        destroy: function() {
            var me = this;

            me._destroy();

            $(".show-in-smartphonelandscape .promotionbanner-collapse--header").addClass("is--hidden");
            $(".show-in-smartphonelandscape .promotionbanner-container").addClass("is--hidden");
        }
    });

    $.plugin('digi1PromotionbannerShowInTabletportrait', {
        defaults: {
        },
        init: function() {
            var me = this;

            me.applyDataAttributes();

            me.createElement();
            me.registerEvents();
        },
        createElement: function() {
            var me = this,
                element = $(me.$el),
                collapse = element.attr("data-collapse"),
                collapseicon = element.attr("data-collapseicon"),
                promotionbanner_id = element.attr("data-promotionbannerid");
            
            if(collapse == 1){
                var promotionbanner_cookie = getCookie("promotionbanner-"+promotionbanner_id);

                if (!promotionbanner_cookie) {
                    $(".promotionbanner-outer-"+promotionbanner_id+".show-in-tabletportrait .promotionbanner-collapse--header").removeClass("collapse-is-closed").addClass("collapse-is-open").removeClass("is--hidden");                        
                    
                    $(".promotionbanner-outer-"+promotionbanner_id+".show-in-tabletportrait .promotionbanner-container").removeClass("is--hidden");
                }else{
                    $(".promotionbanner-outer-"+promotionbanner_id+".show-in-tabletportrait .promotionbanner-collapse--header").removeClass("collapse-is-open").addClass("collapse-is-closed");
     
                    if(collapseicon == 0){
                        $(".promotionbanner-outer-"+promotionbanner_id+".show-in-tabletportrait .promotionbanner-collapse--header").removeClass("is--hidden");
                    }
                }
            }else{
                $(".promotionbanner-outer-"+promotionbanner_id+".show-in-tabletportrait .promotionbanner-container").removeClass("is--hidden");
                $(".promotionbanner-outer-"+promotionbanner_id+".show-in-tabletportrait .promotionbanner-collapse--header").removeClass("collapse-is-closed").addClass("collapse-is-open");
            }

            $.publish('plugin/digi1PromotionbannerShowInTabletportrait/onCreateElement', [ me ]);
        },
        registerEvents: function() {
            var me = this;
        },
        destroy: function() {
            var me = this;

            me._destroy();

            $(".show-in-tabletportrait .promotionbanner-collapse--header").addClass("is--hidden");
            $(".show-in-tabletportrait .promotionbanner-container").addClass("is--hidden");
        }
    });

    $.plugin('digi1PromotionbannerShowInTabletlandscape', {
        defaults: {
        },
        init: function() {
            var me = this;

            me.applyDataAttributes();

            me.createElement();
            me.registerEvents();
        },
        createElement: function() {
            var me = this,
                element = $(me.$el),
                collapse = element.attr("data-collapse"),
                collapseicon = element.attr("data-collapseicon"),
                promotionbanner_id = element.attr("data-promotionbannerid");
            
            if(collapse == 1){
                var promotionbanner_cookie = getCookie("promotionbanner-"+promotionbanner_id);

                if (!promotionbanner_cookie) {
                    $(".promotionbanner-outer-"+promotionbanner_id+".show-in-tabletlandscape .promotionbanner-collapse--header").removeClass("collapse-is-closed").addClass("collapse-is-open").removeClass("is--hidden");                        
                    
                    $(".promotionbanner-outer-"+promotionbanner_id+".show-in-tabletlandscape .promotionbanner-container").removeClass("is--hidden");
                }else{
                    $(".promotionbanner-outer-"+promotionbanner_id+".show-in-tabletlandscape .promotionbanner-collapse--header").removeClass("collapse-is-open").addClass("collapse-is-closed");
     
                    if(collapseicon == 0){
                        $(".promotionbanner-outer-"+promotionbanner_id+".show-in-tabletlandscape .promotionbanner-collapse--header").removeClass("is--hidden");
                    }
                }
            }else{
                $(".promotionbanner-outer-"+promotionbanner_id+".show-in-tabletlandscape .promotionbanner-container").removeClass("is--hidden");
                $(".promotionbanner-outer-"+promotionbanner_id+".show-in-tabletlandscape .promotionbanner-collapse--header").removeClass("collapse-is-closed").addClass("collapse-is-open");
            }

            $.publish('plugin/digi1PromotionbannerShowInTabletlandscape/onCreateElement', [ me ]);
        },
        registerEvents: function() {
            var me = this;
        },
        destroy: function() {
            var me = this;

            me._destroy();

            $(".show-in-tabletlandscape .promotionbanner-collapse--header").addClass("is--hidden");
            $(".show-in-tabletlandscape .promotionbanner-container").addClass("is--hidden");
        }
    });

    $.plugin('digi1PromotionbannerShowInDesktop', {
        defaults: {
        },
        init: function() {
            var me = this;

            me.applyDataAttributes();

            me.createElement();
            me.registerEvents();
        },
        createElement: function() {
            var me = this,
                element = $(me.$el),
                collapse = element.attr("data-collapse"),
                collapseicon = element.attr("data-collapseicon"),
                promotionbanner_id = element.attr("data-promotionbannerid");
            
            if(collapse == 1){
                var promotionbanner_cookie = getCookie("promotionbanner-"+promotionbanner_id);

                if (!promotionbanner_cookie) {
                    $(".promotionbanner-outer-"+promotionbanner_id+".show-in-desktop .promotionbanner-collapse--header").removeClass("collapse-is-closed").addClass("collapse-is-open").removeClass("is--hidden");                        
                    
                    $(".promotionbanner-outer-"+promotionbanner_id+".show-in-desktop .promotionbanner-container").removeClass("is--hidden");
                }else{
                    $(".promotionbanner-outer-"+promotionbanner_id+".show-in-desktop .promotionbanner-collapse--header").removeClass("collapse-is-open").addClass("collapse-is-closed");
     
                    if(collapseicon == 0){
                        $(".promotionbanner-outer-"+promotionbanner_id+".show-in-desktop .promotionbanner-collapse--header").removeClass("is--hidden");
                    }
                }
            }else{
                $(".promotionbanner-outer-"+promotionbanner_id+".show-in-desktop .promotionbanner-container").removeClass("is--hidden");
                $(".promotionbanner-outer-"+promotionbanner_id+".show-in-desktop .promotionbanner-collapse--header").removeClass("collapse-is-closed").addClass("collapse-is-open");
            }

            $.publish('plugin/digi1PromotionbannerShowInDesktop/onCreateElement', [ me ]);
        },
        registerEvents: function() {
            var me = this;
        },
        destroy: function() {
            var me = this;

            me._destroy();

            $(".show-in-desktop .promotionbanner-collapse--header").addClass("is--hidden");
            $(".show-in-desktop .promotionbanner-container").addClass("is--hidden");
        }
    });

    window.StateManager.addPlugin(
        '.promotionbanner-outer.show-in-smartphoneportrait',
        'digi1PromotionbannerShowInSmartphoneportrait',
        ['xs']
    );

    window.StateManager.addPlugin(
        '.promotionbanner-outer.show-in-smartphonelandscape',
        'digi1PromotionbannerShowInSmartphonelandscape',
        ['s']
    );

    window.StateManager.addPlugin(
        '.promotionbanner-outer.show-in-tabletportrait',
        'digi1PromotionbannerShowInTabletportrait',
        ['m']
    );

    window.StateManager.addPlugin(
        '.promotionbanner-outer.show-in-tabletlandscape',
        'digi1PromotionbannerShowInTabletlandscape',
        ['l',]
    );

    window.StateManager.addPlugin(
        '.promotionbanner-outer.show-in-desktop',
        'digi1PromotionbannerShowInDesktop',
        ['xl']
    );
})(jQuery, window);