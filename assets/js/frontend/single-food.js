/*global rp_single_food_params, PhotoSwipe, PhotoSwipeUI_Default */
jQuery( function( $ ) {

	// rp_single_food_params is required to continue.
	if ( typeof rp_single_food_params === 'undefined' ) {
		return false;
	}

	$( 'body' )
		// Tabs
		.on( 'init', '.rp-tabs-wrapper, .restaurantpress-tabs', function() {
			$( '.rp-tab, .restaurantpress-tabs .panel:not(.panel .panel)' ).hide();

			var hash  = window.location.hash;
			var url   = window.location.href;
			var $tabs = $( this ).find( '.rp-tabs, ul.tabs' ).first();

			if ( hash.toLowerCase().indexOf( 'comment-' ) >= 0 || hash === '#reviews' || hash === '#tab-reviews' ) {
				$tabs.find( 'li.reviews_tab a' ).click();
			} else if ( url.indexOf( 'comment-page-' ) > 0 || url.indexOf( 'cpage=' ) > 0 ) {
				$tabs.find( 'li.reviews_tab a' ).click();
			} else if ( hash === '#tab-additional_information' ) {
				$tabs.find( 'li.additional_information_tab a' ).click();
			} else {
				$tabs.find( 'li:first a' ).click();
			}
		} )
		.on( 'click', '.rp-tabs li a, ul.tabs li a', function( e ) {
			e.preventDefault();
			var $tab          = $( this );
			var $tabs_wrapper = $tab.closest( '.rp-tabs-wrapper, .restaurantpress-tabs' );
			var $tabs         = $tabs_wrapper.find( '.rp-tabs, ul.tabs' );

			$tabs.find( 'li' ).removeClass( 'active' );
			$tabs_wrapper.find( '.rp-tab, .panel:not(.panel .panel)' ).hide();

			$tab.closest( 'li' ).addClass( 'active' );
			$tabs_wrapper.find( $tab.attr( 'href' ) ).show();
		} );

	// Init Tabs
	$( '.rp-tabs-wrapper, .restaurantpress-tabs' ).trigger( 'init' );

	/**
	 * Food gallery class.
	 */
	var FoodGallery = function( $target, args ) {
		this.$target = $target;
		this.$images = $( '.restaurantpress-food-gallery__image', $target );

		// No images? Abort.
		if ( 0 === this.$images.length ) {
			this.$target.css( 'opacity', 1 );
			return;
		}

		// Make this object available.
		$target.data( 'food_gallery', this );

		// Pick functionality to initialize...
		this.flexslider_enabled = $.isFunction( $.fn.flexslider ) && rp_single_food_params.flexslider_enabled;
		this.zoom_enabled       = $.isFunction( $.fn.zoom ) && rp_single_food_params.zoom_enabled;
		this.photoswipe_enabled = typeof PhotoSwipe !== 'undefined' && rp_single_food_params.photoswipe_enabled;

		// ...also taking args into account.
		if ( args ) {
			this.flexslider_enabled = false === args.flexslider_enabled ? false : this.flexslider_enabled;
			this.zoom_enabled       = false === args.zoom_enabled ? false : this.zoom_enabled;
			this.photoswipe_enabled = false === args.photoswipe_enabled ? false : this.photoswipe_enabled;
		}

		// Bind functions to this.
		this.initFlexslider       = this.initFlexslider.bind( this );
		this.initZoom             = this.initZoom.bind( this );
		this.initZoomForTarget    = this.initZoomForTarget.bind( this );
		this.initPhotoswipe       = this.initPhotoswipe.bind( this );
		this.onResetSlidePosition = this.onResetSlidePosition.bind( this );
		this.getGalleryItems      = this.getGalleryItems.bind( this );
		this.openPhotoswipe       = this.openPhotoswipe.bind( this );

		if ( this.flexslider_enabled ) {
			this.initFlexslider();
			$target.on( 'restaurantpress_gallery_reset_slide_position', this.onResetSlidePosition );
		} else {
			this.$target.css( 'opacity', 1 );
		}

		if ( this.zoom_enabled ) {
			this.initZoom();
			$target.on( 'restaurantpress_gallery_init_zoom', this.initZoom );
		}

		if ( this.photoswipe_enabled ) {
			this.initPhotoswipe();
		}
	};

	/**
	 * Initialize flexSlider.
	 */
	FoodGallery.prototype.initFlexslider = function() {
		var $target = this.$target,
			gallery = this;

		$target.flexslider( {
			selector:       '.restaurantpress-food-gallery__wrapper > .restaurantpress-food-gallery__image',
			animation:      rp_single_food_params.flexslider.animation,
			smoothHeight:   rp_single_food_params.flexslider.smoothHeight,
			directionNav:   rp_single_food_params.flexslider.directionNav,
			controlNav:     rp_single_food_params.flexslider.controlNav,
			slideshow:      rp_single_food_params.flexslider.slideshow,
			animationSpeed: rp_single_food_params.flexslider.animationSpeed,
			animationLoop:  rp_single_food_params.flexslider.animationLoop, // Breaks photoswipe pagination if true.
			allowOneSlide:  rp_single_food_params.flexslider.allowOneSlide,
			start: function() {
				$target.css( 'opacity', 1 );
			},
			after: function( slider ) {
				gallery.initZoomForTarget( gallery.$images.eq( slider.currentSlide ) );
			}
		} );

		// Trigger resize after main image loads to ensure correct gallery size.
		$( '.restaurantpress-food-gallery__wrapper .restaurantpress-food-gallery__image:eq(0) .wp-post-image' ).one( 'load', function() {
			var $image = $( this );

			if ( $image ) {
				setTimeout( function() {
					var setHeight = $image.closest( '.restaurantpress-food-gallery__image' ).height();
					var $viewport = $image.closest( '.flex-viewport' );

					if ( setHeight && $viewport ) {
						$viewport.height( setHeight );
					}
				}, 100 );
			}
		} ).each( function() {
			if ( this.complete ) {
				$( this ).load();
			}
		} );
	};

	/**
	 * Init zoom.
	 */
	FoodGallery.prototype.initZoom = function() {
		this.initZoomForTarget( this.$images.first() );
	};

	/**
	 * Init zoom.
	 */
	FoodGallery.prototype.initZoomForTarget = function( zoomTarget ) {
		if ( ! this.zoom_enabled ) {
			return false;
		}

		var galleryWidth = this.$target.width(),
			zoomEnabled  = false;

		$( zoomTarget ).each( function( index, target ) {
			var image = $( target ).find( 'img' );

			if ( image.data( 'large_image_width' ) > galleryWidth ) {
				zoomEnabled = true;
				return false;
			}
		} );

		// But only zoom if the img is larger than its container.
		if ( zoomEnabled ) {
			var zoom_options = {
				touch: false
			};

			if ( 'ontouchstart' in window ) {
				zoom_options.on = 'click';
			}

			zoomTarget.trigger( 'zoom.destroy' );
			zoomTarget.zoom( zoom_options );
		}
	};

	/**
	 * Init PhotoSwipe.
	 */
	FoodGallery.prototype.initPhotoswipe = function() {
		if ( this.zoom_enabled && this.$images.length > 0 ) {
			this.$target.prepend( '<a href="#" class="restaurantpress-food-gallery__trigger">🔍</a>' );
			this.$target.on( 'click', '.restaurantpress-food-gallery__trigger', this.openPhotoswipe );
		}
		this.$target.on( 'click', '.restaurantpress-food-gallery__image a', this.openPhotoswipe );
	};

	/**
	 * Reset slide position to 0.
	 */
	FoodGallery.prototype.onResetSlidePosition = function() {
		this.$target.flexslider( 0 );
	};

	/**
	 * Get product gallery image items.
	 */
	FoodGallery.prototype.getGalleryItems = function() {
		var $slides = this.$images,
			items   = [];

		if ( $slides.length > 0 ) {
			$slides.each( function( i, el ) {
				var img = $( el ).find( 'img' ),
					large_image_src = img.attr( 'data-large_image' ),
					large_image_w   = img.attr( 'data-large_image_width' ),
					large_image_h   = img.attr( 'data-large_image_height' ),
					item            = {
						src  : large_image_src,
						w    : large_image_w,
						h    : large_image_h,
						title: img.attr( 'data-caption' ) ? img.attr( 'data-caption' ) : img.attr( 'title' )
					};
				items.push( item );
			} );
		}

		return items;
	};

	/**
	 * Open photoswipe modal.
	 */
	FoodGallery.prototype.openPhotoswipe = function( e ) {
		e.preventDefault();

		var pswpElement = $( '.pswp' )[0],
			items       = this.getGalleryItems(),
			eventTarget = $( e.target ),
			clicked;

		if ( ! eventTarget.is( '.restaurantpress-food-gallery__trigger' ) ) {
			clicked = eventTarget.closest( '.restaurantpress-food-gallery__image' );
		} else {
			clicked = this.$target.find( '.flex-active-slide' );
		}

		var options = $.extend( {
			index: $( clicked ).index()
		}, rp_single_food_params.photoswipe_options );

		// Initializes and opens PhotoSwipe.
		var photoswipe = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options );
		photoswipe.init();
	};

	/**
	 * Function to call rp_food_gallery on jquery selector.
	 */
	$.fn.rp_food_gallery = function( args ) {
		new FoodGallery( this, args );
		return this;
	};

	/*
	 * Initialize all galleries on page.
	 */
	$( '.restaurantpress-food-gallery' ).each( function() {
		$( this ).rp_food_gallery();
	} );
} );
