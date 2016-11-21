(function($){
    "use strict";

    var selector = '.aws-container';
    var instance = 0;
    var pluginPfx = 'aws_opts';

    $.fn.aws_search = function( options ) {


        instance++;


        var self           = $(this),
            $searchForm    = self.find('.aws-search-form'),
            $searchField   = self.find('.aws-search-field'),
            haveResults    = false,
            requests       = Array(),
            searchFor      = '',
            cachedResponse = new Array();


        var ajaxUrl = ( self.data('url') !== undefined ) ? self.data('url') : false;
        //var siteUrl = ( self.data('siteurl') !== undefined ) ? self.data('siteurl') : false;


        self.data( pluginPfx, {
            minChars  : ( self.data('min-chars')   !== undefined ) ? self.data('min-chars') : 1,
            showLoader: ( self.data('show-loader') !== undefined ) ? self.data('show-loader') : true,
            instance: instance,
            resultBlock: '#aws-search-result-' + instance
        });


        var d = self.data(pluginPfx);


        var methods = {

            init: function() {

                $('body').append('<div id="aws-search-result-' + instance + '" class="aws-search-result" style="display: none;"></div>');

                setTimeout(function() { methods.resultLayout(); }, 500)

            },

            onKeyup: function(e) {

                searchFor = $searchField.val();
                searchFor = searchFor.trim();
                searchFor = searchFor.replace( /[`~!@#$%^&*()_|+\?;:'",.<>\{\}\[\]\\\/]/gi, '' );
                searchFor = searchFor.replace( /\s\s+/g, ' ' );

                for ( var i = 0; i < requests.length; i++ ) {
                    requests[i].abort();
                }

                if ( searchFor === '' ) {
                    $(d.resultBlock).html('');
                    return;
                }

                if ( typeof cachedResponse[searchFor] != 'undefined') {
                    methods.showResults( cachedResponse[searchFor] );
                    return;
                }

                if ( searchFor.length < d.minChars ) {
                    $(d.resultBlock).html('');
                    return;
                }

                if ( d.showLoader ) {
                    $searchForm.addClass('processing');
                }

                var data = {
                    action: 'aws_action',
                    keyword : searchFor,
                    page: 0
                };

                requests.push(

                    $.ajax({
                        type: 'POST',
                        url: ajaxUrl,
                        data: data,
                        success: function( response ) {

                            var response = $.parseJSON( response );

                            cachedResponse[searchFor] = response;

                            methods.showResults( response );

                            $(d.resultBlock).show();

                        },
                        error: function (data, dummy) {
                        }
                    })

                );

            },

            showResults: function( response ) {

                var html = '<ul>';


                if ( response.cats.length > 0 ) {

                    $.each(response.cats, function (i, result) {

                        html += '<li class="aws_result_item aws_result_cat">';
                        html += '<a class="aws_result_link" href="' + result.link + '" >';
                        html += '<span class="aws_result_content">';
                        html += '<span class="aws_result_title">';
                        html += result.name + '(' + result.count + ')';
                        html += '</span>';
                        html += '</span>';
                        html += '</a>';
                        html += '</li>';

                    });

                }

                if ( response.tags.length > 0 ) {

                    $.each(response.tags, function (i, result) {

                        html += '<li class="aws_result_item aws_result_tag">';
                        html += '<a class="aws_result_link" href="' + result.link + '" >';
                        html += '<span class="aws_result_content">';
                        html += '<span class="aws_result_title">';
                        html += result.name + '(' + result.count + ')';
                        html += '</span>';
                        html += '</span>';
                        html += '</a>';
                        html += '</li>';

                    });

                }

                if ( response.products.length > 0 ) {

                    $.each(response.products, function (i, result) {

                        html += '<li class="aws_result_item">';
                        html += '<a class="aws_result_link" href="' + result.link + '" >';

                        if ( result.image ) {
                            html += '<span class="aws_result_image">';
                            html += '<img src="' + result.image + '">';
                            html += '</span>';
                        }

                        html += '<span class="aws_result_content">';
                        html += '<span class="aws_result_title">' + result.title + '</span>';

                        if ( result.sku ) {
                            html += '<span class="aws_result_sku">SKU: ' + result.sku + '</span>';
                        }

                        if ( result.excerpt ) {
                            html += '<span class="aws_result_excerpt">' + result.excerpt + '</span>';
                        }

                        if ( result.price ) {
                            html += '<span class="aws_result_price">' + result.price + '</span>';
                        }

                        html += '</span>';

                        if ( result.on_sale ) {
                            html += '<span class="aws_result_sale">';
                            html += '<span class="aws_onsale">Sale!</span>';
                            html += '</span>';
                        }

                        html += '</a>';
                        html += '</li>';

                    });

                    //html += '<li class="aws_result_item aws_search_more"><a href="' + opts.siteUrl + '/?s=' + searchFor + '&post_type=product">View all</a></li>';
                    //html += '<li class="aws_result_item"><a href="#">Next Page</a></li>';

                }

                if ( response.cats.length <= 0 && response.tags.length <= 0 && response.products.length <= 0 ) {
                    html += '<li class="aws_result_item aws_no_result">Nothing found</li>';
                }


                html += '</ul>';

                $searchForm.removeClass('processing');
                $(d.resultBlock).html( html );

                $(d.resultBlock).show();

            },

            onFocus: function( event ) {
                if ( searchFor !== '' ) {
                    $(d.resultBlock).show();
                }
            },

            hideResults: function( event ) {
                if ( ! $(event.target).closest( ".aws-container" ).length ) {
                    $(d.resultBlock).hide();
                }
            },

            resultLayout: function () {
                var offset = self.offset();

                if ( offset ) {

                    var width = self.outerWidth();
                    var top = offset.top + $(self).innerHeight();
                    var left = offset.left;

                    $( d.resultBlock ).css({
                        width : width,
                        top : top,
                        left: left
                    });

                }

            }

        };


        if ( $searchForm.length > 0 ) {
            methods.init.call(this);
        }


        $searchField.on( 'keyup', function(e) {
            methods.onKeyup(e);
        });


        $searchField.on( 'focus', function (e) {
            methods.onFocus(e);
        });


        $(document).on( 'click', function (e) {
            methods.hideResults(e);
        });


        $(window).on( 'resize', function(e) {
            methods.resultLayout();
        });


    };


    // Call plugin method
    $(document).ready(function() {

        $(selector).each( function() {
            $(this).aws_search();
        });

    });


})( jQuery );