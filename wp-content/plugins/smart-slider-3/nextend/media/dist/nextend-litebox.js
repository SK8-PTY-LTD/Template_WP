;
(function ($, window, document, undefined) {
    function useragentIsIphone() {
        return (navigator.userAgent.match(/iPhone/i) != null) || (navigator.userAgent.match(/iPod/i) != null);
    }

    function useragentIsIpad() {
        return navigator.userAgent.match(/iPad/i) != null && !useragentIsIphone();
    }

//	LiteBox v1.3, Copyright 2014, Joe Mottershaw, https://github.com/joemottershaw/
//	===============================================================================
    var pluginName = 'liteBox',
        defaults = {
            revealSpeed: 400,
            background: 'rgba(0,0,0,.8)',
            overlayClose: true,
            escKey: true,
            navKey: true,
            closeTip: 'tip-l-fade',
            closeTipText: 'Close',
            prevTip: 'tip-t-fade',
            prevTipText: 'Previous',
            nextTip: 'tip-t-fade',
            nextTipText: 'Next',
            autoplay: false,
            callbackInit: function () {
            },
            callbackBeforeOpen: function () {
            },
            callbackAfterOpen: function () {
            },
            callbackBeforeClose: function () {
            },
            callbackAfterClose: function () {
            },
            callbackError: function () {
            },
            callbackPrev: function () {
            },
            callbackNext: function () {
            },
            errorMessage: 'Error loading content.'
        };

    function liteBox(element, options) {
        this.element = element;
        this.$element = $(this.element);

        this.options = $.extend({}, defaults, options);

        this._defaults = defaults;
        this._name = pluginName;

        this.init();
    }

    function winHeight() {
        return window.innerHeight ? window.innerHeight : $(window).height();
    }

    function preloadImageArray(images) {
        $(images).each(function () {
            var image = new Image();

            image.src = this;

            if (image.width > 0)
                $('<img />').attr('src', this).addClass('litebox-preload').appendTo('body').hide();
        });
    }

    liteBox.prototype = {
        init: function () {
            // Variables
            var $this = this,
                timeout = false;

            // Element click
            this.$element.on('click n2click', function (e) {
                if (timeout === false) {
                    e.preventDefault();
                    $this.openLitebox();
                    timeout = setTimeout(function () {
                        timeout = false;
                    }, 300);
                }
            });

            // Callback
            this.options.callbackInit.call(this);
        },

        openLitebox: function () {
            // Variables
            var $this = this;

            // Before callback
            this.options.callbackBeforeOpen.call(this);

            // Build
            $this.buildLitebox();

            // Populate
            var link = this.$element;
            $this.populateLitebox(link);

            // Interactions
            if ($this.options.overlayClose)
                $litebox.on('click', function (e) {
                    if (e.target === this || $(e.target).hasClass('litebox-container') || $(e.target).hasClass('litebox-error'))
                        $this.closeLitebox();
                });

            $close.on('click', function () {
                $this.closeLitebox();
            });

            // Groups
            if (this.$element.attr('data-litebox-group')) {
                var $this = this,
                    groupName = this.$element.attr('data-litebox-group'),
                    group = $('[data-litebox-group="' + this.$element.attr('data-litebox-group') + '"]');

                var imageArray = [];

                $('[data-litebox-group="' + groupName + '"]').each(function () {
                    var src = $(this).attr('href') || $(this).data('href');

                    imageArray.push(src);
                });

                preloadImageArray(imageArray);

                $('.litebox-nav').show();

                $prevNav.off('click').on('click', function () {
                    $this.options.callbackPrev.call(this);

                    var index = group.index(link);

                    link = group.eq(index - 1);

                    if (!$(link).length)
                        link = group.last();

                    $this.populateLitebox(link);
                });

                $nextNav.off('click').on('click', function () {
                    $this.options.callbackNext.call(this);

                    var index = group.index(link);

                    link = group.eq(index + 1);

                    if (!$(link).length)
                        link = group.first();

                    $this.populateLitebox(link);

                    $this.startAutoplay();
                });
            }

            // Interaction
            var keyEsc = 27,
                keyLeft = 37,
                keyRight = 39;

            $('body').on('keydown.litebox', function (e) {
                if ($this.options.escKey && e.keyCode == keyEsc) {
                    e.stopImmediatePropagation();
                    $this.closeLitebox();
                }

                if ($this.options.navKey && e.keyCode == keyLeft) {
                    e.stopImmediatePropagation();
                    $('.litebox-prev').trigger('click');
                }

                if ($this.options.navKey && e.keyCode == keyRight) {
                    e.stopImmediatePropagation();
                    $('.litebox-next').trigger('click');
                }
            });

            this.startAutoplay();
            // After callback
            this.options.callbackAfterOpen.call(this);

            if (useragentIsIpad() || useragentIsIphone()) {
                $('.litebox-container').addClass('litebox-iframe-holder');
            }
        },

        startAutoplay: function () {
            if (this.timeout) {
                clearTimeout(this.timeout);
                this.timeout = null;
            }
            if (this.options.autoplay) {
                var $this = this;
                this.timeout = setTimeout(function () {
                    $('.litebox-next').trigger('click');
                }, this.options.autoplay);
            }
        },

        buildLitebox: function () {
            // Variables
            var $this = this;

            $litebox = $('<div>', {'class': 'litebox-overlay'}),
                $close = $('<div>', {
                    'class': 'litebox-close ' + this.options.closeTip,
                    'data-tooltip': this.options.closeTipText
                }),
                $text = $('<div>', {'class': 'litebox-text'}),
                $error = $('<div class="litebox-error"><span>' + this.options.errorMessage + '</span></div>'),
                $prevNav = $('<div>', {
                    'class': 'litebox-nav litebox-prev ' + this.options.prevTip,
                    'data-tooltip': this.options.prevTipText
                }),
                $nextNav = $('<div>', {
                    'class': 'litebox-nav litebox-next ' + this.options.nextTip,
                    'data-tooltip': this.options.nextTipText
                }),
                $container = $('<div>', {'class': 'litebox-container'}),
                $loader = $('<div>', {'class': 'litebox-loader'});

            // Insert into document
            $(document.fullscreenElement || document.msFullscreenElement || document.mozFullScreenElement || document.webkitFullscreenElement || document.body).prepend($litebox.css({'background-color': this.options.background}));

            $litebox.append($close, $text, $prevNav, $nextNav, $container);

            $litebox.fadeIn(this.options.revealSpeed);
        },

        populateLitebox: function (link) {
            // Variables
            var $this = this,
                href = link.attr('href') || link.data('href'),
                $currentContent = $('.litebox-content');

            this.options.autoplay = link.data('autoplay') || this.options.autoplay;

            // Show loader
            $litebox.append($loader);

            // Show image title
            var $text = link.data('title');

            if (typeof $text == 'undefined' || $text == '') {
                $('.litebox-text').removeClass('active');
                $('.litebox-text').html();
            } else {
                $text = '<b>' + $text + '</b>';

                // Show image description
                var $description = link.data('description');

                if (typeof $description != 'undefined' && $description != '') {
                    $text += ' - ' + $description;
                }

                $('.litebox-text').html($text);
                $('.litebox-text').addClass('active');
            }

            // Process
            if (href.match(/\.(jpeg|jpg|gif|png|bmp)/i) !== null) {
                var $img = $('<img>', {'src': href, 'class': 'litebox-content'});

                $this.transitionContent('image', $currentContent, $img);

                $('img.litebox-content').n2imagesLoaded(function () {
                    $loader.remove();
                });

                $img.error(function () {
                    $this.liteboxError();
                    $loader.remove();
                });
            } else if (videoURL = href.match(/(youtube|youtu|vimeo|dailymotion|kickstarter)\.(com|be)\/((watch\?v=([-\w]+))|(video\/([-\w]+))|(projects\/([-\w]+)\/([-\w]+))|([-\w]+))/)) {
                var src = '';

                if (videoURL[1] == 'youtube')
                    src = 'https://www.youtube.com/embed/' + videoURL[5] + '?fs=1&amp;wmode=opaque&amp;autoplay=1;rel=0';

                if (videoURL[1] == 'youtu')
                    src = 'https://www.youtube.com/embed/' + videoURL[3] + '?fs=1&amp;wmode=opaque&amp;autoplay=1;rel=0';

                if (videoURL[1] == 'vimeo')
                    src = 'https://player.vimeo.com/video/' + videoURL[3] + '?autoplay=1';

                if (videoURL[1] == 'dailymotion')
                    src = 'https://www.dailymotion.com/embed/video/' + videoURL[7];

                if (videoURL[1] == 'kickstarter')
                    src = 'https://www.kickstarter.com/projects/' + videoURL[9] + '/' + videoURL[10] + '/widget/video.html';

                if (src) {
                    var $iframe = $('<iframe>', {
                        'frameborder': '0',
                        'vspace': '0',
                        'hspace': '0',
                        'scrolling': 'no',
                        'allowfullscreen': '',
                        'class': 'litebox-content',
                        'style': 'background: #000',
                        'seamless': 'seamless'
                    });

                    $this.transitionContent('embed', $currentContent, $iframe);

                    $iframe.attr('src', src);

                    $iframe.load(function () {
                        $loader.remove();
                    });
                }
            } else if (href.substring(0, 1) == '#') {
                if ($(href).length) {
                    $html = $('<div>', {'class': 'litebox-content litebox-inline-html'});

                    $html.append($(href).clone());

                    $this.transitionContent('inline', $currentContent, $html);
                } else {
                    $this.liteboxError();
                }

                $loader.remove();
            } else {
                var $iframe = $('<iframe>', {
                    'src': href,
                    'frameborder': '0',
                    'vspace': '0',
                    'hspace': '0',
                    'scrolling': 'auto',
                    'class': 'litebox-content',
                    'allowfullscreen': ''
                });

                $this.transitionContent('iframe', $currentContent, $iframe);

                $iframe.load(function () {
                    $loader.remove();
                });
            }
        },

        transitionContent: function (type, $currentContent, $newContent) {
            // Variables
            var $this = this;

            if (type != 'inline')
                $container.removeClass('litebox-scroll');

            // Transition
            $currentContent.remove();
            $container.append($newContent);

            if (type == 'inline')
                $container.addClass('litebox-scroll');

            $this.centerContent();

            $(window).on('resize', function () {
                $this.centerContent();
            });
        },

        centerContent: function () {
            // Overlay to viewport
            $litebox.css({'height': winHeight()});

            // Images
            $container.css({'line-height': $container.height() + 'px'});

            // Inline
            if (typeof $html != 'undefined' && $('.litebox-inline-html').outerHeight() < $container.height())
                $('.litebox-inline-html').css({
                    'margin-top': '-' + ($('.litebox-inline-html').outerHeight()) / 2 + 'px',
                    'top': '50%'
                });
        },

        closeLitebox: function () {
            // Before callback
            this.options.callbackBeforeClose.call(this);

            if (this.timeout) {
                clearTimeout(this.timeout);
                this.timeout = null;
            }

            // Remove
            $litebox.fadeOut(this.options.revealSpeed, function () {
                $('.litebox-nav').hide();
                $litebox.empty().remove();
                $('.litebox-preload').remove();
            });

            $('.tipsy').fadeOut(this.options.revealSpeed, function () {
                $(this).remove();
            });

            // Remove click handlers
            $('.litebox-prev').off('click');
            $('.litebox-next').off('click');

            $('body').off('.litebox');

            // After callback
            this.options.callbackAfterClose.call(this);
        },

        liteboxError: function () {
            this.options.callbackError.call(this);

            $container.append($error);
        }
    };

    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, pluginName))
                $.data(this, pluginName, new liteBox(this, options));
        });
    };

})(n2, window, document);