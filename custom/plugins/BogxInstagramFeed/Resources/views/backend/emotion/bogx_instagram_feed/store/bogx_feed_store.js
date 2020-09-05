//{block name="backend/Emotion/app" append}
Ext.define('Shopware.apps.bogxFeed.store.FeedLayoutStore', {
	extend: 'Ext.data.Store',
	fields : [
	{
		name : 'id',
		type : 'integer'
	},
	{
		name : 'name',
		type : 'string'
	},
	{
		name : 'value',
		type : 'string'
	}],
	data : [
	{
		id : 1,
		name : "Bilder-Grid",
		value : "grid"
	},
	{
		id : 2,
		name : "Bilder-Grid verlinkt",
		value : "grid_linked"
	},
	{
		id : 3,
		name : "Bilder-Grid mit HOVER-Effekt und POPUP",
		value : "grid_hover"
	}]
});
// {/block}
