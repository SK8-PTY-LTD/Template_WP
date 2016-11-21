(function ($) {
	'use strict';

	var pingraphy = {
		
		initReady: function() {
			this.menuAnimate();
			this.searchAnimate();
			this.homepage();
			this.mobileMenu();
			this.scrollTop();

		},
		menuAnimate: function() {
			var self = this;

			$('.section-one .toggle-mobile-menu').on('click', function(e) {
				if($('.section-two .toggle-mobile-menu').hasClass('active')) {
					$('.section-two .toggle-mobile-menu').removeClass('active');
					$('.section-two .toggle-mobile-menu').next('.second-navigation').removeClass('main-nav-open');
				}
				e.preventDefault();
				e.stopPropagation();
				if($(this).hasClass('active')) {
					$(this).removeClass('active');
					$(this).next('.main-navigation').removeClass('main-nav-open');
				} else {
					$(this).addClass('active');
					$(this).next('.main-navigation').addClass('main-nav-open');
				}
			});

			$('.section-two .toggle-mobile-menu').on('click', function(e) {
				if($('.section-one .toggle-mobile-menu').hasClass('active')) {
					$('.section-one .toggle-mobile-menu').removeClass('active');
					$('.section-one .toggle-mobile-menu').next('.main-navigation').removeClass('main-nav-open');
				}
				if($(this).hasClass('active')) {
					$(this).removeClass('active');
					$(this).next().removeClass('main-nav-open');
				} else {
					$(this).addClass('active');
					$(this).next().addClass('main-nav-open');
				}
			});

			$(document).click(function(e) {
				if($('.main-navigation').hasClass('main-nav-open')) {
					e.stopPropagation();
					$('.main-navigation').removeClass('main-nav-open');
				}
			});

			var catcher = $('#catcher'),
				sticky  = $('#sticky'),
				bodyTop = $('body').offset().top;

			if ( sticky.length ) {
				$(window).scroll(function() {
					pingraphy.stickThatMenu(sticky,catcher,bodyTop);
				});
				$(window).resize(function() {
					pingraphy.stickThatMenu(sticky,catcher,bodyTop);
				});
			}
		},
		isScrolledTo: function(elem,top) {
			var docViewTop = $(window).scrollTop(); //num of pixels hidden above current screen
			var docViewBottom = docViewTop + $(window).height();

			var elemTop = $(elem).offset().top - top; //num of pixels above the elem
			var elemBottom = elemTop + $(elem).height();

			return ((elemTop <= docViewTop));
		},
		stickThatMenu: function(sticky,catcher,top) {
			var self = this;

			if(self.isScrolledTo(sticky,top)) {
				sticky.addClass('sticky-nav');
				catcher.height(sticky.height());
			} 
			var stopHeight = catcher.offset().top;
			if ( stopHeight > sticky.offset().top) {
				sticky.removeClass('sticky-nav');
				catcher.height(0);
			}
		},
		searchAnimate: function() {
			var header = $('.site-header');
			var trigger = $('#trigger-overlay');
			var overlay = header.find('.overlay');
			var input = header.find('.hideinput, .header-search .fa-search');
			trigger.click(function(e){
				$(this).hide();
				overlay.addClass('open').find('input').focus();
			});

			$('.overlay-close').click(function(e) {
				$('.site-header .overlay').addClass('closed').removeClass('open');
				setTimeout(function() { $('.site-header .overlay').removeClass('closed'); }, 400);
				$('#trigger-overlay').show();
			});

			$(document).on('click', function(e) {
				var target = $(e.target);
				if (target.is('.overlay') || target.closest('.overlay').length) return true;

				$('.site-header .overlay').addClass('closed').removeClass('open');
				setTimeout(function() { $('.site-header .overlay').removeClass('closed'); }, 400);
				$('#trigger-overlay').show();
			});

		    $('#trigger-overlay').click(function(e) {
		        e.preventDefault();
		        e.stopPropagation();
		    });
		},
		homepage: function() {
			var $container = $('#masonry-container');
			$container.imagesLoaded(function() {
				$('.masonry').isotope({
					itemSelector: '.item',
					masonry: {
						columnWidth: '.item',
						gutter: 30,
						isFitWidth: true,
						animate: true,
						animationOptions: {
							duration: 700,
							queue: true
						}
					}
				});
			});
		},
		mobileMenu: function() {
			$('#masthead .menu-item-has-children > a').append('<i class="fa arrow-sub-menu fa-chevron-right"></i>');
			$('.arrow-sub-menu').on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();

				var active = $(this).hasClass('fa-chevron-down');
				if(!active) {
					$(this).parent().next().css({'display' : 'block'});
					$(this).removeClass('fa-chevron-right').addClass('fa-chevron-down');
				} else {
					$(this).parent().next().css({'display' : 'none'});
					$(this).removeClass('fa-chevron-down').addClass('fa-chevron-right');
				}
				
			});
		},
		
		scrollTop: function() {
			
			var scrollDes = 'html,body';  
			// Opera does a strange thing if we use 'html' and 'body' together so my solution is to do the UA sniffing thing
			if(navigator.userAgent.match(/opera/i)){
				scrollDes = 'html';
			}
			// Show ,Hide
			$(window).scroll(function () {
				if ($(this).scrollTop() > 130) {
					$('.back-to-top').addClass('filling').removeClass('hiding');
					//$('.sharing-top-float').fadeIn();
				} else {
					$('.back-to-top').removeClass('filling').addClass('hiding');
					//$('.sharing-top-float').fadeOut();
				}
			});
			// Scroll to top when click
			$('.back-to-top').click(function () {
				$(scrollDes).animate({ 
					scrollTop: 0
				},{
					duration :500
				});

			});
		},
	};

	$(document).ready(function () {
		pingraphy.initReady();
	});

})(jQuery);