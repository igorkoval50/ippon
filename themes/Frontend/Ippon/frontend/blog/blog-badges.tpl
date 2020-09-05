{namespace name="frontend/blog/box"}

{* Small product badges on the left *}
{block name="frontend_blog_box_blog_badges"}
    <div class="blog--badges">

        {assign var="date1" value=$smarty.now}
        {assign var="date2" value=$sArticle.displayDate->getTimestamp()}
        {assign var="dateDiff" value=$date1-$date2}
        {assign var="dateDiffDays" value=$dateDiff / 60 / 60 / 24}

        {* Discount badge *}
        {block name='frontend_listing_box_blog_date'}
            {if $dateDiffDays <= $theme.blogValidationTime}
                <div class="blog--badge badge--date">
                    {if $dateDiffDays|string_format:"%d" == 0}
                        {s name="BlogBoxDaysNew"}Neu{/s}
                    {elseif $dateDiffDays|string_format:"%d" == 1}
                        {s name="BlogBoxDayBefore"}vor{/s} {$dateDiffDays|string_format:"%d"} {s name="BlogBoxDayAfter"}Tag{/s}
                    {else}
                        {s name="BlogBoxDaysBefore"}vor{/s} {$dateDiffDays|string_format:"%d"} {s name="BlogBoxDaysAfter"}Tagen{/s}
                    {/if}
                </div>
            {/if}
        {/block}

    </div>
{/block}