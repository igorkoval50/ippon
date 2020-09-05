{block name="frontend_neti_language_detector_modalbox"}
    <div class="netiLanguageDetector">
        {block name="frontend_neti_language_detector_modalbox_inner"}

            {block name="frontend_neti_language_detector_modalbox_wrap"}
                <div class="cWrap">
                    {block name="frontend_neti_language_detector_modalbox_wrap_inner"}
                        {$netiLanguageDetector.content}
                    {/block}
                </div>
            {/block}

            {block name="frontend_neti_language_detector_modalbox_actionwrap"}
                <div class="actionWrap">

                    {block name="frontend_neti_language_detector_modalbox_actionwrap_form"}
                        <form method="post" action="{$netiLanguageDetector.requestUri}" class="language--form neti--redirect--form new_customer_form">
                            <input type="hidden" name="neti-redirecting" value="1">
                            <input type="hidden" name="__shop" value="{$netiLanguageDetector.shopId}">
                            <input type="hidden" name="__redirect" value="1">
                            <a class="btn is--secondary is--small is--icon-right" href="#" data-redirect="false">
                                <i class="icon--cross"></i>
                                <span>{$netiLanguageDetector.stay}</span>
                            </a>
                            <a class="btn is--primary is--small is--icon-right" href="#" data-redirect="true">
                                <i class="icon--check"></i>
                                <span>{$netiLanguageDetector.follow}</span>
                            </a>
                        </form>
                    {/block}

                </div>
            {/block}

        {/block}
    </div>
{/block}
