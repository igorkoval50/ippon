{foreach $TlsNewsletterGroupList as $row}
    <div>
        <input type="checkbox"
               id="newsletter_group_{$row.id}"
               name="tls_newsletter_groups[]"
               {if $showChecked && ($row.active|| in_array($row.id, $_POST.tls_newsletter_groups))}checked="checked"{/if}
               value="{$row.id}">
        <label for="newsletter_group_{$row.id}">{$row.name}</label>
    </div>
{/foreach}
