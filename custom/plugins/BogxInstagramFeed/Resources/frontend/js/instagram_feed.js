/*
 * instagram_feed.js
 * xhr feed reader
 * @version 1.3.3
 * @extended & adapted by bogx 15-16.03.2020
 *
 */
(function(root, factory) {
    if (typeof define === "function" && define.amd) {
        define([], factory);
    } else if (typeof exports === "object") {
        module.exports = factory();
    } else {
        root.InstagramFeed = factory();
    }

    }(this, function() {
        var defaults = {
           'host': "https://www.instagram.com/",
            'username': '',
            'tag': '',
            'container': '',
            'display_profile': true,
            'display_biography': true,
            'display_gallery': true,
            'display_igtv': false,
            'get_data': false,
            'callback': null,
            'styling': true,
            'items': 8,
            'items_per_row': 4,
            'margin': 0,
            'image_size': 640,
            'cache_suffix': "",
        };

        var image_sizes = {
            "150": 0,
            "240": 1,
            "320": 2,
            "480": 3,
            "640": 4
        };

        var escape_map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;',
            '/': '&#x2F;',
            '`': '&#x60;',
            '=': '&#x3D;'
        };
        function escape_string(str){
            return str.replace(/[&<>"'`=\/]/g, function (char) {
                return escape_map[char];
            });
        }

        return function(opts) {
            this.options = Object.assign({}, defaults);
            this.options = Object.assign(this.options, opts);

            /* 15.03.2020 by bogx, username vs. hashtag*/
            var firstChar = this.options.username.charAt(0);
            if (firstChar === '#') {
                this.options.tag = this.options.username.substring(1);
                this.options.username = '';
            }
            /* end by bogx */

            this.is_tag = this.options.username === "";

            this.valid = true;
            if (this.options.username === "" && this.options.tag === "") {
                console.error("InstagramFeed: Error, no username or hashtag defined.");
                this.valid = false;
            } else if (!this.options.get_data && this.options.container === "") {
                console.error("InstagramFeed: Error, no container found.");
                this.valid = false;
            } else if (this.options.get_data && typeof this.options.callback != "function") {
                console.error("InstagramFeed: Error, invalid or undefined callback for get_data");
                this.valid = false;
            }

            this.get = function(callback) {
                var url = this.is_tag ? this.options.host + "explore/tags/" + this.options.tag : this.options.host + this.options.username,
                    xhr = new XMLHttpRequest();

                var _this = this;
                xhr.onload = function(e) {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            var data = xhr.responseText.split("window._sharedData = ")[1].split("<\/script>")[0];
                            data = JSON.parse(data.substr(0, data.length - 1));
                            data = data.entry_data.ProfilePage || data.entry_data.TagPage || null;
                            if (data === null) {
                                console.log(url);
                                console.error("InstagramFeed: Request error. No data retrieved: " + xhr.statusText);
                            } else {
                                data = data[0].graphql.user || data[0].graphql.hashtag;
                                callback(data, _this);
                            }
                        } else {
                            console.error("InstagramFeed: Request error. Response: " + xhr.statusText);
                        }
                    }
                };
                xhr.open("GET", url, true);
                xhr.send();
            };

            this.parse_caption = function(igobj, data) {
                if (typeof igobj.node.edge_media_to_caption.edges[0] !== "undefined" && igobj.node.edge_media_to_caption.edges[0].node.text.length !== 0) {
                    return igobj.node.edge_media_to_caption.edges[0].node.text;
                }

                if (typeof igobj.node.title !== "undefined" && igobj.node.title.length !== 0) {
                    return igobj.node.title;
                }

                if (typeof igobj.node.accessibility_caption !== "undefined" && igobj.node.accessibility_caption.length !== 0) {
                    return igobj.node.accessibility_caption;
                }
                return (this.is_tag ? data.name : data.username) + " image ";
            };

            this.display = function(data) {
                // Styling
                if (this.options.styling) {
                    var width = (100 - this.options.margin * 2 * this.options.items_per_row) / this.options.items_per_row;

                    /* 15.03.2020 by bogx, widget config data */
                    var bogx_config_element = document.getElementById('bogx_instagram_data');
                    var originCellWidth = bogx_config_element.getAttribute("data-cell_width");
                    var padding_x = bogx_config_element.getAttribute("data-padding_x");
                    var padding_y = bogx_config_element.getAttribute("data-padding_y");
                    //var cache_suffix = bogx_config_element.getAttribute("data-cache_suffix");
                    //if (cache_suffix !== '') cache_suffix = "_" + cache_suffix;
                    //this.options.cache_suffix = cache_suffix;

                    //*** Instagram Zellenbreite festlegen und je nach aktuelle Displaybreite anpassen
                    if (originCellWidth) {
                        var originCellWidthStr = originCellWidth + "%";

                        //prüfen ob, das Feed in einem "Fullscreen" Einkaufswelt-Elemnet eingeblendet werden soll
                        var fullscreen = $('#bogx_instagram_data').closest('section.emotion--container').attr('data-fullscreen');
                        if (fullscreen === "true") {
                            var parent_width = window.innerWidth;
                        } else {
                            var parent_width = bogx_config_element.getBoundingClientRect().width;
                        }

                        //var parent_width = bogx_config_element.offsetWidth;
                        var cellHeight = (parent_width - this.options.items_per_row * parseFloat(padding_x)) * parseFloat(originCellWidth) / 100;
                        //var cellHeight = cellWidth;
                        if (window.innerWidth < 320) {
                            var originCellWidthStr = "";
                            var cellHeight='';
                        }
                        //var cellHeight = cellWidth;
                    } else {
                        var originCellWidthStr = '';
                        var cellHeight='';
                    }

                    var layout = bogx_config_element.getAttribute("data-layout");
                    var profile = bogx_config_element.getAttribute("data-profile");
                    var captions = bogx_config_element.getAttribute("data-captions");
                    var counts = bogx_config_element.getAttribute("data-counts");

                    /* end by bogx */


                    var styles = {
                        'profile_container': " style='text-align:center;'",
                        'profile_image': " style='border-radius:10em;width:15%;max-width:125px;min-width:50px;'",
                        'profile_name': " style='font-size:1.2em;'",
                        'profile_biography': " style='font-size:1em;'",
                        //'gallery_image': " style='margin:" + this.options.margin + "% " + this.options.margin + "%;width:" + width + "%;float:left;'"
                        /* 15.03.2020 by bogx, styling for instagram_cell (posting) */
                        'instagram_cell': " style='padding:0 " + padding_x + "px " + padding_y + "px 0; width:" + originCellWidthStr + "; height:" + cellHeight + "px;'",
                        /* end by bogx */
                    };
                } else {
                    var styles = {
                        'profile_container': "",
                        'profile_image': "",
                        'profile_name': "",
                        'profile_biography': "",
                        //'gallery_image': "",
                        'instagram_cell': "",
                    };
                }

                // Profile
                var html_popup_profile = "", html_popup_username = "";
                if (profile === "1") {
                    html_popup_profile += "<div class='bogx--instagramshow-profile'>";
                    if (this.is_tag) {
                        html_popup_profile += "<a href='https://www.instagram.com/explore/tags/" + this.options.tag + "' title='go to istagram hashtag' rel='noopener' target='_blank'>";
                        html_popup_username = "<p class='name'>#" + this.options.tag + "</p>";
                    } else {
                        html_popup_profile += "<a href='https://www.instagram.com/" + this.options.username + "' title='go to istagram account' rel='noopener' target='_blank'>";
                        html_popup_username = "<p class='name'>@" + this.options.username + "</p>";
                    }

                    html_popup_profile += "<img class='image' src='" + data.profile_pic_url + "' />";
                    html_popup_profile += html_popup_username;
                    html_popup_profile += "</a></div>";
                }
                /*
                var html = "";
                if (this.options.display_profile) {
                    html += "<div class='instagram_profile'" + styles.profile_container + ">";
                    html += "<img class='instagram_profile_image' src='" + data.profile_pic_url + "' alt='" + (this.is_tag ? data.name + " tag pic" : data.username + " profile pic") + " profile pic'" + styles.profile_image + " />";
                    if (this.is_tag)
                        html += "<p class='instagram_tag'" + styles.profile_name + "><a href='https://www.instagram.com/explore/tags/" + this.options.tag + "' rel='noopener' target='_blank'>#" + this.options.tag + "</a></p>";
                    else
                        html += "<p class='instagram_username'" + styles.profile_name + ">@" + data.full_name + " (<a href='https://www.instagram.com/" + this.options.username + "' rel='noopener' target='_blank'>@" + this.options.username + "</a>)</p>";

                    if (!this.is_tag && this.options.display_biography)
                        html += "<p class='instagram_biography'" + styles.profile_biography + ">" + data.biography + "</p>";

                    html += "</div>";
                }
                */


                // Gallery
                //var html = "";
                if (this.options.display_gallery) {
                    var image_index = typeof image_sizes[this.options.image_size] !== "undefined" ? image_sizes[this.options.image_size] : image_sizes[640];

                    if (typeof data.is_private !== "undefined" && data.is_private === true) {
                        //html += "<p class='instagram_private'><strong>This profile is private</strong></p>";
                        html += "<li class='bogx--instagram-error'>This profile is private</li>";
                        this.options.container.insertAdjacentHTML('beforeend', html);
                    } else {
                        var imgs = (data.edge_owner_to_timeline_media || data.edge_hashtag_to_media).edges;
                        max = (imgs.length > this.options.items) ? this.options.items : imgs.length;

                        //html += "<div class='instagram_gallery'>";

                        for (var i = 0; i < max; i++) {
                            var html = "";
                            var url = "https://www.instagram.com/p/" + imgs[i].node.shortcode,
                                image, type_resource;
                            var node_id = imgs[i].node.id;
                            var caption = escape_string(this.parse_caption(imgs[i], data));


                            /* 15.03.2020 by bogx, counts for likes and comments */
                            var likescount = imgs[i].node.edge_liked_by.count;
                            var commentscount = imgs[i].node.edge_media_to_comment.count;
                            /* end by bogx */
                            //console.log(imgs[i].node);

                            switch (imgs[i].node.__typename) {
                                case "GraphSidecar":
                                    type_resource = "sidecar";
                                    image = imgs[i].node.thumbnail_resources[image_index].src;
                                    break;
                                case "GraphVideo":
                                    type_resource = "video";
                                    image = imgs[i].node.thumbnail_src;
                                    break;
                                default:
                                    type_resource = "image";
                                    image = imgs[i].node.thumbnail_resources[image_index].src;
                            }

                            if (this.is_tag) data.username = '';
                            //html += "<a href='" + url + "' class='instagram-" + type_resource + "' title='" + caption + "' rel='noopener' target='_blank'>";
                            //html += "<img src='" + image + "' alt='" + caption + "'" + styles.gallery_image + " />";
                            //html += "</a>";

                            /*** Layout = grid_linked ***/
                            var html_linked_start = "", html_linked_end = "";
                            if (layout === 'grid_linked') {
                                var html_linked_start = "<a href='" + url + "' class='bogx--instagram-linked' title='go to posting on instagram' rel='noopener' target='_blank'>";
                                var html_linked_end = "</a>";
                            }

                            /*** Layout = grid_hover ***/
                            var html_hover = "", html_popup_captions = "", html_popup_counts = "";
                            if (layout === 'grid_hover') {
                                //HOVER
                                html_hover += "<a href=\"#bogx_hidden-content_" + node_id +  "\" class=\"bogx--instagram-link\" >";
                                html_hover += "<div class=\"bogx--instagram-overlay\">";
                                if (captions === '1') {
                                    html_hover += "<div class=\"bogx--instagram-message\">" + caption + "</div>";
                                }
                                if (counts === '1') {
                                    html_hover += "<div class=\"bogx--instagram-meta bogx--flexbox-justifiy\">";
                                    html_hover += "<div class=\"bogx--meta-likes\">" + likescount + "</div>";
                                    html_hover += "<div class=\"bogx--meta-comments\">" + commentscount + "</div>";
                                    html_hover += "</div>";
                                }
                                html_hover += "</div></a>";

                                //POPUP
                                html_hover += "<div class=\"bogx--instagram-hidden\">";
                                html_hover += "<div id=\"bogx_hidden-content_" +  node_id + "\" style=\"max-width: 1040px;\">";
                                html_hover += "<div class=\"bogx--instagramshow-html\">";
                                html_hover += "<div class=\"bogx--instagramshow-media\">";
                                html_hover += "<a href='" + url + "' class='linked' title='go to posting on instagram' rel='noopener' target='_blank'>";
                                html_hover += "<img src=\"" + image + "\" />";
                                html_hover += "</a>";
                                html_hover += "</div>";
                                html_hover += "<div class=\"bogx--instagramshow-text\">";
                                //Profile
                                html_hover += html_popup_profile;
                                //Caption
                                if (captions === '1') {
                                    html_hover += "<div class=\"bogx--instagramshow-caption\">" + caption + "</div>";
                                }
                                //Counts
                                if (counts === '1') {
                                    html_hover += "<div class=\"bogx--instagramshow-instagrammeta bogx--flexbox-justifiy\">";
                                    html_hover += "<div class=\"bogx--meta-likes\">" + likescount + "</div>";
                                    html_hover += "<div class=\"bogx--meta-comments\">" + commentscount + "</div>";
                                    html_hover += "</div>";
                                }
                                html_hover += "</div>";

                                html_hover += "</div></div></div>";
                            }


                            /*** FEED GALLERY ***/
                            html += "<li class='bogx--instagram-cell'" + styles.instagram_cell + ">";
                            html += html_linked_start;
                            html += "<img src='" + image + "' class='bogx--instagram-img' />";
                            html += html_linked_end;
                            html += html_hover;
                            html += "</li>";

                            //Das Feed Zelle für Zelle in der Schleife generieren/ausgeben - und nicht das ganze Feed auf Einmal
                            this.options.container.insertAdjacentHTML('beforeend', html);
                            /* end by bogx */

                        }

                    }
                }

                // IGTV
                if (this.options.display_igtv && typeof data.edge_felix_video_timeline !== "undefined") {
                    var igtv = data.edge_felix_video_timeline.edges,
                        max = (igtv.length > this.options.items) ? this.options.items : igtv.length;
                    if (igtv.length > 0) {
                        html += "<div class='instagram_igtv'>";
                        for (var i = 0; i < max; i++) {
                            var url = "https://www.instagram.com/p/" + igtv[i].node.shortcode,
                                caption = this.parse_caption(igtv[i], data);

                            html += "<a href='" + url + "' rel='noopener' title='" + caption + "' target='_blank'>";
                            html += "<img src='" + igtv[i].node.thumbnail_src + "' alt='" + caption + "'" + styles.gallery_image + " />";
                            html += "</a>";
                        }
                        html += "</div>";
                    }
                }

                //this.options.container.innerHTML = html;

                /* 15.03.2020 by bogx, unify different cell height, when located */
                //Die Funktion für cell-height wird erst aufgerufen, wenn das Feed/DOM wirklich komplett geladen wurde

                /*
                var callback_cell_height = function(){

                    // Handler when the DOM is fully loaded
                    var currentWidth = $('.bogx--instagram-cell').width();
                    var currentHeight = $('.bogx--instagram-cell').height();

                    if (currentWidth !== currentHeight) {
                        var currentHeightStr = currentWidth + "px";
                        $('.bogx--instagram-cell').css('height', currentHeightStr);
                    }
                };

                if (
                    document.readyState === "complete" ||
                    (document.readyState !== "loading" && !document.documentElement.doScroll)
                ) {
                    callback_cell_height();
                } else {
                    document.addEventListener("DOMContentLoaded", callback_cell_height);
                }
                */

                /* end by bogx */
            };

            this.run = function() {

                /* 15.03.2020 by bogx, instagram feed neu scrapen oder aus der Session-Storage holen und anzeigen */
                //sessionStorage wird automatisch geleert, wenn Browserfenster geschlossen wird.
                //Das Neuladen des Browserfensters leert die sessionStorage nicht!
                var bogx_config_element = document.getElementById('bogx_instagram_data');
                var cache_suffix = bogx_config_element.getAttribute("data-cache_suffix");
                if (cache_suffix !== '') cache_suffix = "_" + cache_suffix;
                this.options.cache_suffix = cache_suffix;

                //separate Sessions für desktop und mobile, wenn angegeben
                var bogx_session_key = 'bogx_instagram_feed' + this.options.cache_suffix;
                if(!sessionStorage.getItem(bogx_session_key)) {
                    this.get(function(data, instance) {
                        if (instance.options.get_data)
                            instance.options.callback(data);
                        else
                            instance.display(data);
                            sessionStorage.setItem(bogx_session_key, JSON.stringify(data));
                            console.log("Instagram-Feed in Session-Storage speichern");
                    });
                } else {
                    var instagram_data = JSON.parse(sessionStorage.getItem(bogx_session_key));
                    this.display(instagram_data);
                    console.log("Instagram-Feed aus Session-Storage holen");
                }
                /* end by bogx */
            };

            if (this.valid) {
                this.run();
            }
        };
    }
));