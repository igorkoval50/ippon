{extends file='parent:frontend/index/index.tpl'}

{block name='frontend_index_after_body'}

    {$smarty.block.parent}

    {block name='frontend_index_after_body_newsletterbox'}
        {if $MagNewsletterBoxConfig.shownewsletterbox}
            <div class="newsletterbox--wrapper">
                <div class="newsletterbox--wrapper--inner{if !$MagNewsletterBoxConfig.mobile} no--mobile{/if}"
                     data-header="{$MagNewsletterBoxConfig.headline}"
                     data-displaytime="{$MagNewsletterBoxConfig.displaytime}"
                     data-autohide="{$MagNewsletterBoxConfig.autohide}"
                     data-cookielife="{$MagNewsletterBoxConfig.cookielife}"
                     data-hideafterregistration="{$MagNewsletterBoxConfig.hideafterregistration}"
                     data-maxwidth="{$MagNewsletterBoxConfig.maxwidth}"
                     data-errorsend="{s name='newsletterBoxMsgError'}Fehler bei der Übertragung!{/s}"
                     data-errorvalidate="{s name='newsletterBoxMsgErrorValidate'}Die E-Mail-Adresse ist bereits eingetragen{/s}"
                     data-sending="{s name='newsletterBoxMsgSending'}Bitte warten...{/s}"
                     data-controller="{url controller=MagNewsletterBox action=subscribeNewsletter}"
                     data-validatecontroller="{url controller=MagNewsletterBox action=validateMailAddress}">

                    <div class="newsletterbox--wrapper--inner--content">
                        {if $MagNewsletterBoxConfig.showimage && $MagNewsletterBoxConfig.image}
                            {block name='newsletterbox_content_image'}
                                <div class="newsletterbox--image">
                                    <img src="{$MagNewsletterBoxConfig.image}" alt="{$MagNewsletterBoxConfig.headline}" />
                                </div>
                            {/block}
                        {/if}

                        <form id="newsletterbox--form">

                            {block name='newsletterbox_content_text'}
                                <p>{$MagNewsletterBoxConfig.text}</p>
                            {/block}

                            {if $MagNewsletterBoxConfig.privacyid}
                                {block name='newsletterbox_content_privacy'}
                                    <div class="newsletterbox--privacy">
                                        <input name="privacy-checkbox" type="checkbox" id="privacy-checkbox" required="required" aria-required="true" value="1" class="is--required">
                                        <label for="privacy-text">{s name='sNewsletterBoxPrivacyInfo'}Die <a title="Datenschutzbestimmungen" href="{url controller=custom sCustom=$MagNewsletterBoxConfig.privacyid}">Datenschutzbestimmungen</a> habe ich zur Kenntnis genommen.{/s}</label>
                                        <div class="privacy--content" data-privacy-url="{url controller=custom sCustom=$MagNewsletterBoxConfig.privacyid}?isXHR=1">
                                            <div class="privacy--content--inner"></div>
                                        </div>
                                    </div>
                                {/block}
                            {/if}

                            <div class="alert is--error is--rounded">
                                <div class="alert--icon"> <i class="icon--element icon--warning"></i> </div>
                                <div class="alert--content">{s name='sNewsletterBoxErr'}Bitte geben Sie eine gültige eMail-Adresse ein.{/s}</div>
                            </div>

                            <div class="alert is--success is--rounded">
                                <div class="alert--icon"> <i class="icon--element icon--check"></i> </div>
                                <div class="alert--content"> </div>
                            </div>

                            <div class="success bold"></div>

                            <div class="newsletterbox--wrapper--inner--content--form">
                                <div class="fieldset">
                                    {block name='newsletterbox_form'}
                                        <input type="email" name="newsletteremail" id="newsletterbox_email" placeholder="{s name='IndexFooterNewsletterLabel'}Ihre eMail-Adresse{/s}" required="required" aria-required="true" value="" class="input--field is--required" />

                                        {* Captcha *}
                                        {block name="frontend_plugin_newsletter_form_captcha"}
                                            {$newsletterCaptchaName = {config name=newsletterCaptcha}}
                                            <div class="newsletter--captcha-form">
                                                <div class="captcha--placeholder"
                                                     data-src="{url module=widgets controller=Captcha action=getCaptchaByName captchaName=$newsletterCaptchaName}" {if isset($sErrorFlag) && count($sErrorFlag) > 0}data-hasError="true"{/if} data-autoload="true">
                                                </div>
                                            </div>
                                        {/block}

                                        <button type="submit" id="newsletterbox_submit" class="btn is--primary is--icon-left"> <i class="icon--mail"></i> <span class="button--text">{s name='IndexFooterNewsletterSubmit'}Newsletter abonnieren{/s}</span> </button>
                                    {/block}
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        {/if}
    {/block}
{/block}