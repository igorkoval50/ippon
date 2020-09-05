{block name="frontend_register_index_form_privacy"}
    {if {config name=ACTDPRTEXT} || {config name=ACTDPRCHECK}}
        {block name="frontend_register_index_form_privacy_title"}
            <h2 class="panel--title is--underline">
                {s name="PrivacyTitle" namespace="frontend/index/privacy"}{/s}
            </h2>
        {/block}
        <div class="panel--body is--wide">
            {block name="frontend_register_index_form_privacy_content"}
                <div class="register--password-description">
                    {if {config name=ACTDPRCHECK}}
                        {* Privacy checkbox *}
                        {block name="frontend_register_index_form_privacy_content_checkbox"}
                            <input name="register[personal][dpacheckbox]" type="checkbox" id="dpacheckbox"{if $form_data.dpacheckbox} checked="checked"{/if} required="required" aria-required="true" value="1" class="is--required" />
                            <label for="dpacheckbox">
                                {s name="PrivacyText" namespace="frontend/index/privacy"}{/s}
                            </label>
                        {/block}
                    {else}
                        {block name="frontend_register_index_form_privacy_content_text"}
                            {s name="PrivacyText" namespace="frontend/index/privacy"}{/s}
                        {/block}
                    {/if}
                </div>
            {/block}
        </div>
    {/if}
{/block}