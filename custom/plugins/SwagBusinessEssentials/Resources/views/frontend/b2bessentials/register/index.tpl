{namespace name="frontend/account/login"}

<form method="post" action="{$registerRedirectUrl}" class="panel register--form">
    {block name='frontend_register_index_form_captcha_fieldset'}
        {include file="frontend/register/error_message.tpl" error_messages=$errors.captcha}
    {/block}

    {block name='frontend_register_index_form_personal_fieldset'}
        {include file="frontend/register/error_message.tpl" error_messages=$errors.personal}
        {include file="frontend/register/personal_fieldset.tpl" form_data=$register.personal error_flags=$errors.personal}
    {/block}

    {block name='frontend_register_index_form_billing_fieldset'}
        {include file="frontend/register/error_message.tpl" error_messages=$errors.billing}
        {include file="frontend/register/billing_fieldset.tpl" form_data=$register.billing error_flags=$errors.billing country_list=$countryList}
    {/block}

    {block name='frontend_register_index_form_shipping_fieldset'}
        {include file="frontend/register/error_message.tpl" error_messages=$errors.shipping}
        {include file="frontend/register/shipping_fieldset.tpl" form_data=$register.shipping error_flags=$errors.shipping country_list=$countryList}
    {/block}

    {block name='frontend_register_index_form_required'}
        {* Required fields hint *}
        <div class="register--required-info required_fields">
            {s name='RegisterPersonalRequiredText' namespace='frontend/register/personal_fieldset'}{/s}
        </div>
    {/block}

    {* Captcha *}
    {block name='frontend_register_index_form_captcha'}
        {$captchaHasError = $errors.captcha}
        {$captchaName = {config name=registerCaptcha}}
        {include file="widgets/captcha/custom_captcha.tpl" captchaName=$captchaName captchaHasError=$captchaHasError}
    {/block}

    {if !$update}
        {include file="frontend/b2bessentials/register/privacy.tpl"}
    {/if}

    {block name='frontend_register_index_form_submit'}
        {* Submit button *}
        <div class="register--action">
            <button type="submit" class="register--submit btn is--primary is--large is--icon-right" name="Submit">{s namespace="frontend/register/index" name="RegisterIndexNewActionSubmit"}{/s} <i class="icon--arrow-right"></i></button>
        </div>
    {/block}
</form>