/* global Handlebars */

;(function($, window, document, Handlebars, undefined) {
    'use strict';

    $.plugin('swagCustomProductsUpload', {

        /** @object Default plugin settings */
        defaults: {

            /** @string indicates that we can use advanced techniques e.g. FormData, FileReader, etc. */
            advancedCls: 'is--advanced-upload',

            /** @string class which will be applied when the user drags something over the upload field */
            dragCls: 'is--dragover',

            /** @string mode selection - file (default) and image */
            mode: 'file',

            /** @string icon which will be used for the file upload list */
            fileIconCls: 'icon--paperclip',

            /** @string Preview container which will be display the files */
            previewContainerSelector: '.custom-products--upload-list',

            /** @string Delete button selector to remove a file from the upload list */
            deleteFileBtnSelector: '.upload--btn-delete',

            /** @string Actual upload button which should fire the request */
            uploadBtnSelector: '.upload-btn--send-request',

            /** @string Upload url */
            uploadURL: '',

            /** @integer Custom products template id */
            templateId: -1,

            /** @integer Custom products option id */
            optionId: -1,

            /** @string Class which will applied to the dropzone and button to indicate that the file / image is uploading */
            uploadingCls: 'is--uploading',

            /** @string Class which hides an element */
            hiddenCls: 'is--hidden',

            /** @integer Max number of upload-able files */
            maxFiles: -1,

            /** @integer Max file size in bytes */
            maxFileSize: -1,

            /** @array Allowed mime types */
            allowedMimeTypes: [],

            /** @string Class which indicates an error occurred */
            failureCls: 'is--failure',

            /** @string Selector for a failure panel which will be displayed in the upload widget */
            failureMessageSelector: '.custom-products--uploading-failure',

            /** @string Selector for a failure message which will be displayed if the user tries to upload too many files */
            failureMessageMaxFilesSelector: '.too-much-files',

            /** @string Selector for a failure message which will be displayed if the user uploads a file which is too big */
            failureMessageMaxFileSizeSelector: '.max-file-size',

            /** @string Selector for a failure message which will be displayed if the user uploads an existing file */
            failureMessageDuplicateNameSelector: '.duplicate-name',

            /** @string Selector for a failure message which will be displayed if the user uploads a not allowed file */
            failureMessageNotAllowedMimeType: '.not-allowed-mime-type',

            /** @string Class which will be added when the drop was successful */
            successCls: 'is--success',

            /** @string Selector for a success panel which will be displayed in the upload widget */
            successMessageSelector: '.custom-products--uploading-success',

            /** @string Selector for a uploading information panel which will be displayed in the upload widget */
            uploadingMessageSelector: '.custom-products--uploading-information',

            /** @string Selector which points to the parent element in the default view */
            parentSelector: '.custom-product--option-wrapper',

            /** @string Selector which points to the parent element in the wizard */
            parentSelectorWizard: '.custom-product--option-wrapper-wizard',

            /** @string Selector which points to the wizard wrapper */
            wizardWrapperSelector: '.custom-products--wizard-container',

            /** @integer Number in milliseconds which represents the duration a message in the upload widget will be displayed */
            messageTimeout: 4000,

            /** @string Template for the file uploads */
            fileTpl: [
                '<ul class="upload--list">',
                    '{{#each files}}',
                        '{{#if error}}',
                            '<li class="upload--item has--error">',
                        '{{else}}',
                            '<li class="upload--item">',
                        '{{/if}}',
                        '<i class="{{../iconCls}}"></i>',
                        '<span class="upload--title">{{title}}</span>',
                        '{{#if isUploaded}}',
                            '<i class="icon--check is--uploaded"></i>',
                        '{{else}}',
                            '<button class="btn btn-secondary is--small upload--btn-delete" data-index="{{index}}">',
                                '<i class="icon--cross"></i>',
                            '</button>',
                        '{{/if}}',
                        '</li>',
                    '{{/each}}',
                '</ul>'
            ].join(''),

            imageTpl: [
                '<ul class="upload--list">',
                    '{{#each files}}',
                        '{{#if error}}',
                            '<li class="upload--item has--error">',
                        '{{else}}',
                            '<li class="upload--item">',
                        '{{/if}}',
                        '<img src="{{path}}" title="{{title}}" class="upload-preview-image is--hidden" />',
                        '<span class="upload--title">{{title}}</span>',
                        '{{#if isUploaded}}',
                            '<i class="icon--check is--uploaded"></i>',
                        '{{else}}',
                            '<button class="btn btn-secondary is--small upload--btn-delete" data-index="{{index}}">',
                                '<i class="icon--cross"></i>',
                            '</button>',
                        '{{/if}}',
                        '</li>',
                    '{{/each}}',
                '</ul>'
            ].join('')
        },

        /**
         * Sets up the normal events for the plugin. It's the case when the user uses a legacy browser.
         *
         * @returns {registerNormalModeEventListeners}
         */
        registerNormalModeEventListeners: function() {
            var me = this;

            me._on(me.$uploadInput, 'change', $.proxy(me.onChange, me));
            me._on(me.$uploadBtn, 'click', $.proxy(me.onUploadFiles, me));

            return me;
        },

        /**
         * Initializes the plugin, caches the necessary elements and parses the client side template using
         * {@link Handlebars}.
         *
         * @constructor
         * @returns {init}
         */
        init: function() {
            var me = this;

            me.applyDataAttributes();

            me._isAdvancedUpload = me.isAdvancedUpload();
            me.$parent = me.$el.parents(me.opts.parentSelector);

            // Use wizard selector the parent element wasn't found
            if (me.$parent.length === 0) {
                me.$parent = me.$el.parents(me.opts.parentSelectorWizard);
            }

            me._$previewContainer = me.$parent.find(me.opts.previewContainerSelector);
            me.$uploadBtn = me.$parent.find(me.opts.uploadBtnSelector);
            me.$uploadInput = me.$el.find('input[type="file"]');
            me._inputName = me.$uploadInput.attr('name');

            me.$failureMessage = me.$el.find(me.opts.failureMessageSelector);
            me.$successMessage = me.$el.find(me.opts.successMessageSelector);
            me.$uploadingMessage = me.$el.find(me.opts.uploadingMessageSelector);

            me.$errorContainer = me.$el.prev('.alert');

            me._files = [];

            if (me._isAdvancedUpload) {
                me.toggleUploadButton();
                me.$el.addClass(me.opts.advancedCls);
                me.registerAdvancedEventListeners();
            }

            me.registerNormalModeEventListeners();

            if (me.opts.mode === 'image') {
                me.tpl = Handlebars.compile(me.opts.imageTpl);
            } else {
                me.tpl = Handlebars.compile(me.opts.fileTpl);
            }

            $.publish('plugin/swagCustomProductsUpload/init', [ me ]);

            return me;
        },

        /**
         * Sets up the event listeners for the advanced mode of the uploader. This is the case when the user uses
         * a modern browser.
         */
        registerAdvancedEventListeners: function() {
            var me = this;

            // First catch all possible drag or drop events to disable the default behavior
            me._on(me.$el, 'drag dragstart dragend dragover dragenter dragleave drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
            });

            me._on(me.$el, 'dragover dragenter', $.proxy(me.onDragStart, me));
            me._on(me.$el, 'dragleave dragend drop', $.proxy(me.onDragEnd, me));
            me._on(me.$el, 'drop', $.proxy(me.onDrop, me));

            me._$previewContainer.on(me.getEventName('click'), me.opts.deleteFileBtnSelector, $.proxy(me.onDeleteFile, me));

            $.publish('plugin/swagCustomProductsUpload/registerAdvancedEventListeners', [ me ]);
        },

        /**
         * Event handler which will be fired when the user changes the value of the input.
         *
         * The method adds the selected files to the plugin.
         *
         * @param {Event} e
         */
        onChange: function(e) {
            var me = this,
                duplicateFile = false,
                files = e.target.files,
                totalLength = me._files.length + files.length,
                newFile, oldFile;

            // Check for duplicate file names
            duplicateFile = false;
            $.each(files, function () {
                newFile = this;

                $.each(me._files, function () {
                    oldFile = this;

                    if (newFile.name === oldFile.name) {
                        duplicateFile = true;
                        return false;
                    }
                });
            });

            if (duplicateFile) {
                me.showFailureMessage(me.opts.failureMessageDuplicateNameSelector);
                $.publish('plugin/swagCustomProductsUpload/onChange/onFail', [ me, files, e ]);
                return;
            }

            // Validate amount of uploaded files
            if (totalLength > me.opts.maxFiles) {
                me.showFailureMessage(me.opts.failureMessageMaxFilesSelector);
                $.publish('plugin/swagCustomProductsUpload/onChange/onFail', [ me, files, e ]);
                return;
            }

            me.addFiles(files);
            me.$uploadInput.val('');

            $.publish('plugin/swagCustomProductsUpload/onChange/onSuccess', [ me, files, e ]);
        },

        /**
         * Helper method which is using feature detection to check if the browser features advanced uploading features.
         *
         * @returns {boolean}
         */
        isAdvancedUpload: function() {
            var testEl = document.createElement('div');
            return (('draggable' in testEl) || ('ondrag' in testEl && 'ondrop' in testEl)) && 'FormData' in window && 'FileReader' in window;
        },

        /**
         * Event handler method which will be fired when the user starts dragging files / images over the drop zone.
         *
         * @param {Event} e
         */
        onDragStart: function(e) {
            var me = this;

            e.stopPropagation();
            me.$el.addClass(me.opts.dragCls);

            $.publish('plugin/swagCustomProductsUpload/onDragStart', [ me, e ]);
        },

        /**
         * Event handler method which will be fired when the user ends the drag operation.
         *
         * @param {Event} e
         */
        onDragEnd: function(e) {
            var me = this;

            me.$el.removeClass(me.opts.dragCls);

            $.publish('plugin/swagCustomProductsUpload/onDragEnd', [ me, e ]);
        },

        /**
         * Event handler method which will be fired when the user drops the files / images onto the drop zone.
         *
         * The method checks the file limit and add them to the files.
         *
         * @param {Event} e
         */
        onDrop: function(e) {
            var me = this,
                files = e.originalEvent.dataTransfer.files,
                totalLength = me._files.length + files.length;

            if (totalLength > me.opts.maxFiles) {
                me.showFailureMessage(me.opts.failureMessageMaxFilesSelector);
                $.publish('plugin/swagCustomProductsUpload/onDrop/onFail', [ me, files, e ]);

                return;
            }

            me.showSuccessMessage();
            me.addFiles(files);
            $.publish('plugin/swagCustomProductsUpload/onDrop/onSuccess', [ me, files, e ]);
        },

        /**
         * Adds files to the plugin and maps the file names for the preview.
         *
         * @param {FileList} files
         */
        addFiles: function(files) {
            var me = this;

            $.each(files, function() {
                var file = this,
                    idx = me._files.map(function(file) {
                        return file.name.toLowerCase();
                    }).indexOf(file.name.toLowerCase()),
                    uploadAllowed = false;

                if (idx === -1) {
                    if (file.size > me.opts.maxFileSize) {
                        me.showFailureMessage(me.opts.failureMessageMaxFileSizeSelector);
                        return false;
                    }

                    if (me.opts.mode === 'image') {
                        if (me.opts.allowedMimeTypes.indexOf(file.type) !== -1) {
                            uploadAllowed = true;
                        }

                        if (!uploadAllowed) {
                            me.showFailureMessage(me.opts.failureMessageNotAllowedMimeType);
                            return false;
                        }
                    }

                    file._uploaded = false;
                    me._files.push(file);
                }
            });

            $.publish('plugin/swagCustomProductsUpload/addFiles', [ me, files ]);

            me.previewFiles();
        },

        /**
         * Loads uploaded files which will be set if an existing configuration will be loaded.
         *
         * @param {Array} files
         */
        addUploadedFiles: function(files) {
            var me = this,
                form = $('.custom-products--form');

            $.each(files, function () {
                var file = this;

                file._uploaded = true;
                me._files.push(file);

                me.$uploadInput.data('data-uploadResponse', JSON.stringify(files));

                $.publish('plugin/swagCustomProductsUpload/addUploadedFiles', [ me, files ]);

                // Fire form change event
                form.change();

                // Trigger the wizard change event
                if (me.$parent.hasClass('custom-product--option-wrapper-wizard')) {
                    me.$parent.trigger('change');
                }

                me.previewFiles();
            });
        },

        /**
         * Event handler method which will be fired when the user clicks on the "upload" button. The method uses
         * {@link FormData} to provide all information for the upload.
         *
         * @param {Event} event
         * @returns void
         */
        onUploadFiles: function(event) {
            var me = this,
                formData = new window.FormData(),
                name = me.$uploadInput.attr('name'),
                form = $('.custom-products--form'),
                optionManager = form.data('plugin_optionManager'),
                jsonFiles;

            event.preventDefault();

            $.each(optionManager._data, function(key, val) {
                formData.append(key, val);
            });

            // Add files to formData
            $.each(me._files, function() {
                var file = this;
                if (file._uploaded) {
                    return;
                }
                formData.append(name, file);
            });

            formData.append('mode', me.opts.mode);
            formData.append('templateId', me.opts.templateId);
            formData.append('optionId', me.opts.optionId);

            me.$uploadBtn.attr('disabled', 'disabled').addClass(me.opts.uploadingCls);
            me.$el.addClass(me.opts.uploadingCls);
            me.$errorContainer.addClass('is--hidden');

            me.toggleUploadingMessage(true);

            $.publish('plugin/swagCustomProductsUpload/onUploadFiles/beforeUpload', [ me, formData, name ]);

            $.ajax({
                url: optionManager.generateProtocolRelativeUrl(me.opts.uploadURL),
                type: 'post',
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                complete: function() {
                    me.toggleUploadButton();
                    me.toggleUploadingMessage(false);
                    me.$uploadBtn.removeClass(me.opts.uploadingCls);
                    me.$el.removeClass(me.opts.uploadingCls)
                        .removeClass(me.opts.successCls)
                        .removeClass(me.opts.failureCls);

                    $.publish('plugin/swagCustomProductsUpload/onUploadFiles/afterUpload/complete', [ me, formData, name ]);
                },
                success: function(response) {
                    if (!response.success) {
                        me.$errorContainer.find('.alert--content').html(response.message);
                        me.$errorContainer.removeClass('is--hidden');

                        // Set error flags for every invalid file
                        $.each(response.errorFiles, function () {
                            var fileName = this;

                            $.each(me._files, function () {
                                if (this.name === fileName) {
                                    this.error = true;
                                }
                            });
                        });

                        me.previewFiles();

                        $.publish('plugin/swagCustomProductsUpload/onUploadFiles/afterUpload/success/onFail', [ me, formData, name ]);

                        return;
                    }

                    $.each(me._files, function () {
                        var file = this;
                        file._uploaded = true;
                    });

                    jsonFiles = me.addFilesToDataInputJson(response.files);
                    me.$uploadInput.data('data-uploadResponse', jsonFiles);

                    optionManager.setHashToHistory(response.hash);

                    // Fire form change event
                    form.change();

                    // Trigger the wizard change event
                    if ($(me.opts.wizardWrapperSelector)) {
                        $(me.opts.wizardWrapperSelector).trigger('change');
                    }

                    $.publish('plugin/swagCustomProductsUpload/onUploadFiles/afterUpload/success/onSuccess', [ me, formData, name, me._files ]);
                    me.previewFiles();
                },
                error: function(response) {
                    if (response.message) {
                        me.$errorContainer.find('.alert--content').html(response.message);
                    }
                    me.$errorContainer.removeClass('is--hidden');

                    $.publish('plugin/swagCustomProductsUpload/onUploadFiles/afterUpload/error', [ me, formData, name, me._files ]);
                }
            });
        },

        /**
         * Adds all new uploaded files to the response json string.
         *
         * @param {Array} newFiles
         * @return {String}
         */
        addFilesToDataInputJson: function(newFiles) {
            var me = this,
                files = newFiles;

            if (me.$uploadInput.data('data-uploadResponse')) {
                files = JSON.parse(me.$uploadInput.data('data-uploadResponse'));

                $.each(newFiles, function() {
                    var file = this;
                    files.push(file);
                });
            }

            return JSON.stringify(files);
        },

        /**
         * Event handler method which will be fired when the user clicks on the "delete" button in the preview list.
         *
         * @param {Event} event
         */
        onDeleteFile: function(event) {
            var me = this,
                $target = $(event.currentTarget),
                idx = $target.attr('data-index');

            event.preventDefault();

            if (idx.length > 0) {
                idx = window.parseInt(idx, 10);
            }

            if (typeof me._files[idx] === 'object') {
                me._files.splice(idx, 1);
            }

            if (me._files.length === 0) {
                me.$errorContainer.addClass('is--hidden');
            }

            $.publish('plugin/swagCustomProductsUpload/onDeleteFile', [ me, idx, event ]);

            me.previewFiles();
        },

        /**
         * Provides the data for the preview and renders it in the corresponding container.
         *
         * @returns {boolean}
         */
        previewFiles: function() {
            var me = this,
                previewData = [];

            $.each(me._files, function(i, file) {
                previewData.push({
                    index: i,
                    title: file.name,
                    isUploaded: file._uploaded,
                    error: file.error
                });
            });

            me.toggleUploadButton();

            me._$previewContainer.empty();
            if (me._files.length > 0) {
                me._$previewContainer.html(me.tpl({ files: previewData, iconCls: me.opts.fileIconCls }));
            }

            if (me.opts.mode === 'image') {
                $.each(me._files, function(i) {
                    var file = this,
                        reader = new FileReader(),
                        $previewImg = me._$previewContainer.find('.upload--list li:nth-child(' + (i + 1) + ') .upload-preview-image');

                    if (file._uploaded && !(file instanceof Blob)) {
                        $previewImg.attr('src', file.path).removeClass('is--hidden');
                    } else {
                        reader.addEventListener('load', function() {
                            $previewImg.attr('src', reader.result).removeClass('is--hidden');

                            $.publish('plugin/swagCustomProductsUpload/previewFiles/image', [ me, file, $previewImg, reader.result ]);
                        }, false);

                        reader.readAsDataURL(file);
                    }
                });
            }

            $.publish('plugin/swagCustomProductsUpload/previewFiles', [ me, me._files ]);

            return true;
        },

        /**
         * Enables / disables the "upload" button based on the configured file limit.
         */
        toggleUploadButton: function() {
            var me = this,
                notUploaded = 0;

            $.each(me._files, function() {
                var file = this;

                if (!file._uploaded) {
                    notUploaded++;
                }
            });

            if (notUploaded > 0 && me._files.length <= me.opts.maxFiles) {
                me.$uploadBtn.removeAttr('disabled');
            } else {
                me.$uploadBtn.attr('disabled', 'disabled');
            }

            $.publish('plugin/swagCustomProductsUpload/toggleUploadButton', [ me, notUploaded ]);
        },

        /**
         * Shows the failure panel in the upload widget.
         *
         * @returns void
         */
        showFailureMessage: function(messageSelector) {
            var me = this;

            me.$el.addClass(me.opts.failureCls);
            me.$failureMessage.show().css('opacity', 1);
            $(messageSelector).show().css('display', 'block');

            me.$uploadInput.val('');

            window.setTimeout(function() {
                me.$el.removeClass(me.opts.failureCls);
                me.$failureMessage.css('opacity', 0).hide();
                $(messageSelector).css('display', 'none');
            }, me.opts.messageTimeout);

            $.publish('plugin/swagCustomProductsUpload/showFailureMessage', [ me ]);
        },

        /**
         * Shows the success panel in the upload widget.
         *
         * @returns void
         */
        showSuccessMessage: function() {
            var me = this;

            me.$el.addClass(me.opts.successCls);
            me.$successMessage.show().css('opacity', 1);

            window.setTimeout(function() {
                me.$el.removeClass(me.opts.successCls);
                me.$successMessage.css('opacity', 0).hide();
            }, me.opts.messageTimeout);

            $.publish('plugin/swagCustomProductsUpload/showSuccessMessage', [ me ]);
        },

        /**
         * Toggles e.g. shows / hides the uploading message in the upload widget.
         *
         * @param {boolean} toggle
         * @returns void
         */
        toggleUploadingMessage: function(toggle) {
            var me = this;

            $.publish('plugin/swagCustomProductsUpload/toggleUploadingMessage', [ me ]);

            if (toggle === true) {
                me.$el.addClass(me.opts.successCls);
                me.$uploadingMessage.show().css('opacity', 1);
                return;
            }
            me.$el.removeClass(me.opts);
            me.$uploadingMessage.css('opacity', 0).hide();
        },

        /**
         * Resets the upload widget.
         *
         * @param {String} val
         * @returns void
         */
        reset: function(val) {
            var me = this;

            $.publish('plugin/swagCustomProductsUpload/reset', [ me ]);

            if (!val) {
                me._files = new Array(0);
                me.$uploadInput.data('data-uploadResponse', '');
            } else {
                me.addUploadedFiles(JSON.parse(val));
            }

            if (me.$parent.hasClass('custom-product--option-wrapper-wizard')) {
                me.$parent.trigger('change');
            }

            me.previewFiles();
        },

        /**
         * Destroys the plugin instance and clears up the variables for performance purpose.
         *
         * @returns void
         */
        destroy: function() {
            var me = this;

            me._$previewContainer.off(me.getEventName('click'));
            me._destroy();

            $.publish('plugin/swagCustomProductsUpload/destroy', [ me ]);
        }
    });

    // Plugin starter
    $(function() {
        $('*[data-swag-custom-products-upload="true"]').swagCustomProductsUpload();
    });
})(jQuery, window, document, Handlebars);
