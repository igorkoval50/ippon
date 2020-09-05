//{block name="backend/article/model/prems_emotion_cms/article"}
Ext.define('Shopware.apps.Article.PremsEmotionCms.model.Article', {

    /**
     * Extends the standard Ext Model
     * @string
     */
    extend: 'Ext.data.Model',

    /**
     * The fields used for this model
     * @array
     */
    fields: [
        { name : 'id', type : 'int' },
        { name : 'name', type : 'string' },
        { name : 'position', type : 'string' },
        { name : 'shopId', type : 'int' }
    ],

    /**
     * Configure the data communication
     * @object
     */
    proxy: {
        type:'ajax',

        /**
         * Configure the url mapping for the different
         * store operations based on
         * @object
         */
        api: {
            read: '{url controller="PremsEmotionCmsArticle" action="getEmotionArticle"}',
            destroy: '{url controller="PremsEmotionCmsArticle" action="deleteEmotionArticle"}'
        },

        /**
         * Configure the data reader
         * @object
         */
        reader: {
            type:'json',
            root:'data',
            totalProperty:'total'
        }
    }
});
//{/block}