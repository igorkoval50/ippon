{block name="widgets_promotionbanner_index"}
    {if $promotionbanner_count != 0}
        {foreach $promotionbanner as $pb}
            {if $pb['position'] == $position}
                {if ($pb['showoncontroller'] == 0) || (($pb['showoncontroller'] == 1) && $controllerName == "index") || (($pb['showoncontroller'] == 2) && $controllerName == "listing") || (($pb['showoncontroller'] == 3) && $controllerName == "detail") || (($pb['showoncontroller'] == 4) && $controllerName == "register" && $targetName == "account") || (($pb['showoncontroller'] == 5) && $controllerName == "checkout" && $actionName == "cart") || (($pb['showoncontroller'] == 6) && $controllerName == "register" && $targetName == "checkout") || (($pb['showoncontroller'] == 7) && $controllerName == "checkout" && $actionName == "shippingpayment") || (($pb['showoncontroller'] == 8) && $controllerName == "checkout" && $actionName == "confirm") || (($pb['showoncontroller'] == 9) && $controllerName == "checkout" && $actionName == "finish") || (($pb['showoncontroller'] == 10) && $controllerName == "checkout") || (($pb['showoncontroller'] == 11) && $controllerName == "blog" && $actionName == "index") || (($pb['showoncontroller'] == 12) && $controllerName == "blog" && $actionName == "detail") || (($pb['showoncontroller'] == 13) && $controllerName == "blog") || (($pb['showoncontroller'] == 14) && $controllerName == "campaign") || (($pb['showoncontroller'] == 15) && $controllerName == "note") || (($pb['showoncontroller'] == 16) && $controllerName == "custom") || (($pb['showoncontroller'] == 17) && $controllerName == "forms") || (($pb['showoncontroller'] == 18) && $controllerName == "search") || (($pb['showoncontroller'] == 19) && $controllerName == "newsletter") || (($pb['showoncontroller'] == 20) && $controllerName == "account")}
                    {if $position == 4}<div class="promotionbanner-modalbox-outer">{/if}
                    {block name="widgets_promotionbanner_index_individualpromotionbanner_inner"}    
                        <div class="promotionbanner-outer promotionbanner-outer-{$pb['id']}{if $pb['hideinsmartphoneportrait'] != 1} show-in-smartphoneportrait{/if}{if $pb['hideinsmartphonelandscape'] != 1} show-in-smartphonelandscape{/if}{if $pb['hideintabletportrait'] != 1} show-in-tabletportrait{/if}{if $pb['hideintabletlandscape'] != 1} show-in-tabletlandscape{/if}{if $pb['hideindesktop'] != 1} show-in-desktop{/if}{if $position == 0} promotionbanner-below-body{elseif $position == 1} promotionbanner-below-menue{elseif $position == 2} promotionbanner-bottom-screen{elseif $position == 3} promotionbanner-offcanvas{elseif $position == 4} promotionbanner-modalbox{elseif $position == 5} promotionbanner-uncertainly{/if}{if $pb['cssclass'] != ""} {$pb['cssclass']}{/if}" data-collapse="{if $pb['collapsible'] == 0}0{else}1{/if}" data-collapseicon="{if $pb['hidecollapseicon'] == 1}1{else}0{/if}" data-promotionbannerid="{$pb['id']}">
                            {block name="widgets_promotionbanner_index_individualpromotionbanner_collapseheader"}
                                {if $position != 4}
                                    {if $pb['collapsible'] == 1}
                                        {block name="widgets_promotionbanner_index_individualpromotionbanner_collapseheader_inner"}
                                            <div class="promotionbanner-collapse--header is--hidden" data-promotionbannerid="{$pb['id']}" data-collapsiblecookielifetime="{$pb['collapsiblecookielifetime']}" data-cookiepermissioncheck="{config name="promotionbanner_cookie_permissioncheck" namespace="digi1Individualpromotionbanner"}" data-namecookiecookienote="{config name="promotionbanner_namecookiecookienote" namespace="digi1Individualpromotionbanner"}" data-collapseicon="{if $pb['hidecollapseicon'] == 1}1{else}0{/if}">
                                                <span class="promotionbanner-collapse--toggler"></span>
                                            </div>
                                        {/block}
                                    {/if}
                                {/if}
                            {/block}
                            {block name="widgets_promotionbanner_index_individualpromotionbanner_promotionbannercontainer"}
                                <div class="promotionbanner-container is--hidden"{if $position == 4} data-width="{config name="promotionbanner_modalbox_width" namespace="digi1Individualpromotionbanner"}" data-timedelay="{if $pb['modalboxtimedelay'] != ""}{$pb['modalboxtimedelay']}{/if}" data-promotionbannerid="{$pb['id']}" data-collapsiblecookielifetime="{$pb['collapsiblecookielifetime']}" data-cookiepermissioncheck="{config name="promotionbanner_cookie_permissioncheck" namespace="digi1Individualpromotionbanner"}" data-namecookiecookienote="{config name="promotionbanner_namecookiecookienote" namespace="digi1Individualpromotionbanner"}" data-collapse="{if $pb['collapsible'] == 1}1{else}0{/if}"{/if}>
                                    <div class="container">
                                        {block name="widgets_promotionbanner_index_individualpromotionbanner_promotionbanner"}
                                            <div class="promotionbanner{if $position == 0} promotionbanner-below-body{elseif $position == 1} promotionbanner-below-menue{elseif $position == 2} promotionbanner-bottom-screen{elseif $position == 3} promotionbanner-offcanvas{elseif $position == 4} promotionbanner-modalbox{elseif $position == 5} promotionbanner-uncertainly{/if}">
                                                {block name="widgets_promotionbanner_index_individualpromotionbanner_promotionbannercontent"}
                                                    <div class="promotionbanner-content">
                                                        {block name="widgets_promotionbanner_index_individualpromotionbanner_promotionbannercontentinner"}
                                                            <div class="promotionbanner-content-inner">
                                                                {block name="widgets_promotionbanner_index_individualpromotionbanner_promotionbannersale"}
                                                                    {if $pb['percentage'] != ""}
                                                                        {block name="widgets_promotionbanner_index_individualpromotionbanner_promotionbannersale_inner"}
                                                                            <div class="promotionbanner-content-inner-sale{if $pb['percentagealignment'] == 0} align-left{elseif $pb['percentagealignment'] == 1} align-center{elseif $pb['percentagealignment'] == 2} align-right{/if} width-{if $pb['percentagewidth'] == 0}auto{elseif $pb['percentagewidth'] == 1}5{elseif $pb['percentagewidth'] == 2}10{elseif $pb['percentagewidth'] == 3}15{elseif $pb['percentagewidth'] == 4}20{elseif $pb['percentagewidth'] == 5}25{elseif $pb['percentagewidth'] == 6}30{elseif $pb['percentagewidth'] == 7}33{elseif $pb['percentagewidth'] == 8}35{elseif $pb['percentagewidth'] == 9}40{elseif $pb['percentagewidth'] == 10}45{elseif $pb['percentagewidth'] == 11}50{elseif $pb['percentagewidth'] == 12}55{elseif $pb['percentagewidth'] == 13}60{elseif $pb['percentagewidth'] == 14}65{elseif $pb['percentagewidth'] == 15}66{elseif $pb['percentagewidth'] == 16}70{elseif $pb['percentagewidth'] == 17}75{elseif $pb['percentagewidth'] == 18}80{elseif $pb['percentagewidth'] == 19}85{elseif $pb['percentagewidth'] == 20}90{elseif $pb['percentagewidth'] == 21}95{elseif $pb['percentagewidth'] == 22}100{/if}{if $pb['percentagecssclass'] != ""} {$pb['percentagecssclass']}{/if}">
                                                                                {$pb['percentage']}
                                                                            </div>
                                                                        {/block}
                                                                    {/if}
                                                                {/block}
                                                                {block name="widgets_promotionbanner_index_individualpromotionbanner_promotionbannerinformation"}
                                                                    <div class="promotionbanner-content-inner-information{if $pb['contentcssclass'] != ""} {$pb['contentcssclass']}{/if}">
                                                                        {block name="widgets_promotionbanner_index_individualpromotionbanner_promotionbannerinformation_headline"}
                                                                            {if $pb['headline'] != ""}
                                                                                {block name="widgets_promotionbanner_index_individualpromotionbanner_promotionbannerinformation_headline_inner"}
                                                                                    <div class="promotionbanner-content-inner-information-headline{if $pb['headlinealignment'] == 0} align-left{elseif $pb['headlinealignment'] == 1} align-center{elseif $pb['headlinealignment'] == 2} align-right{/if} width-{if $pb['headlinewidth'] == 0}auto{elseif $pb['headlinewidth'] == 1}5{elseif $pb['headlinewidth'] == 2}10{elseif $pb['headlinewidth'] == 3}15{elseif $pb['headlinewidth'] == 4}20{elseif $pb['headlinewidth'] == 5}25{elseif $pb['headlinewidth'] == 6}30{elseif $pb['headlinewidth'] == 7}33{elseif $pb['headlinewidth'] == 8}35{elseif $pb['headlinewidth'] == 9}40{elseif $pb['headlinewidth'] == 10}45{elseif $pb['headlinewidth'] == 11}50{elseif $pb['headlinewidth'] == 12}55{elseif $pb['headlinewidth'] == 13}60{elseif $pb['headlinewidth'] == 14}65{elseif $pb['headlinewidth'] == 15}66{elseif $pb['headlinewidth'] == 16}70{elseif $pb['headlinewidth'] == 17}75{elseif $pb['headlinewidth'] == 18}80{elseif $pb['headlinewidth'] == 19}85{elseif $pb['headlinewidth'] == 20}90{elseif $pb['headlinewidth'] == 21}95{elseif $pb['headlinewidth'] == 22}100{/if}">
                                                                                        {$pb['headline']}
                                                                                    </div>
                                                                                {/block}
                                                                            {/if}
                                                                        {/block}
                                                                        {block name="widgets_promotionbanner_index_individualpromotionbanner_promotionbannerinformation_txt"}
                                                                            {if $pb['txt'] != ""}
                                                                                {block name="widgets_promotionbanner_index_individualpromotionbanner_promotionbannerinformation_txt_inner"}
                                                                                    <div class="promotionbanner-content-inner-information-text{if $pb['txtalignment'] == 0} align-left{elseif $pb['txtalignment'] == 1} align-center{elseif $pb['txtalignment'] == 2} align-right{/if} width-{if $pb['txtwidth'] == 0}auto{elseif $pb['txtwidth'] == 1}5{elseif $pb['txtwidth'] == 2}10{elseif $pb['txtwidth'] == 3}15{elseif $pb['txtwidth'] == 4}20{elseif $pb['txtwidth'] == 5}25{elseif $pb['txtwidth'] == 6}30{elseif $pb['txtwidth'] == 7}33{elseif $pb['txtwidth'] == 8}35{elseif $pb['txtwidth'] == 9}40{elseif $pb['txtwidth'] == 10}45{elseif $pb['txtwidth'] == 11}50{elseif $pb['txtwidth'] == 12}55{elseif $pb['txtwidth'] == 13}60{elseif $pb['txtwidth'] == 14}65{elseif $pb['txtwidth'] == 15}66{elseif $pb['txtwidth'] == 16}70{elseif $pb['txtwidth'] == 17}75{elseif $pb['txtwidth'] == 18}80{elseif $pb['txtwidth'] == 19}85{elseif $pb['txtwidth'] == 20}90{elseif $pb['txtwidth'] == 21}95{elseif $pb['txtwidth'] == 22}100{/if}">
                                                                                        {$pb['txt']}
                                                                                    </div>
                                                                                {/block}
                                                                            {/if}
                                                                        {/block}
                                                                        {block name="widgets_promotionbanner_index_individualpromotionbanner_promotionbannerinformation_innerlink"}
                                                                            {if $pb['completelinking'] == 1}{else}
                                                                                {if $pb['linkbelowcontent'] == 1}
                                                                                    {if $pb['link'] != ""}
                                                                                        {block name="widgets_promotionbanner_index_individualpromotionbanner_promotionbannerinformation_innerlink_inner"}
                                                                                            <div class="promotionbanner-content-inner-information-btn{if $pb['linkalignment'] == 0} align-left{elseif $pb['linkalignment'] == 1} align-center{elseif $pb['linkalignment'] == 2} align-right{/if} width-{if $pb['linkwidth'] == 0}auto{elseif $pb['linkwidth'] == 1}5{elseif $pb['linkwidth'] == 2}10{elseif $pb['linkwidth'] == 3}15{elseif $pb['linkwidth'] == 4}20{elseif $pb['linkwidth'] == 5}25{elseif $pb['linkwidth'] == 6}30{elseif $pb['linkwidth'] == 7}33{elseif $pb['linkwidth'] == 8}35{elseif $pb['linkwidth'] == 9}40{elseif $pb['linkwidth'] == 10}45{elseif $pb['linkwidth'] == 11}50{elseif $pb['linkwidth'] == 12}55{elseif $pb['linkwidth'] == 13}60{elseif $pb['linkwidth'] == 14}65{elseif $pb['linkwidth'] == 15}66{elseif $pb['linkwidth'] == 16}70{elseif $pb['linkwidth'] == 17}75{elseif $pb['linkwidth'] == 18}80{elseif $pb['linkwidth'] == 19}85{elseif $pb['linkwidth'] == 20}90{elseif $pb['linkwidth'] == 21}95{elseif $pb['linkwidth'] == 22}100{/if}">
                                                                                                <a href="{if $pb['target'] == 1}javascript:void(0);{else}{$pb['link']}{/if}" class="btn{if $pb['linkcssclass'] != ""} {$pb['linkcssclass']}{/if}" title="{if $pb['linktext'] != ""}{$pb['linktext']}{/if}" aria-label="{if $pb['linktext'] != ""}{$pb['linktext']}{/if}">
                                                                                                    {if $pb['linktext'] != ""}
                                                                                                        {$pb['linktext']}
                                                                                                    {/if}
                                                                                                </a>
                                                                                            </div>
                                                                                        {/block}
                                                                                    {/if}
                                                                                {/if}
                                                                            {/if}
                                                                        {/block}
                                                                    </div>
                                                                {/block}   
                                                                {block name="widgets_promotionbanner_index_individualpromotionbanner_promotionbannerinformation_outerlink"}
                                                                    {if $pb['completelinking'] == 1}{else}
                                                                        {if $pb['linkbelowcontent'] == 1}{else}
                                                                            {if $pb['link'] != ""}
                                                                                {block name="widgets_promotionbanner_index_individualpromotionbanner_promotionbannerinformation_outerlink_inner"}
                                                                                    <div class="promotionbanner-content-inner-btn-outer{if $pb['linkalignment'] == 0} align-left{elseif $pb['linkalignment'] == 1} align-center{elseif $pb['linkalignment'] == 2} align-right{/if} width-{if $pb['linkwidth'] == 0}auto{elseif $pb['linkwidth'] == 1}5{elseif $pb['linkwidth'] == 2}10{elseif $pb['linkwidth'] == 3}15{elseif $pb['linkwidth'] == 4}20{elseif $pb['linkwidth'] == 5}25{elseif $pb['linkwidth'] == 6}30{elseif $pb['linkwidth'] == 7}33{elseif $pb['linkwidth'] == 8}35{elseif $pb['linkwidth'] == 9}40{elseif $pb['linkwidth'] == 10}45{elseif $pb['linkwidth'] == 11}50{elseif $pb['linkwidth'] == 12}55{elseif $pb['linkwidth'] == 13}60{elseif $pb['linkwidth'] == 14}65{elseif $pb['linkwidth'] == 15}66{elseif $pb['linkwidth'] == 16}70{elseif $pb['linkwidth'] == 17}75{elseif $pb['linkwidth'] == 18}80{elseif $pb['linkwidth'] == 19}85{elseif $pb['linkwidth'] == 20}90{elseif $pb['linkwidth'] == 21}95{elseif $pb['linkwidth'] == 22}100{/if}">
                                                                                        <a href="{if $pb['target'] == 1}javascript:void(0);{else}{$pb['link']}{/if}" class="btn{if $pb['linkcssclass'] != ""} {$pb['linkcssclass']}{/if}" title="{if $pb['linktext'] != ""}{$pb['linktext']}{/if}" aria-label="{if $pb['linktext'] != ""}{$pb['linktext']}{/if}">
                                                                                            {if $pb['linktext'] != ""}
                                                                                                {$pb['linktext']}
                                                                                            {/if}
                                                                                        </a>
                                                                                    </div>
                                                                                {/block} 
                                                                            {/if}
                                                                        {/if}
                                                                    {/if}
                                                                {/block} 
                                                            </div>
                                                        {/block}
                                                    </div>
                                                {/block}
                                                {block name="widgets_promotionbanner_index_individualpromotionbanner_promotionbanner_completelinking"}
                                                    {if $pb['completelinking'] == 1}
                                                        {block name="widgets_promotionbanner_index_individualpromotionbanner_promotionbanner_completelinking_inner"}
                                                            <a href="{if $pb['target'] == 1}javascript:void(0);{else}{$pb['link']}{/if}" class="promotionbanner-content-complete-linking" title="{if $pb['linktext'] != ""}{$pb['linktext']}{/if}" aria-label="{if $pb['linktext'] != ""}{$pb['linktext']}{/if}">
                                                                {if $pb['linktext'] != ""}
                                                                    {$pb['linktext']}
                                                                {/if}
                                                            </a>
                                                        {/block}
                                                    {/if}
                                                {/block}
                                            </div>
                                        {/block}
                                    </div>
                                </div>
                            {/block}
                        </div>
                    {/block}
                    {if $position == 4}</div>{/if}        
                    {block name="widgets_promotionbanner_index_individualpromotionbanner_style"} 
                        <style type="text/css">
                            .promotionbanner-outer-{$pb['id']}::after {
                            {if $pb['backgroundimage'] != ""}
                                background-image: url({$pb['backgroundimage']}); background-repeat: no-repeat; background-position: {if $pb['backgroundposition'] == 0}left top{elseif $pb['backgroundposition'] == 1}center top{elseif $pb['backgroundposition'] == 2}right top{elseif $pb['backgroundposition'] == 3}left center{elseif $pb['backgroundposition'] == 4}center center{elseif $pb['backgroundposition'] == 5}right center{elseif $pb['backgroundposition'] == 6}left bottom{elseif $pb['backgroundposition'] == 7}center bottom{elseif $pb['backgroundposition'] == 8}right bottom{/if};
                                {if $pb['backgroundsize'] == 0}background-size: cover;{elseif $pb['backgroundsize'] == 1}background-size: contain;{elseif $pb['backgroundsize'] == 2}background-size: auto;{/if}
                            {/if}
                            {if $pb['backgroundcolor'] != ""}
                                background-color: {$pb['backgroundcolor']};
                            {/if}
                                opacity: {if $pb['backgroundopacity'] == 0}1.0{elseif $pb['backgroundopacity'] == 1}0.95{elseif $pb['backgroundopacity'] == 2}0.9{elseif $pb['backgroundopacity'] == 3}0.85{elseif $pb['backgroundopacity'] == 4}0.8{elseif $pb['backgroundopacity'] == 5}0.75{elseif $pb['backgroundopacity'] == 6}0.7{elseif $pb['backgroundopacity'] == 7}0.65{elseif $pb['backgroundopacity'] == 8}0.6{elseif $pb['backgroundopacity'] == 9}0.55{elseif $pb['backgroundopacity'] == 10}0.5{elseif $pb['backgroundopacity'] == 11}0.45{elseif $pb['backgroundopacity'] == 12}0.4{elseif $pb['backgroundopacity'] == 13}0.35{elseif $pb['backgroundopacity'] == 14}0.3{elseif $pb['backgroundopacity'] == 15}0.25{elseif $pb['backgroundopacity'] == 16}0.2{elseif $pb['backgroundopacity'] == 17}0.15{elseif $pb['backgroundopacity'] == 18}0.1{elseif $pb['backgroundopacity'] == 19}0.05{/if}
                            }

                            {if $pb['collapseiconbackgroundcolor'] != ""}
                                .promotionbanner-outer-{$pb['id']} .promotionbanner-collapse--header {
                                    background: {$pb['collapseiconbackgroundcolor']};
                                }
                            {/if}

                            {if $pb['collapseiconfontcolor'] != ""}
                                .promotionbanner-outer-{$pb['id']} .promotionbanner-collapse--header .promotionbanner-collapse--toggler {
                                    color: {$pb['collapseiconfontcolor']};
                                }
                            {/if}

                            {if $pb['percentagebackgroundcolor'] != "" || $pb['percentagefontcolor'] != ""}
                            .promotionbanner-outer-{$pb['id']} .promotionbanner .promotionbanner-content-inner-sale {
                            {if $pb['percentagebackgroundcolor'] != ""}
                                background-color: {$pb['percentagebackgroundcolor']};
                            {/if}
                            {if $pb['percentagefontcolor'] != ""}
                                color: {$pb['percentagefontcolor']};
                            {/if}
                            }
                            {/if}

                            {if $pb['percentagepadding'] != ""}
                            .promotionbanner-outer-{$pb['id']}.promotionbanner-outer .container .promotionbanner .promotionbanner-content .promotionbanner-content-inner .promotionbanner-content-inner-sale {
                                padding: {$pb['percentagepadding']};
                            }
                            {/if}

                            {if $pb['contentbackgroundcolor'] != ""}
                            .promotionbanner-outer-{$pb['id']} .promotionbanner .promotionbanner-content-inner-information {
                            {if $pb['contentbackgroundcolor'] != ""}
                                background-color: {$pb['contentbackgroundcolor']};
                            {/if}
                            }
                            {/if}

                            {if $pb['contentpadding'] != ""}
                            .promotionbanner-outer-{$pb['id']}.promotionbanner-outer .container .promotionbanner .promotionbanner-content .promotionbanner-content-inner .promotionbanner-content-inner-information {
                                padding: {$pb['contentpadding']};
                            }
                            {/if}    

                            {if $pb['headlinefontcolor'] != ""}
                            .promotionbanner-outer-{$pb['id']} .promotionbanner .promotionbanner-content-inner-information .promotionbanner-content-inner-information-headline {
                            {if $pb['headlinefontcolor'] != ""}
                                color: {$pb['headlinefontcolor']};
                            {/if}
                            }
                            {/if}

                            {if $pb['txtfontcolor'] != ""}
                            .promotionbanner-outer-{$pb['id']} .promotionbanner .promotionbanner-content-inner-information .promotionbanner-content-inner-information-text {
                                color: {$pb['txtfontcolor']};
                            }
                            {/if}

                            {if $pb['linkbackgroundcolor'] != ""}
                            .promotionbanner-outer-{$pb['id']} .promotionbanner .promotionbanner-content-inner-btn-outer {
                                background-color: {$pb['linkbackgroundcolor']};
                            }
                            {/if}

                            {if $pb['linktransparent'] == 1}
                            .promotionbanner-outer-{$pb['id']} .promotionbanner .promotionbanner-content-inner-information-btn .btn, 
                            .promotionbanner-outer-{$pb['id']} .promotionbanner .promotionbanner-content-inner-btn-outer .btn {
                                background-image: none;
                                background: none;
                            }
                            {/if}

                            {if $pb['linkbgcolor'] != "" || $pb['linkfontcolor'] != "" || $pb['linkbordercolor'] != ""}
                            .promotionbanner-outer-{$pb['id']} .promotionbanner .promotionbanner-content-inner-information-btn .btn, 
                            .promotionbanner-outer-{$pb['id']} .promotionbanner .promotionbanner-content-inner-btn-outer .btn {
                                {if $pb['linkbgcolor'] != ""}
                                    background-image: none;
                                    background-color: {$pb['linkbgcolor']};
                                {/if}
                                {if $pb['linkfontcolor'] != ""}
                                    color: {$pb['linkfontcolor']};
                                {/if}
                                {if $pb['linkbordercolor'] != ""}
                                    border: 1px solid {$pb['linkbordercolor']};
                                {/if}
                            }
                            {/if}

                            {if $pb['linkpadding'] != ""}
                            .promotionbanner-outer-{$pb['id']}.promotionbanner-outer .container .promotionbanner .promotionbanner-content .promotionbanner-content-inner .promotionbanner-content-inner-information-btn {
                                padding: {$pb['linkpadding']};
                            }    

                            .promotionbanner-outer-{$pb['id']}.promotionbanner-outer .container .promotionbanner .promotionbanner-content .promotionbanner-content-inner .promotionbanner-content-inner-btn-outer {
                                padding: {$pb['linkpadding']};
                            }
                            {/if}
                        </style>
                    {/block}
                {/if}
            {/if}
        {/foreach}
    {/if}
{/block}