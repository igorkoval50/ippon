{extends file="parent:frontend/index/index.tpl"}

{block name="frontend_index_body_classes"}{strip}
    {$smarty.block.parent}
    {if $minimalView} is--minimal-view{/if}
{/strip}{/block}

{block name="frontend_index_body_attributes"}{strip}
    {$smarty.block.parent}
    {if $theme.languageSwitcher}data-languageSwitcher="true"{/if}
{/strip}{/block}

{block name='frontend_index_after_body'}
    {$smarty.block.parent}
    {include file="frontend/index/fixed-btns.tpl"}
{/block}

{* Wrap Shop Header *}
{block name='frontend_index_navigation'}
    <div class="header-wrap">
        {$smarty.block.parent}

        {* ... add features slider *}
        <div class="header--features">
            {include file='frontend/index/features_slider.tpl'
                sliderArrowControls='true'
                sliderItemsPerPage='1'
                sliderItemsPerSlide='1'
                sliderAutoSlide='false'
                sliderArrowAction='slide'
                sliderItemMinWidth='1000'
            }
        </div>
    </div>
{/block}



{* Shop header *}
{block name='frontend_index_navigation'}
    <header class="header-main">
        {block name='frontend_index_header_navigation'}
            {* Logo container *}
            {block name='frontend_index_logo_container'}
                <div class="header--logo">
                    <div class="container">
                        <div class="header-logo--inner">
                            {include file="frontend/index/logo-container.tpl"}
                        </div>
                    </div>
                </div>
            {/block}

            {block name='frontend_index_top_bar_container'}
                <div class="header--navigation">
                    <div class="top--navigation">
                        <div class="container">
                            <div class="navigation-top--inner">
                                {* Shop navigation *}
                                {block name='frontend_index_shop_navigation'}
                                    {include file="frontend/index/shop-navigation.tpl"}
                                {/block}

                                {* top bar navigation *}

                                {include file="frontend/index/topbar-navigation.tpl"}
                            </div>
                        </div>
                    </div>
                </div>
            {/block}

            {* ... add Maincategories navigation top *}
            {block name='frontend_index_navigation_categories_top'}
                <div class="main--navigation">
                    <div class="container">
                        <nav class="navigation-main">
                            <div class="container" data-menu-scroller="true" data-listSelector=".navigation--list.container" data-viewPortSelector=".navigation--list-wrapper">
                                {block name="frontend_index_navigation_categories_top_include"}
                                    {include file='frontend/index/main-navigation.tpl'}
                                {/block}
                            </div>
                        </nav>
                    </div>
                </div>
            {/block}

            {block name='frontend_index_container_ajax_cart'}
                <div class="container--ajax-cart" data-collapse-cart="true"{if $theme.offcanvasCart} data-displayMode="offcanvas"{/if}></div>
            {/block}

        {/block}
    </header>
{/block}

{block name="frontend_index_content_main"}
    <div class="content-wrap">
        {$smarty.block.parent}
    </div>
{/block}

{* Last seen products *}
{block name='frontend_index_left_last_articles'}
    {if $sLastArticlesShow && !$isEmotionLandingPage}
        {* Last seen products *}
        <div class="last-seen-products is--hidden" data-last-seen-products="true">
            <div class="last-seen-products--title">
                {s namespace="frontend/plugins/index/viewlast" name='WidgetsRecentlyViewedHeadline'}{/s}
            </div>
            <div class="last-seen-products--slider product-slider" data-itemMinWidth="300" data-product-slider="true">
                <div class="last-seen-products--container product-slider--container"></div>
            </div>
        </div>
    {/if}
{/block}

{* Footer *}
{block name="frontend_index_footer"}
    <footer class="footer-main">
        <div class="footer--inner">

            <div class="footer--marketing block-group">
                <div class="footer--newsletter block">
                    <div class="block-group">
                        {block name="frontend_index_footer_newsletter_content"}
                            <div class="newsletter--content block">
                                <p class="newsletter--desc">
                                    {s namespace="frontend/index/menu_footer" name="sFooterNewsletter"}{/s}
                                </p>
                            </div>

                            {block name="frontend_index_footer_newsletter_form"}
                                <form class="newsletter--form block" action="{url controller='newsletter'}" method="post">
                                    <div class="newsletter--inner">
                                        <input type="hidden" value="1" name="subscribeToNewsletter" />

                                        {block name="frontend_index_footer_newsletter_form_field"}
                                            <input type="email" name="newsletter" class="newsletter--field" placeholder="{s namespace="frontend/index/menu_footer" name="IndexFooterNewsletterValue"}{/s}" />
                                        {/block}

                                        {block name="frontend_index_footer_newsletter_form_submit"}
                                            <button type="submit" class="newsletter--button btn">
                                                <i class="icon--mail"></i> <span class="button--text">{s name='IndexFooterNewsletterSubmit'}{/s}</span>
                                            </button>
                                        {/block}

                                        {if $TlsNewsletterGroupList}
                                            {include file="frontend/tls_newsletter_group/group-list.tpl"}
                                        {/if}

                                        {* Data protection information *}
                                        {block name="frontend_index_footer_column_newsletter_privacy"}
                                            {if {config name=ACTDPRTEXT} || {config name=ACTDPRCHECK}}
                                                {include file="frontend/_includes/privacy.tpl"}
                                            {/if}
                                        {/block}
                                    </div>
                                </form>
                            {/block}

                        {/block}
                    </div>
                </div>

                <div class="footer--social-media block">
                    <div class="block-group">
                        {block name="frontend_index_footer_social_media_content"}
                            <div class="social-media--content block">
                                <p class="social-media--desc">
                                    {s namespace="frontend/index/menu_footer" name="sFooterSocialmedia"}Besuche uns <br />auch auf:{/s}
                                </p>
                            </div>

                            {block name="frontend_index_footer_social_media_icons"}
                                {* Store Snippet Text into var *}
                                {$instagram = "{s namespace='frontend/index/social' name='IndexSocialMediaInstagramUrl'}{/s}"}
                                {$instagram2 = "{s namespace='frontend/index/social' name='IndexSocialMediaInstagram2Url'}{/s}"}
                                {$facebook1 ="{s namespace='frontend/index/social' name='IndexSocialMediaFacebookUrl1'}{/s}"}
                                {$facebook2 ="{s namespace='frontend/index/social' name='IndexSocialMediaFacebookUrl2'}{/s}"}
                                {$facebook3 ="{s namespace='frontend/index/social' name='IndexSocialMediaFacebookUrl3'}{/s}"}
                                {$youtube = "{s namespace='frontend/index/social' name='IndexSocialMediaYoutubeUrl'}{/s}"}
                                {$twitter = "{s namespace='frontend/index/social' name='IndexSocialMediaTwitterUrl'}{/s}"}
                                {$google = "{s namespace='frontend/index/social' name='IndexSocialMediaGoogleUrl'}{/s}"}
                                {$linkedin = "{s namespace='frontend/index/social' name='IndexSocialMediaLinkedinUrl'}{/s}"}
                                {$xing = "{s namespace='frontend/index/social' name='IndexSocialMediaXingUrl'}{/s}"}
                                {$google = "{s namespace='frontend/index/social' name='IndexSocialMediaGoogleUrl'}{/s}"}
                                {$rss = "{s namespace='frontend/index/social' name='IndexSocialMediaRssUrl'}{/s}"}
                                {$call = "{s namespace='frontend/index/social' name='IndexSocialMediaCallUrl'}{/s}"}
                                {$mail = "{s namespace='frontend/index/social' name='IndexSocialMediaMailUrl'}{/s}"}

                                <div class="social-media--icons block">
                                    {* Check if snippet is in use *}
                                    {if $instagram}
                                        <a class="entry--social" href="{s namespace='frontend/index/social' name='IndexSocialMediaInstagramUrl'}{/s}" target="_blank" title="{s namespace='frontend/index/social' name='IndexSocialMediaInstagramTitel'}@IPPONGEAR{/s}"><i class="theme-icon--instagram"></i> {s namespace='frontend/index/social' name='IndexSocialMediaInstagramTitel'}{/s}</a>
                                    {/if}

                                    {if $instagram2}
                                        <a class="entry--social" href="{s namespace='frontend/index/social' name='IndexSocialMediaInstagram2Url'}{/s}" target="_blank" title="{s namespace='frontend/index/social' name='IndexSocialMediaInstagram2Titel'}@fujimatseurope{/s}"><i class="theme-icon--instagram"></i> {s namespace='frontend/index/social' name='IndexSocialMediaInstagram2Titel'}{/s}</a>
                                    {/if}

                                    {if $facebook1}
                                        <a class="entry--social" href="{s namespace='frontend/index/social' name='IndexSocialMediaFacebookUrl1'}{/s}" target="_blank" title="{s namespace='frontend/index/social' name='IndexSocialMediaFacebookTitel1'}{$sShopname} Karate{/s}"><i class="icon--facebook3"></i> {s namespace='frontend/index/social' name='IndexSocialMediaFacebookTitel1'}{/s}</a>
                                    {/if}

                                    {if $facebook2}
                                        <a class="entry--social" href="{s namespace='frontend/index/social' name='IndexSocialMediaFacebookUrl2'}{/s}" target="_blank" title="{s namespace='frontend/index/social' name='IndexSocialMediaFacebookTitel2'}{$sShopname} Judo{/s}"><i class="icon--facebook3"></i> {s namespace='frontend/index/social' name='IndexSocialMediaFacebookTitel2'}{/s}</a>
                                    {/if}

                                    {if $facebook3}
                                        <a class="entry--social" href="{s namespace='frontend/index/social' name='IndexSocialMediaFacebookUrl3'}{/s}" target="_blank" title="{s namespace='frontend/index/social' name='IndexSocialMediaFacebookTitel3'}{$sShopname} BJJ{/s}"><i class="icon--facebook3"></i> {s namespace='frontend/index/social' name='IndexSocialMediaFacebookTitel3'}{/s}</a>
                                    {/if}

                                    {if $youtube}
                                        <a class="entry--social" href="{s namespace='frontend/index/social' name='IndexSocialMediaYoutubeUrl'}{/s}" target="_blank" title="{s namespace='frontend/index/social' name='IndexSocialMediaYoutubeTitel'}{$sShopname} bei Youtube{/s}"><i class="theme-icon--youtube"></i></a>
                                    {/if}

                                    {if $twitter}
                                        <a class="entry--social" href="{s namespace='frontend/index/social' name='IndexSocialMediaTwitterUrl'}{/s}" target="_blank" title="{s namespace='frontend/index/social' name='IndexSocialMediaTwitterTitel'}{$sShopname} bei Twitter{/s}"><i class="icon--twitter"></i></a>
                                    {/if}

                                    {if $google}
                                        <a class="entry--social" href="{s namespace='frontend/index/social' name='IndexSocialMediaGoogleUrl'}{/s}" target="_blank" title="{s namespace='frontend/index/social' name='IndexSocialMediaGoogleTitel'}{$sShopname} bei Google{/s}"><i class="icon--googleplus"></i></a>
                                    {/if}

                                    {if $linkedin}
                                        <a class="entry--social" href="{s namespace='frontend/index/social' name='IndexSocialMediaLinkedinUrl'}{/s}" target="_blank" title="{s namespace='frontend/index/social' name='IndexSocialMediaLinkedinTitel'}{$sShopname} bei Linkedin{/s}"><i class="icon--linkedin"></i></a>
                                    {/if}

                                    {if $xing}
                                        <a class="entry--social" href="{s namespace='frontend/index/social' name='IndexSocialMediaXingUrl'}{/s}" target="_blank" title="{s namespace='frontend/index/social' name='IndexSocialMediaXingTitel'}{$sShopname} bei Xing{/s}"><i class="theme-icon--xing"></i></a>
                                    {/if}

                                    {if $rss}
                                        <a class="entry--social" href="{s namespace='frontend/index/social' name='IndexSocialMediaRssUrl'}{/s}" target="_blank" title="{s namespace='frontend/index/social' name='IndexSocialMediaRssTitel'}{$sShopname} RSS{/s}"><i class="icon--rss"></i></a>
                                    {/if}

                                    {if $call}
                                        <a class="entry--social" href="{s namespace='frontend/index/social' name='IndexSocialMediaCallUrl'}{/s}" target="_blank" title="{s namespace='frontend/index/social' name='IndexSocialMediaCallTitel'}{$sShopname} anrufen{/s}"><i class="icon--chat"></i></a>
                                    {/if}

                                    {if $mail}
                                        <a class="entry--social" href="{s namespace='frontend/index/social' name='IndexSocialMediaMailUrl'}{/s}" target="_blank" title="{s namespace='frontend/index/social' name='IndexSocialMediaMailTitel'}{$sShopname} schreiben{/s}"><i class="icon--envelope2"></i></a>
                                    {/if}
                                </div>
                            {/block}

                        {/block}
                    </div>
                </div>
            </div>



            {block name="frontend_index_footer_container"}
                {include file='frontend/index/footer.tpl'}

                {block name="frontend_language_switcher_content"}
                    {block name="frontend_language_switcher_config"}
                        {$themeLanguages = [
                            'language1' => ['title' => $theme.language1Title, 'url' => $theme.language1Url, 'media' => $theme.language1Media],
                            'language2' => ['title' => $theme.language2Title, 'url' => $theme.language2Url, 'media' => $theme.language2Media],
                            'language3' => ['title' => $theme.language3Title, 'url' => $theme.language3Url, 'media' => $theme.language3Media],
                            'language4' => ['title' => $theme.language4Title, 'url' => $theme.language4Url, 'media' => $theme.language4Media],
                            'language5' => ['title' => $theme.language5Title, 'url' => $theme.language5Url, 'media' => $theme.language5Media],
                            'language6' => ['title' => $theme.language6Title, 'url' => $theme.language6Url, 'media' => $theme.language6Media],
                            'language7' => ['title' => $theme.language7Title, 'url' => $theme.language7Url, 'media' => $theme.language7Media],
                            'language8' => ['title' => $theme.language8Title, 'url' => $theme.language8Url, 'media' => $theme.language8Media],
                            'language9' => ['title' => $theme.language9Title, 'url' => $theme.language9Url, 'media' => $theme.language9Media],
                            'language10' => ['title' => $theme.language10Title, 'url' => $theme.language10Url, 'media' => $theme.language10Media]
                        ]}

                        <script type="text/javascript">
                            var languageSwitcherShopPath = languageSwitcherShopPath || '{url controller=index}';

                            var languageSwitcherShopLocale = languageSwitcherShopLocale || '{$Locale}';

                            var languageSwitcherModalSmallTitle = languageSwitcherModalSmallTitle || {ldelim}
                                title: '{s name="languageSwitcherModalSmallTitle" namespace="frontend/index/modal/languageswitcher"}Choose your Location{/s}'
                                {rdelim};

                            var languageSwitcherModalSmallText = languageSwitcherModalSmallText || {ldelim}
                                title: '{s name="languageSwitcherModalSmallText" namespace="frontend/index/modal/languageswitcher"}We just wanted to ask if you would prefer to see our products in your local Ippon-Shop. <br />Not your location? <a href="#" class="language-switcher--show-all">Show all local Ippon Shops</a>{/s}'
                                {rdelim};

                            var languageSwitcherModalTitle = languageSwitcherModalTitle || {ldelim}
                                title: '{s name="languageSwitcherModalTitle" namespace="frontend/index/modal/languageswitcher"}Welcome to Ippon-Shop. Choose your country{/s}'
                                {rdelim};


                            var languageSwitcherModalContent = languageSwitcherModalContent || {ldelim}
                                language1: {ldelim}
                                    title:  '{$theme.language1Title}',
                                    url:    '{$theme.language1Url}',
                                    code:   '{$theme.language1Code}',
                                    media:  '{$theme.language1Media}'
                                    {rdelim},
                                language2: {ldelim}
                                    title:  '{$theme.language2Title}',
                                    url:    '{$theme.language2Url}',
                                    code:   '{$theme.language2Code}',
                                    media:  '{$theme.language2Media}'
                                    {rdelim},
                                language3: {ldelim}
                                    title:  '{$theme.language3Title}',
                                    url:    '{$theme.language3Url}',
                                    code:   '{$theme.language3Code}',
                                    media:  '{$theme.language3Media}'
                                    {rdelim},
                                language4: {ldelim}
                                    title:  '{$theme.language4Title}',
                                    url:    '{$theme.language4Url}',
                                    code:   '{$theme.language4Code}',
                                    media:  '{$theme.language4Media}'
                                    {rdelim},
                                language5: {ldelim}
                                    title:  '{$theme.language5Title}',
                                    url:    '{$theme.language5Url}',
                                    code:   '{$theme.language5Code}',
                                    media:  '{$theme.language5Media}'
                                    {rdelim},
                                language6: {ldelim}
                                    title:  '{$theme.language6Title}',
                                    url:    '{$theme.language6Url}',
                                    code:   '{$theme.language6Code}',
                                    media:  '{$theme.language6Media}'
                                    {rdelim},
                                language7: {ldelim}
                                    title:  '{$theme.language7Title}',
                                    url:    '{$theme.language7Url}',
                                    code:   '{$theme.language7Code}',
                                    media:  '{$theme.language7Media}'
                                    {rdelim},
                                language8: {ldelim}
                                    title:  '{$theme.language8Title}',
                                    url:    '{$theme.language8Url}',
                                    code:   '{$theme.language8Code}',
                                    media:  '{$theme.language8Media}'
                                    {rdelim},
                                language9: {ldelim}
                                    title:  '{$theme.language9Title}',
                                    url:    '{$theme.language9Url}',
                                    code:   '{$theme.language9Code}',
                                    media:  '{$theme.language9Media}'
                                    {rdelim},
                                language10: {ldelim}
                                    title:  '{$theme.language10Title}',
                                    url:    '{$theme.language10Url}',
                                    code:   '{$theme.language10Code}',
                                    media:  '{$theme.language10Media}'
                                    {rdelim}
                                {rdelim};
                        </script>
                    {/block}
                {/block}
            {/block}
        </div>
    </footer>
{/block}

{block name="frontend_index_header_javascript_data"}
    {$smarty.block.parent}

    {if $sArticle}
        {if $sArticle.additionaltext}
            {$lastSeenProductsConfig.currentArticle.articleName = $sArticle.articleName}
        {/if}
    {/if}
{/block}

{block name="frontend_plugin_newsletter_form_captcha"}
    {$smarty.block.parent}
    {if $TlsNewsletterGroupList}
        <div class="tls-newsletter-group-list-popup">
            <span>{s name="NewsletterSelectGroupPlaceholder" namespace="frontend/tls_newsletter_group/group-list"}Wähle deine Sportart{/s}</span>
            <div class="list--checkbox" role="menu">
                {include file="frontend/tls_newsletter_group/checkboxes.tpl" showChecked=true}
            </div>
        </div>
    {/if}
{/block}


