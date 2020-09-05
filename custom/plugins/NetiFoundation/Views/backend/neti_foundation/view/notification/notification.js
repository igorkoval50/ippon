//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
if ({$show} === 1) {
    Shopware.Notification.createStickyGrowlMessage({
        'title': '{$title}',
        'text': '{$message}',
        'log': false
    });
}
//{/block}
