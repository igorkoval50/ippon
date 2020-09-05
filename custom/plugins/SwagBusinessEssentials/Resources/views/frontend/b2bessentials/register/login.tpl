{namespace name="frontend/account/login"}

<div class="register--existing-customer panel has--border is--rounded">
    <h2 class="panel--title is--underline">{s name="LoginHeaderExistingCustomer"}{/s}</h2>
    <div class="panel--body is--wide">
        {block name='frontend_register_login_form'}
            <form name="sLogin" method="post" action="{$loginUrl}">
                {if $sTarget}<input name="sTarget" type="hidden" value="{$sTarget|escape}" />{/if}

                {block name='frontend_register_login_description'}
                    <div class="register--login-description">{s name="LoginHeaderFields"}{/s}</div>
                {/block}

                {block name='frontend_register_login_input_email'}
                    <div class="register--login-email">
                        <input name="email" placeholder="{s name="LoginPlaceholderMail"}{/s}" type="email" tabindex="1" value="{$sFormData.email|escape}" id="email" class="register--login-field{if $sErrorFlag.email} has--error{/if}" />
                    </div>
                {/block}

                {block name='frontend_register_login_input_password'}
                    <div class="register--login-password">
                        <input name="password" placeholder="{s name="LoginPlaceholderPassword"}{/s}" type="password" tabindex="2" id="passwort" class="register--login-field{if $sErrorFlag.password} has--error{/if}" />
                    </div>
                {/block}

                {block name='frontend_register_login_input_lostpassword'}
                    <div class="register--login-lostpassword">
                        <a href="{url controller=account action=password}" title="{"{s name="LoginLinkLostPassword"}{/s}"|escape}">
                            {s name="LoginLinkLostPassword"}{/s}
                        </a>
                    </div>
                {/block}

                {block name='frontend_register_login_input_form_submit'}
                    <div class="register--login-action">
                        <button type="submit" class="register--login-btn btn is--primary is--large is--icon-right" name="Submit">{s name="LoginLinkLogon"}{/s} <i class="icon--arrow-right"></i></button>
                    </div>
                {/block}
            </form>
        {/block}
    </div>
</div>