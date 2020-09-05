(function($, window) {

    $.plugin('languageCountry', {

        defaults: {
            /**
             * The Google Maps API Key.
             *
             * @type {String}
             */
            googleMapKey: 'AIzaSyCkFlRaPZFNN0eD66eYEe45cQ0VNhszvo8',
        },

        /**
         * Initializes the plugin
         *
         * @public
         * @method init
         */
        init: function () {
            var me = this,
                opts = me.opts;

            var localeCheck = StorageManager.getItem('local', 'localeCheck');

            if(localeCheck) {
                return;
            }

            // Config values
            me.shopPath = window.languageSwitcherShopPath || {};

            if ("geolocation" in navigator) {
                // check if geolocation is supported/enabled on current browser
                navigator.geolocation.getCurrentPosition(
                    function success(position) {
                        // for when getting location is a success
                        console.log('latitude', position.coords.latitude, 'longitude', position.coords.longitude);
                        me.getAddress(position.coords.latitude, position.coords.longitude);
                    },
                    function error(error_message) {
                        // for when getting location results in an error
                        console.error('An error has occured while retrieving' + 'location', error_message);
                        me.ipLookUp();
                    }
                );

            } else {
                // geolocation is not supported
                // get your location some other way
                console.log('geolocation is not enabled on this browser')
                me.ipLookUp()
            }

            me.registerEvents();

        },

        /**
         * Registers the listener for the window resize.
         * Also adds the click/tap listeners for the navigation buttons.
         *
         * @public
         * @method registerEvents
         */
        registerEvents: function () {
            var me = this;


            $.publish('plugin/languageCountry/onRegisterEvents', [ me ]);
        },


        /**
         * Will be called when geolocation in navigator failed
         *
         * @public
         * @method ipLookUp
         *
         */
        ipLookUp: function () {
            var me = this,
                opts = me.opts,
                xhttp = new XMLHttpRequest();

            xhttp.onreadystatechange = function()
            {
                if (this.readyState == 4 && this.status == 200)
                {
                    var responseText = JSON.parse(this.responseText);
                    me.countryCode = responseText.country;

                    $.publish('plugin/languageCountry/getCountryCode', [ me, me.countryCode ]);

                    console.log('ipLookUp', me.countryCode);
                }
            };

            xhttp.open("GET", 'https://ipapi.co/json', true);
            xhttp.send();
        },

        /**
         * Will be called when geolocation in navigator succeed
         *
         * @public
         * @method getAddress
         *
         */
        getAddress : function (latitude, longitude) {
            var me = this;

            var xhttp = new XMLHttpRequest();

            xhttp.onreadystatechange = function()
            {
                if (this.readyState == 4 && this.status == 200)
                {
                    try {
                        console.log(this.responseText);
                        var responseText = JSON.parse(this.responseText);

                        if ( responseText &&
                                "results" in responseText &&
                                Array.isArray(responseText["results"]) &&
                                responseText["results"].length > 0 &&
                                "address_components" in responseText["results"][0] &&
                                Array.isArray(responseText["results"][0]["address_components"]) &&
                                responseText["results"][0]["address_components"].length > 0 )
                        {
                            for (index = 0; index < responseText["results"][0]["address_components"].length; index++)
                            {
                                if ( "types" in responseText["results"][0]["address_components"][index] &&
                                    Array.isArray(responseText["results"][0]["address_components"][index]["types"]) &&
                                    responseText["results"][0]["address_components"][index]["types"].length == 2 &&
                                    responseText["results"][0]["address_components"][index]["types"].indexOf("country") >= 0 &&
                                    responseText["results"][0]["address_components"][index]["types"].indexOf("political") >= 0 )
                                {
                                    me.countryCode = responseText["results"][0]["address_components"][index]["short_name"];
                                    break;
                                }
                            } 
                        } else {
                            me.ipLookUp();
                        }
                        
                    } catch (e) {}

                    $.publish('plugin/languageCountry/getCountryCode', [ me, me.countryCode ]);
                    // TODO: Mit korrektem API Key die Response prüfen
                    console.log('getAddress',me.countryCode);
                    console.log('API Key',me.opts.googleMapKey);
                    console.log('getAddress', 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' + latitude + ',' + longitude + '&key=' + me.opts.googleMapKey);
                } else {
                    me.ipLookUp();
                }
            };

            xhttp.open("GET", 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' + latitude + ',' + longitude + '&key=' + me.opts.googleMapKey, true);
            xhttp.send();
        },

        destroy: function () {
            var me = this;
            me._destroy();
        }

    });

    $(function() {
        window.StateManager.addPlugin('body[data-languageSwitcher="true"]','languageCountry', ['xs', 's', 'm', 'l', 'xl']);
    });

})(jQuery, window);