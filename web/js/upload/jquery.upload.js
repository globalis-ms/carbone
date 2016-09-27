;(function($) {
    $(document).on('ready', function () {

        // Keep track of the dropped files, as we can't modify an input .files object
        var dropped_files = {};

        // Clear the field when clicked, to be consistent with HTML
        $('.upload-field-drag-box').on('click', function (ev) {
            var $drag_box_content = $(this).find('.upload-field-drag-box-content');
            var $field = $(this).find('input[type="file"]');
            $field.data('dropped-files', null);
            $drag_box_content.empty();
        });

        // Change the label when the field change. The event is on the parent form so we can delegate if there's multiple files input
        $('.upload-field-drag-box').parents('form').on('change', 'input[type="file"]', function (ev) {
            var $this = $(this);
            var $drag_box_content = $('.upload-field-drag-box[data-for-field="' + $(this).attr('name') + '"]').find('.upload-field-drag-box-content');
            $drag_box_content.empty();

            $this.data('dropped-files', null);

            // Label depends on the options checked
            if (typeof this.files !== 'undefined') {
                if (this.files.length === 1) {
                    var $span = $(document.createElement('span')).text(this.files[0].name).appendTo($drag_box_content);
                } else if (this.files.length > 0) {
                    var label = $drag_box_content.attr('data-selected-multiple-label').replace('%d', this.files.length);
                    var $span = $(document.createElement('span')).text(label).appendTo($drag_box_content);
                }
            } else {
                var label = $drag_box_content.attr('data-selected-label');
                var $span = $(document.createElement('span')).text(label).appendTo($drag_box_content);
            }
            return false;
        }).trigger('change');


        // Drag and drop

        // Don't continue if there's no drag and drop or filelist enabled on the browser
        var div = document.createElement('div');
        if (!('FormData' in window)
        || !(('draggable' in div) || ('ondragstart' in div && 'ondrop' in div))
        || !('FileReader' in window)) {
            return;
        }
        delete div;

        // Change the upload box style when drag and drop is starting
        $('.upload-field-drag-box.is-drag-upload').on('drag dragstart dragend dragover dragenter dragleave drop', function (ev) {
            ev.preventDefault();
            ev.stopPropagation();
        }).on('dragover dragenter', function() {
            $(this).addClass('is-dragover');
        }).on('drop dragleave dragend', function() {
            $(this).removeClass('is-dragover');
        }).on('drop', function(e) {
            if (e.originalEvent.dataTransfer.files.length === 0) {
                return;
            }

            // Recreates File input to clear it, if something was already stored. It avoids to upload files accidentally if something was selected manually before the drop
            var $file_input = $(this).next('input[type="file"]');
            var $new_file_input = $(this).next('input[type="file"]').clone().insertAfter(this);
            $file_input.remove();

            // Store the files
            $new_file_input.data('dropped-files', e.originalEvent.dataTransfer.files);

            var $drag_box_content = $(this).find('.upload-field-drag-box-content');
            $drag_box_content.empty();
            // Translate the label depending on the options for the box
            if (e.originalEvent.dataTransfer.files.length === 1) {
                var $span = $(document.createElement('span')).text(e.originalEvent.dataTransfer.files[0].name).appendTo($drag_box_content);
            } else if (e.originalEvent.dataTransfer.files.length > 0) {
                var label = $drag_box_content.attr('data-selected-multiple-label').replace('%d', e.originalEvent.dataTransfer.files.length);
                var $span = $(document.createElement('span')).text(label).appendTo($drag_box_content);
            }
        });

        // Handle file upload
        $('.upload-field-drag-box').parents('form').on('submit', function (ev) {
            var $form = $(this);

            // Allow the form submit when already uploaded
            if ($form.hasClass('is-uploaded')) {
                return true;
            }

            // If it's still uploading, don't continue processing
            if ($form.hasClass('is-uploading')) {
                ev.preventDefault();
                return false;
            }


            if (!$form.hasClass('is-uploaded')) {
                // Start the progress bar
                start_upload_progress(this);

                var $file_fields = $form.find('input[type="file"]');

                var form_data = new FormData();
                var any_added = false;

                // Add progress field before anything
                var $progress_field = $form.find('input[name="PHP_SESSION_UPLOAD_PROGRESS"]');
                if ($progress_field.length > 0) {
                    form_data.append('PHP_SESSION_UPLOAD_PROGRESS', $progress_field.val());
                }

                // Create the FormData element for the actual upload
                $file_fields.each(function() {
                    var $field = $(this);
                    var dropped_files = $field.data('dropped-files');
                    if (dropped_files === null) {
                        return;
                    }

                    for (var i = 0; i < dropped_files.length; i++) {
                        var file = dropped_files[i];
                        form_data.append($file_fields.attr('name'), file);
                        any_added = true;
                    }
                });

                // If there's nothing to upload, just don't do anything
                if (!any_added) {
                    return true;
                }

                // Disable form revalidation (and thus reuploading)
                $form.addClass('is-uploading');
                ev.preventDefault();

                // Send our request to the upload file
                $.ajax({
                    type: 'post',
                    url: 'dist/upload_file.php',
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    data: form_data,
                    success: function (data) {

                        for (var file in data) {
                            var filename = data[file];
                            // Create the input element to be handled by form_parser
                            var name = filename.substr(0, filename.lastIndexOf('_tmp')) + '_ajax';
                            var input = $(document.createElement('input')).attr({
                                type: 'hidden',
                                name: name,
                                value: '1',
                            }).appendTo($form);
                        }

                        $form.addClass('is-uploaded');
                        $form.submit();
                    },
                    complete: function() {
                        $form.removeClass('is-uploading');
                    },
                });

                return false;
            }

            return false;
        });


        function start_upload_progress(element) {
            // Update upload bar every second
            element.upload_progress_interval = setInterval(function() {
                upload_progress(element);
            }, 1000);
        }
        function upload_progress(element) {
            // Request upload information on the server
            jQuery.ajax({
                url: document.location.href.substr(0, document.location.href.lastIndexOf('/')) + '/dist/upload_progress.php',
                success: function(data, statut) {
                    if (typeof data !== 'undefined') {
                        var json = jQuery.parseJSON(data);

                        if (typeof json === 'undefined' || json === null || typeof json.bytes_processed === 'undefined') {
                            clearInterval(element.upload_progress_interval);
                            return;
                        }

                        // Calculate the current upload position and change the :after used for the actual scrollbar
                        var current = parseFloat(json.bytes_processed / json.content_length * 100);

                        var $drag_box_after = $('.upload-field-drag-box').find('.upload-field-drag-box-progress');
                        $drag_box_after.css({ width: current + '%' });
                    }
                }
            });
        }

    });
})(jQuery);

