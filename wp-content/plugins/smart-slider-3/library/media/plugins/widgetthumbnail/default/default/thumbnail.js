(function ($, scope, undefined) {
    "use strict";
    function NextendSmartSliderWidgetThumbnailDefault(id, parameters) {

        this.slider = window[id];

        this.slider.started($.proxy(this.start, this, id, parameters));
    };


    NextendSmartSliderWidgetThumbnailDefault.prototype.start = function (id, parameters) {

        if (this.slider.sliderElement.data('thumbnail')) {
            return false;
        }
        this.slider.sliderElement.data('thumbnail', this);

        this.parameters = $.extend({captionSize: 0, minimumThumbnailCount: 1.5}, parameters);

        this.ratio = 1;
        this.hidden = false;
        this.forceHidden = false;
        this.forceHiddenCB = null;
        this.group = 2;
        this.itemPerPane = 1;
        this.currentI = 0;
        this.offset = 0;
        this.horizontal = {
            prop: 'width',
            Prop: 'Width',
            sideProp: nextend.rtl.left,
            invProp: 'height'
        };
        this.vertical = {
            prop: 'height',
            Prop: 'Height',
            sideProp: 'top',
            invProp: 'width'
        };

        this.group = parseInt(parameters.group);
        this.orientation = parameters.orientation;
        if (this.orientation == 'vertical') {
            this.goToDot = this._goToDot;
        }

        this.outerBar = this.slider.sliderElement.find('.nextend-thumbnail-default');
        this.bar = this.outerBar.find('.nextend-thumbnail-inner');
        this.scroller = this.bar.find('.nextend-thumbnail-scroller');

        var event = 'universalclick';
        if (parameters.action == 'mouseenter') {
            event = 'mouseenter';
        }
        this.dots = this.scroller.find('td > div').on(event, $.proxy(this.onDotClick, this));
        this.images = this.dots.find('.n2-ss-thumb-image');

        if (!nextend.rtl.isRtl) {
            this.previous = this.outerBar.find('.nextend-thumbnail-previous').on('click', $.proxy(this.previousPane, this));
            this.next = this.outerBar.find('.nextend-thumbnail-next').on('click', $.proxy(this.nextPane, this));
        } else {
            this.previous = this.outerBar.find('.nextend-thumbnail-next').on('click', $.proxy(this.previousPane, this));
            this.next = this.outerBar.find('.nextend-thumbnail-previous').on('click', $.proxy(this.nextPane, this));
        }

        if (this.orientation == 'horizontal' && this.group > 1) {
            var dots = [],
                group = this.group;
            this.scroller.find('tr').each(function (i, tr) {
                $(tr).find('td > div').each(function (j, div) {
                    dots[i + j * group] = div;
                });
            });
            this.dots = $(dots);
        }

        if (typeof this.slider.shuffled !== 'undefined') {
            var _temp = [],
                parents = {};
            for (var i = 0; i < this.slider.shuffled.length; i++) {
                var $dot = this.dots.eq(this.slider.shuffled[i]),
                    dot = $dot.get(0);
                _temp.push(dot);
                parents[this.slider.shuffled[i]] = $dot.parent();
                if (typeof parents[i] !== 'undefined') {
                    $dot.appendTo(parents[i]);
                } else {
                    $dot.appendTo(this.dots.eq(i).parent());
                }
            }
            this.dots = $(_temp);
        }


        this.scrollerDimension = {
            width: this.scroller.width(),
            height: this.scroller.width()
        };
        this.thumbnailDimension = {
            width: this.dots.outerWidth(true),
            height: this.dots.outerHeight(true)
        };

        this.thumbnailDimension.widthMargin = this.thumbnailDimension.width - this.dots.outerWidth();
        this.thumbnailDimension.heightMargin = this.thumbnailDimension.height - this.dots.outerHeight();

        this.imageDimension = {
            width: this.images.outerWidth(true),
            height: this.images.outerHeight(true)
        };

        this.sideDimension = this.thumbnailDimension[this[this.orientation].prop] * 0.25;

        if (this.orientation == 'horizontal') {
            this.scroller.height(this.thumbnailDimension.height * this.group);
            this.bar.height(this.scroller.outerHeight(true));
        } else {
            this.scroller.width(this.thumbnailDimension.width * this.group);
            this.bar.width(this.scroller.outerWidth(true));
        }
        //this.onSliderResize();

        this.slider.sliderElement
            .on('BeforeVisible', $.proxy(this.onReady, this))
            .on('sliderSwitchTo', $.proxy(this.onSlideSwitch, this));

        this.onSlideSwitch(null, this.slider.currentSlideIndex, this.slider.getRealIndex(this.slider.currentSlideIndex));

        if (parameters.overlay == 0) {
            var side = false;
            switch (parameters.area) {
                case 1:
                    side = 'Top';
                    break;
                case 12:
                    side = 'Bottom';
                    break;
                case 5:
                    side = 'Left';
                    break;
                case 8:
                    side = 'Right';
                    break;
            }
            if (side) {
                this.offset = parseFloat(this.outerBar.data('offset'));
                this.slider.responsive.addStaticMargin(side, this);
            }
        }
    };

    NextendSmartSliderWidgetThumbnailDefault.prototype.onReady = function () {
        this.slider.sliderElement.on('SliderResize', $.proxy(this.onSliderResize, this));
        this.onSliderResize();
    };


    NextendSmartSliderWidgetThumbnailDefault.prototype.onSliderResize = function () {
        if (this.forceHiddenCB !== null) {
            this.forceHiddenCB.call(this);
        }
        this.adjustScrollerSize();

        this.goToDot(this.dots.index(this.dots.filter('.n2-active')));
    };

    NextendSmartSliderWidgetThumbnailDefault.prototype.adjustScrollerSize = function () {
        var prop = this[this.orientation].prop,
            size = Math.ceil(this.dots.length / this.group) * this.thumbnailDimension[prop] * this.ratio,
            diff = this.scroller['outer' + this[this.orientation].Prop]() - this.scroller[prop](),
            barDimension = this.slider.dimensions['thumbnail' + prop];
        if (size + diff <= barDimension) {
            this.scroller[prop](barDimension - diff);
        } else {
            this.scroller[prop](size);
        }

    };

    NextendSmartSliderWidgetThumbnailDefault.prototype.onDotClick = function (e) {
        this.slider.directionalChangeToReal(this.dots.index(e.currentTarget));
    };

    NextendSmartSliderWidgetThumbnailDefault.prototype.onSlideSwitch = function (e, targetSlideIndex, realTargetSlideIndex) {
        this.dots.filter('.n2-active').removeClass('n2-active');
        this.dots.eq(realTargetSlideIndex).addClass('n2-active');

        this.goToDot(realTargetSlideIndex);

    };

    NextendSmartSliderWidgetThumbnailDefault.prototype.previousPane = function () {
        this.goToDot(this.currentI - this.itemPerPane);
    };

    NextendSmartSliderWidgetThumbnailDefault.prototype.nextPane = function () {
        this.goToDot(this.currentI + this.itemPerPane);
    };

    NextendSmartSliderWidgetThumbnailDefault.prototype.goToDot = function (i) {

        var variables = this[this.orientation],
            ratio = 1,
            barDimension = this.slider.dimensions['thumbnail' + variables.prop],
            sideDimension = this.sideDimension,
            availableBarDimension = barDimension - sideDimension * 2,
            itemPerPane = availableBarDimension / this.thumbnailDimension[variables.prop];
        if (itemPerPane <= this.parameters.minimumThumbnailCount) {
            sideDimension = barDimension * 0.1;
            availableBarDimension = barDimension - sideDimension * 2;
            ratio = availableBarDimension / (this.parameters.minimumThumbnailCount * this.thumbnailDimension[variables.prop]);
            itemPerPane = availableBarDimension / (this.thumbnailDimension[variables.prop] * ratio);
        }

        if (this.ratio != ratio) {
            var css = {};
            css[variables.prop] = parseInt(this.thumbnailDimension[variables.prop] * ratio - this.thumbnailDimension[variables.prop + 'Margin']);
            var scrollerDimension = css[variables.invProp] = parseInt((this.thumbnailDimension[variables.invProp] - this.parameters['captionSize']) * ratio + this.parameters['captionSize']);
            this.dots.css(css);
            css = {};
            css[variables.prop] = parseInt(this.imageDimension[variables.prop] * ratio - this.thumbnailDimension[variables.prop + 'Margin']);
            css[variables.invProp] = parseInt(this.imageDimension[variables.invProp] * ratio);
            this.images.css(css);

            this.scroller.css(variables.invProp, 'auto');
            this.bar.css(variables.invProp, 'auto');
            this.ratio = ratio;
            this.slider.responsive.doNormalizedResize();

            this.adjustScrollerSize();
        }

        itemPerPane = Math.floor(itemPerPane);
        i = Math.max(0, Math.min(this.dots.length - 1, i));
        var currentPane = Math.floor(i / this.group / itemPerPane),
            to = {};

        var min = -(this.scroller['outer' + variables.Prop]() - barDimension);

        if (currentPane == Math.floor((this.dots.length - 1) / this.group / itemPerPane)) {
            to[variables.sideProp] = -(currentPane * itemPerPane * this.thumbnailDimension[variables.prop] * ratio);
            if (currentPane == 0) {
                this.previous.removeClass('n2-active');
            } else {
                this.previous.addClass('n2-active');
            }
            this.next.removeClass('n2-active');
        } else if (currentPane > 0) {
            to[variables.sideProp] = -(currentPane * itemPerPane * this.thumbnailDimension[variables.prop] * ratio - sideDimension);
            this.previous.addClass('n2-active');
            this.next.addClass('n2-active');
        } else {
            to[variables.sideProp] = 0;
            this.previous.removeClass('n2-active');
            this.next.addClass('n2-active');
        }
        if (min >= to[variables.sideProp]) {
            to[variables.sideProp] = min;
            this.next.removeClass('n2-active');
        }
        NextendTween.to(this.scroller, 0.5, to).play();


        this.currentI = i;
        this.itemPerPane = itemPerPane;

    };

    NextendSmartSliderWidgetThumbnailDefault.prototype._goToDot = function (i) {
        if (this.forceHidden) {
            return;
        }
        var variables = this[this.orientation];
        var barDimension = this.slider.dimensions['thumbnail' + variables.prop];


        var itemPerPane = (barDimension - this.sideDimension * 2) / this.thumbnailDimension[variables.prop];
        if (barDimension != 0 && itemPerPane < this.parameters.minimumThumbnailCount - 0.5) {
            if (!this.hidden) {
                if (this.orientation == 'horizontal') {
                    this.outerBar.css('height', 0);
                } else {
                    this.outerBar.css('width', 0);
                }
                this.hidden = true;
                this.forceHidden = true;
                setTimeout($.proxy(function () {
                    this.forceHiddenCB = function () {
                        this.forceHiddenCB = null;
                        this.forceHidden = false;
                    };
                }, this), 300);
                this.slider.responsive.doNormalizedResize();
            }
        } else if (this.hidden) {
            if (itemPerPane >= this.parameters.minimumThumbnailCount + 0.5) {
                this.hidden = false;
                if (this.orientation == 'horizontal') {
                    this.outerBar.css('height', '');
                } else {
                    this.outerBar.css('width', '');
                }
                this.slider.responsive.doNormalizedResize();
            }
        }

        if (!this.hidden) {
            itemPerPane = Math.floor(itemPerPane);
            i = Math.max(0, Math.min(this.dots.length - 1, i));
            var currentPane = Math.floor(i / this.group / itemPerPane),
                to = {};

            var min = -(this.scroller['outer' + variables.Prop]() - barDimension);

            if (currentPane == Math.floor((this.dots.length - 1) / this.group / itemPerPane)) {
                to[variables.sideProp] = -(currentPane * itemPerPane * this.thumbnailDimension[variables.prop]);
                if (currentPane == 0) {
                    this.previous.removeClass('n2-active');
                } else {
                    this.previous.addClass('n2-active');
                }
                this.next.removeClass('n2-active');
            } else if (currentPane > 0) {
                to[variables.sideProp] = -(currentPane * itemPerPane * this.thumbnailDimension[variables.prop] - this.sideDimension);
                this.previous.addClass('n2-active');
                this.next.addClass('n2-active');
            } else {
                to[variables.sideProp] = 0;
                this.previous.removeClass('n2-active');
                this.next.addClass('n2-active');
            }
            if (min >= to[variables.sideProp]) {
                to[variables.sideProp] = min;
                this.next.removeClass('n2-active');
            }
            NextendTween.to(this.scroller, 0.5, to).play();
        }


        this.currentI = i;
        this.itemPerPane = itemPerPane;
    };

    NextendSmartSliderWidgetThumbnailDefault.prototype.isVisible = function () {
        return this.outerBar.is(':visible');
    };

    NextendSmartSliderWidgetThumbnailDefault.prototype.getSize = function () {
        if (this.orientation == 'horizontal') {
            return this.outerBar.height() + this.offset;
        }
        return this.outerBar.width() + this.offset;
    };

    scope.NextendSmartSliderWidgetThumbnailDefault = NextendSmartSliderWidgetThumbnailDefault;

})(n2, window);