jQuery(document).ready(function ($) {
    'use strict';


    // Reindex table
    $('#aws-reindex .button').on( 'click', function(e) {

        e.preventDefault();

        var $reindexBlock = $(this).closest('#aws-reindex');

        $reindexBlock.addClass('loading');

        $.ajax({
            type: 'POST',
            url: aws_vars.ajaxurl,
            data: {
                action: 'aws-reindex'
            },
            dataType: "json",
            success: function (data) {
                console.log('Reindex complete!');
                $reindexBlock.removeClass('loading');
            }
        });

    });

    // Clear cache
    $('#aws-clear-cache .button').on( 'click', function(e) {

        e.preventDefault();

        var $clearCacheBlock = $(this).closest('#aws-clear-cache');

        $clearCacheBlock.addClass('loading');

        $.ajax({
            type: 'POST',
            url: aws_vars.ajaxurl,
            data: {
                action: 'aws-clear-cache'
            },
            dataType: "json",
            success: function (data) {
                alert('Cache cleared!');
                $clearCacheBlock.removeClass('loading');
            }
        });

    });


});