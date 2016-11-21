<?php

class N2ImageHelper extends N2ImageHelperAbstract {

    public static function initLightbox() {
        static $inited = false;
        if (!$inited) {
            wp_enqueue_media();
            $inited = true;
        }
    }

    public static function getLightboxFunction() {
        return 'function (callback) {
        var frame = new wp.media();

        frame.on("select", $.proxy(function () {
            var attachment = frame.state().get("selection").first().toJSON();
            callback(this.make(attachment.url));
        }, this));
        frame.on("close", function () {
            setTimeout(function () {
                NextendEsc.pop();
            }, 50)
        });
        NextendEsc.add(function () {
            return false;
        });
        frame.open();
    }';
    }

    public static function getLightboxMultipleFunction() {
        return 'function (callback) {
        var frame = new wp.media({
            multiple: "add"
        });

        frame.on("select", $.proxy(function () {
            var attachments = frame.state().get("selection").toJSON(),
                images = [];

            for (var i = 0; i < attachments.length; i++) {
                var attachment = attachments[i];
                images.push({
                    title: attachment.title,
                    description: attachment.description,
                    image: this.make(attachment.url),
                    alt: attachment.alt
                })
            }
            callback(images);
        }, this));
        frame.on("close", function () {
            setTimeout(function () {
                NextendEsc.pop();
            }, 50)
        });
        frame.open();
        NextendEsc.add(function () {
            return false;
        });
    }';
    }

    public static function onImageUploaded($filename) {
        // Check the type of file. We'll use this as the 'post_mime_type'.
        $filetype = wp_check_filetype(basename($filename), null);

        // Get the path to the upload directory.
        $wp_upload_dir = wp_upload_dir();

        // Prepare an array of post data for the attachment.
        $attachment = array(
            'guid'           => $wp_upload_dir['url'] . '/' . basename($filename),
            'post_mime_type' => $filetype['type'],
            'post_title'     => preg_replace('/\.[^.]+$/', '', basename($filename)),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );

        // Insert the attachment.
        $attach_id = wp_insert_attachment($attachment, $filename);

        // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        // Generate the metadata for the attachment, and update the database record.
        $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
        wp_update_attachment_metadata($attach_id, $attach_data);
    }
}