//{namespace name=backend/prems_emotion_cms/article/controller/main}
//{block name="backend/prems_emotion_cms/controller/main"}
Ext.define('Shopware.apps.Article.PremsEmotionCms.controller.Batch', {
    override: 'Shopware.apps.Article.controller.Main',
    snippets: {
        processWindow: 'Stapelverarbeitung',
        finished: 'Stapelverarbeitung erfolgreich abgeschlossen',
        process: 'Verarbeite...',
    },

    /**
     * This method creates listener for events fired from the process
     */
    init: function() {
        var me = this;

        me.control({
            // Start button
            'prems-emotion-cms-view-batch-mask': {
                process: me.onProcess,
            },
            'prems-emotion-cms-view-batch-process': {
                startProcess: me.onStartProcess,
                cancelProcess: me.onCancelProcess,
            },
            /**'swag-import-export-manager-session': {
                resumeExport: me.onResume
            }*/
        });

        me.callParent(arguments);
    },

    onProcess: function(btn) {
        var me = this,
            form = btn.up('form'),
            values = form.getValues();
        me.parameters = values;

        me.onCreateProcessWindow();
    },

    /**
     * Triggers if the resume button was pressed
     * in the previous operation window.
     *
     */
    onResume: function() {
        var me = this;

        me.getConfig();
    },

    /**
     * Creates batch configuration
     */
    onCreateProcessWindow: function() {
        var me = this;

        me.getBatchConfig = me.getConfig();
    },

    /**
     * Triggers if the start button was pressed
     * in the process window.
     *
     * @param object Enlight.app.SubWindow win
     * @param object Ext.button.Button btn
     */
    onStartProcess: function(win, btn) {
        var me = this;

        me.cancelOperation = false;

        me.runRequest(win);

        btn.hide();
        win.cancelButton.show();
        win.closeButton.disable();
    },

    /**
     * Returns the parameters that will be sent to the backend
     */
    getParams: function() {
        var me = this;

        return {
            emotionId: me.parameters.emotionId,
            shopId: me.parameters.shopId,
            position: me.parameters.position,
            type: me.parameters.type,
            categories: Ext.encode(me.parameters.categories),
        };
    },

    /**
     * Returns the needed configuration for the next batch call
     */
    getConfig: function() {
        var me = this;

        me.batchConfig = {
            requestUrl: '{url controller="PremsEmotionCmsArticle" action="process"}',
            params: me.getParams(),
        };

        Ext.Ajax.request({
            url: '{url controller="PremsEmotionCmsArticle" action="prepareProcess"}',
            method: 'POST',
            params: me.batchConfig.params,
            success: function(response) {
                var result = Ext.decode(response.responseText);
                me.batchConfig.position = result.position;
                me.batchConfig.totalCount = result.count;
                me.batchConfig.snippet = me.snippets.process + me.batchConfig.position + ' / ' + me.batchConfig.totalCount;
                me.batchConfig.progress = me.batchConfig.position / me.batchConfig.totalCount;

                me.window = me.getView('Shopware.apps.Article.PremsEmotionCms.view.batch.Process').create({
                    batchConfig: me.batchConfig,
                }).show();
            },
            failure: function(response) {
                Shopware.Msg.createStickyGrowlMessage({
                    title: 'Es ist ein Fehler aufgetreten',
                    text: response.responseText,
                });
            },
        });
    },

    /**
     * This function sends a request to process data
     *
     * @param object Enlight.app.SubWindow win
     */
    runRequest: function(win) {
        var me = this,
            config = me.batchConfig,
            params = config.params;

        // if cancel button was pressed
        if (me.cancelOperation) {
            win.closeButton.enable();
            return;
        }

        Ext.Ajax.request({
            url: config.requestUrl,
            method: 'POST',
            params: params,
            timeout: 4000000,
            success: function(response) {
                var result = Ext.decode(response.responseText);

                if (result.success === false) {
                    Shopware.Msg.createStickyGrowlMessage({
                        title: 'Stapelverarbeitung Fehler',
                        text: result.msg,
                    });

                    win.closeButton.enable();
                    win.cancelButton.disable();
                    return;
                }

                me.batchConfig.params = result.data;
                me.batchConfig.position = result.data.position;

                win.progress.updateProgress(
                    me.batchConfig.position / me.batchConfig.totalCount,
                    me.snippets.process + me.batchConfig.position + ' / ' + me.batchConfig.totalCount,
                    true
                );

                if (me.batchConfig.position === me.batchConfig.totalCount) {
                    me.onProcessFinish(win);
                } else {
                    me.runRequest(win);
                }
            },
            failure: function(response) {
                Shopware.Msg.createStickyGrowlMessage({
                    title: 'Es ist ein Fehler aufgetreten',
                    text: response.responseText,
                });

                win.closeButton.enable();
                win.cancelButton.disable();
            },
        });
    },

    /**
     * Sets cancelOperation to true which will be checked in the
     * next batch call and will stop.
     *
     * @param btn
     */
    onCancelProcess: function(btn) {
        var me = this;

        btn.disable();

        me.cancelOperation = true;
    },

    /**
     * Will be called when process finish
     *
     * @param object Enlight.app.SubWindow win
     */
    onProcessFinish: function(win) {
        var me = this;

        win.closeButton.enable();
        win.cancelButton.hide();
        win.progress.updateText(me.snippets.finished + me.batchConfig.position + ' / ' + me.batchConfig.totalCount);

    },
});
// {/block}