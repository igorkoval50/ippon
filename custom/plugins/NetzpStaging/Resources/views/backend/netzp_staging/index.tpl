{extends file="parent:backend/_base/nplayout.tpl"}

{block name="content-main"}
    {if ! {acl_is_allowed resource=netzpStaging privilege=create}}
        <b>Sie haben leider keinen Zugriff auf diese Funktion.</b>

    {else if $checksok['all']}
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link {if $tab == 'profiles'}active{/if}" data-toggle="tab" href="#profiles">
                    Testumgebungen
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {if $tab == 'backups'}active{/if}" data-toggle="tab" href="#backups">
                    Backups <span class="badge badge-pill badge-primary">{$backups|@count}</span>
                </a>
            </li>
            <li class="nav-item ml-auto">
                <a class="nav-link {if $tab == 'support'}active{/if}" data-toggle="tab" href="#support">
                    Support
                </a>
            </li>
        </ul>

        <div class="tab-content">
            {if $msg|@count > 0}
                <div class="card bg">
                    <div class="card-body">
                        {include file="backend/netzp_staging/_messages.tpl"}
                    </div>
                </div>
            {/if}

            <div class="tab-pane {if $tab == 'profiles'}active{/if}" id="profiles">
                <div class="card bg-light">
                    <div class="card-body">
                        {if $cmd == 'newprofile' || $cmd == 'edit'}
                            {include file="backend/netzp_staging/profile.tpl"}
                        {elseif $cmd == 'settings'}
                            {include file="backend/netzp_staging/settings.tpl"}
                        {elseif $cmd == 'accessdata'}
                            {include file="backend/netzp_staging/accessdata.tpl"}
                        {elseif $cmd == 'diff'}
                            {include file="backend/netzp_staging/diff.tpl"}
                        {elseif $cmd == 'difffile'}
                            {include file="backend/netzp_staging/difffile.tpl"}
                        {else}
                            {include file="backend/netzp_staging/profiles.tpl"}
                        {/if}
                    </div>
                </div>
            </div>
            <div class="tab-pane {if $tab == 'backups'}active{/if}" id="backups">
                <div class="card bg-light">
                    <div class="card-body">
                        {include file="backend/netzp_staging/backups.tpl"}
                    </div>
                </div>
            </div>
            <div class="tab-pane {if $tab == 'support'}active{/if}" id="support">
                <div class="card bg-light">
                    <div class="card-body">
                        {include file="backend/netzp_staging/support.tpl"}
                    </div>
                </div>
            </div>
        </div>

    {/if}
{/block}

{block name="content/javascript"}
<script type="text/javascript" src="{link file="backend/_resources/js/promise.min.js"}"></script>
<script>
    var _basePath = "{$basepath}";
    {include file="backend/netzp_staging/script.js"}
</script>
{/block}
