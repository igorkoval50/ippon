//{block name="backend/blog/model/prems_emotion_cms/blog"}
Ext.define('Shopware.apps.Blog.PremsEmotionCms.model.Blog', {

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
            read: '{url controller="PremsEmotionCmsBlog" action="getEmotionBlog"}',
            destroy: '{url controller="PremsEmotionCmsBlog" action="deleteEmotionBlog"}'
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