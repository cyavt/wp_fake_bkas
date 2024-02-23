(function($){
	"use strict";
	

	$(window).on('elementor/frontend/init', function () {


		/* Search */

		$(".ovabrw-search .ovabrw-search-form").each(function(){
			var that = $(this);
			var guestspicker = that.find('.ovabrw-guestspicker');
			var guestspicker_control = $(this).find('.guestspicker-control')

			guestspicker.on('click', function() {
				var guestspicker_control = $(this).closest('.guestspicker-control').toggleClass('active');
			});

			$(window).click( function(e) {
				var guestspicker_content = $('.ovabrw-guestspicker-content');
        		if ( !guestspicker.is(e.target) && guestspicker.has(e.target).length === 0 && !guestspicker_content.is(e.target) && guestspicker_content.has(e.target).length === 0 ) {
        			guestspicker_control.removeClass('active');
        		}
			});

			var minus = that.find('.minus');
			minus.on('click', function() {
				gueststotal($(this), 'sub');
			});

			var plus = that.find('.plus');
			plus.on('click', function() {
				gueststotal($(this), 'sum');
			});

			// select 2
	        $('#brw-destinations-select-box, #brw_custom_taxonomy_dropdown').select2({ 
	        	width: '100%',
	        });

	    });

	    function gueststotal( that, cal ) {
	    	var guests_button = that.closest('.guests-button');
			var input 	= guests_button.find('input[type="text"]');
			var value 	= input.val();
			var min 	= input.attr('min');
			var max 	= input.attr('max');

			if ( cal == 'sub' && parseInt(value) > parseInt(min) ) {
				input.val(parseInt(value) - 1);
			}

			if ( cal == 'sum' && parseInt(value) < parseInt(max) ) {
				input.val(parseInt(value) + 1);
			}

			var guestspicker_control = that.closest('.guestspicker-control');
			var adults = guestspicker_control.find('.ovabrw_adults').val();
			var children = guestspicker_control.find('.ovabrw_childrens').val();
			var gueststotal = guestspicker_control.find('.gueststotal');
			if ( gueststotal ) {
				gueststotal.text( parseInt(adults) + parseInt(children) );
			}
	    }
		

		/* Search Ajax */
		$(".ovabrw-search-ajax .wrap-search-ajax").each(function(){

			// price filter slider
			var price_wrap = $(this).find('.brw-tour-price-input');
			var price_from = $(this).find('.brw-tour-price-from');
			var price_to   = $(this).find('.brw-tour-price-to');
			var min        = price_from.data('value') ? price_from.data('value') : 0 ;
			var max        = price_to.data('value') ? price_to.data('value') : 500 ;
			var symbol     = price_wrap.data('currency_symbol');
			
            $("#brw-tour-price-slider").slider({
	            range: true,
			    min: min,
			    max: max,
			    values: [ min, max ],
			    slide: function( event, ui ) {
			        $( ".brw-tour-price-from" ).val(ui.values[0]);
			        $( ".brw-tour-price-to" ).val(ui.values[1] );
			    }
			});

			$(".brw-tour-price-from").change(function () {
			    var value = $(this).val();
			    $("#brw-tour-price-slider").slider("values", 0, value);
			});

			$(".brw-tour-price-to").change(function () {
			    var value = $(this).val();
			    $("#brw-tour-price-slider").slider("values", 1, value);
			});  
            
            // Advanded search toggle
			function advanced_search_toggle(){

				var btn = $('.ovabrw-search-advanced .search-advanced-input');

				btn.on('click', function () {
		        	$(this).closest('.ovabrw-search-advanced').find('.search-advanced-field-wrapper').toggleClass('toggled');
		        	$(this).toggleClass( 'active' );

	                // change icon
		        	if ( $(this).hasClass('active') ) {
		        		$(this).find('i').removeClass('icomoon-chevron-down');
		        		$(this).find('i').addClass('icomoon-chevron-up');
		        	} else {
		        		$(this).find('i').removeClass('icomoon-chevron-up');
		        		$(this).find('i').addClass('icomoon-chevron-down');
		        	}
		        });
		    } 

		    advanced_search_toggle();

		    // Sort by filer dropdown
			function sort_by_filter_dropdown(){

				var sort_by  		      = $('.ovabrw-tour-filter .input_select_input');
				var sort_by_value  	 	  = $('.ovabrw-tour-filter .input_select_input_value');
				var term_item 		      = $('.ovabrw-tour-filter .input_select_list .term_item');
				var sort_by_text_default  = $('.ovabrw-tour-filter .input_select_list .term_item_selected').data('value');
				var sort_by_value_default = $('.ovabrw-tour-filter .input_select_list .term_item_selected').data('id');	
                
                sort_by.attr('value',sort_by_text_default);
				sort_by_value.attr('value',sort_by_value_default);

				sort_by.on('click', function () {
		        	$(this).closest('.filter-sort').find('.input_select_list').show();
		        	$(this).toggleClass( 'active' );
		        });

		        term_item.on('click', function () {
		        	$(this).closest('.ovabrw-tour-filter').find('.input_select_list').hide();

		        	// change term item selected
		        	var item_active   = $('.ovabrw-tour-filter .input_select_list .term_item_selected').data('id');
		            var item          = $(this).data('id');
		            if ( item != item_active ) {
		                term_item.removeClass('term_item_selected');
		                $(this).addClass('term_item_selected');
		            }

		            // get value, id sort by
		            var sort_value = $(this).data('id');
		            var sort_label = $(this).data('value');

	                // change input select text
		        	sort_by.val(sort_label);
		        	// change input value
		        	sort_by_value.val(sort_value);
		        });
		    } 

		    sort_by_filter_dropdown();    

	    });
        
        // Search Ajax Sidebar
	    $(".ovabrw-search-ajax-sidebar .wrap-search-ajax-sidebar").each(function(){

	    	var search_title = $(this).find('.search-title');
	    	search_title.on('click', function () {
	        	$(this).closest('.ovabrw-search').find('.ovabrw-search-form .ovabrw-s-field').toggle();
	        	$(this).toggleClass( 'unactive' );
	        	$(this).closest('.ovabrw-search').find('.ovabrw-search-form').toggleClass( 'unborder' );

                // change icon
	        	if ( $(this).hasClass('unactive') ) {
	        		$(this).find('i').removeClass('icomoon-chevron-up');
	        		$(this).find('i').addClass('icomoon-chevron-down');
	        	} else {
	        		$(this).find('i').removeClass('icomoon-chevron-down');
	        		$(this).find('i').addClass('icomoon-chevron-up');
	        	}
	        });
            
            // Advanded search part toggled
			function advanced_search_part_toggle(){

				var btn = $('.ovabrw-search-advanced-sidebar .ovabrw-label');

				btn.on('click', function () {
		        	$(this).closest('.search-advanced-field').find('.search-advanced-content').toggleClass('toggled');
		        	$(this).toggleClass( 'unactive' );

	                // change icon
		        	if ( $(this).hasClass('unactive') ) {
		        		$(this).find('i').removeClass('icomoon-chevron-up');
		        		$(this).find('i').addClass('icomoon-chevron-down');
		        	} else {
		        		$(this).find('i').removeClass('icomoon-chevron-down');
		        		$(this).find('i').addClass('icomoon-chevron-up');
		        	}
		        });
		    } 

		    advanced_search_part_toggle();   

	    });
		    

		/* Product categories */ 
		elementorFrontend.hooks.addAction('frontend/element_ready/ovabrw_product_categories.default', function(){

			$(".ova_product_categories").each(function(){
		        var owlsl     = $(this) ;
		        var owlsl_ops = owlsl.data('options') ? owlsl.data('options') : {};

		        var responsive_value = {
		            0:{
		              items:1,
		              nav:false,
		              dots: true,
		            },
		            479:{
		              items:2,
		              margin: 20,
					  nav:false,
		              dots: true,
		            },
		            767:{
		              items:2,
		              margin: 20,
		            },

		            1024:{
		            	items:3
		            },

		            1300:{
		              items:owlsl_ops.items
		            }
		        };
		        
		        owlsl.owlCarousel({
		            margin: owlsl_ops.margin,
		            items: owlsl_ops.items,
		            loop: owlsl_ops.loop,
		            autoplay: owlsl_ops.autoplay,
		            autoplayTimeout: owlsl_ops.autoplayTimeout,
		            nav: owlsl_ops.nav,
		            dots: owlsl_ops.dots,
		            autoplayHoverPause: owlsl_ops.autoplayHoverPause,
		            slideBy: owlsl_ops.slideBy,
		            smartSpeed: owlsl_ops.smartSpeed,
		            rtl: owlsl_ops.rtl,
		            navText:[
		               '<i class="icomoon icomoon-pre-small"></i>',
		               '<i class="icomoon icomoon-next-small"></i>'
		            ],
		          responsive: responsive_value,
		        });

		      	/* Fixed WCAG */
				owlsl.find(".owl-nav button.owl-prev").attr("title", "Previous");
				owlsl.find(".owl-nav button.owl-next").attr("title", "Next");
				owlsl.find(".owl-dots button").attr("title", "Dots");

		      });
		});


		/* Product slider */ 
		elementorFrontend.hooks.addAction('frontend/element_ready/ovabrw_product_slider.default', function(){

			$(".ova-product-slider").each(function(){
		        var owlsl      = $(this) ;
		        var owlsl_ops  = owlsl.data('options') ? owlsl.data('options') : {};

		        var responsive_value = {
		            0:{
                        items:1,
                    },
                    576:{
                        items:1,
                    },
                    767: {
                    	items:2,
                    },
                    960:{
                        items:owlsl_ops.items - 1,
                    },
                    1200:{
                        items:owlsl_ops.items
                    }
		        };
		        
		        owlsl.owlCarousel({
		            margin: owlsl_ops.margin,
		            items: owlsl_ops.items,
		            loop: owlsl_ops.loop,
		            autoplay: owlsl_ops.autoplay,
		            autoplayTimeout: owlsl_ops.autoplayTimeout,
		            nav: owlsl_ops.nav,
		            dots: true,
		            autoplayHoverPause: owlsl_ops.autoplayHoverPause,
		            slideBy: owlsl_ops.slideBy,
		            smartSpeed: owlsl_ops.smartSpeed,
		            rtl: owlsl_ops.rtl,
		            navText:[
			            '<i class="icomoon icomoon-pre-small"></i>',
			            '<i class="icomoon icomoon-next-small"></i>'
		            ],
		            responsive: responsive_value,
		        });

		      	/* Fixed WCAG */
				owlsl.find(".owl-nav button.owl-prev").attr("title", "Previous");
				owlsl.find(".owl-nav button.owl-next").attr("title", "Next");
				owlsl.find(".owl-dots button").attr("title", "Dots");

		      });
		});

		/* Gallery Slideshow */
		elementorFrontend.hooks.addAction('frontend/element_ready/ovabrw_product_images.default', function(){

			$('.elementor-product-image .ova-gallery-slideshow').each( function() {
				var that 	= $(this);
				var options = that.data('options') ? that.data('options') : {};

				var responsive_value = {
		            0:{
		                items:1,
		                nav:false,
		          		slideBy: 1,
		            },
		            768:{
		              	items: 2,
		              	slideBy: 1,
		            },
		            1025:{
		              	items: 3,
		              	slideBy: 1,
		            },
		            1300:{
		              	items: options.items,
		            }
		        };
		        
		        that.owlCarousel({
		        	autoWidth: options.autoWidth,
					margin: options.margin,
					items: options.items,
					loop: options.loop,
					autoplay: options.autoplay,
					autoplayTimeout: options.autoplayTimeout,
					center: options.center,
					lazyLoad: options.lazyLoad,
					nav: options.nav,
					dots: options.dots,
					autoplayHoverPause: options.autoplayHoverPause,
					slideBy: options.slideBy,
					smartSpeed: options.smartSpeed,
					rtl: options.rtl,
					navText:[
			          	'<i aria-hidden="true" class="'+ options.nav_left +'"></i>',
			          	'<i aria-hidden="true" class="'+ options.nav_right +'"></i>'
			        ],
					responsive: responsive_value,
		        });

		        that.find('.gallery-fancybox').off('click').on('click', function() {
					var index = $(this).data('index');
					var gallery_data = $(this).closest('.ova-gallery-popup').find('.ova-data-gallery').data('gallery');

					Fancybox.show(gallery_data, {
		            	Image: {
						    Panzoom: {
						      	zoomFriction: 0.7,
						      	maxScale: function () {
						        	return 3;
						      	},
						    },
					  	},
					  	startIndex: index,
					});
				});
			});
		});

		/* Product Forms */
		elementorFrontend.hooks.addAction('frontend/element_ready/ovabrw_product_forms.default', function(){
            /* Forms */
			$('.ova-forms-product').each( function() {
				var that = $(this);
				var item = that.find('.tabs .item');

				if ( item.length > 0 ) {
					item.each( function( index ) {
					  	if ( index == 0 ) {
					  		$(this).addClass('active');
					  		var id = $(this).data('id');
					  		$(id).show();
					  	}
					});
				}

				item.on('click', function() {
					item.removeClass('active');
					$(this).addClass('active');
					var id = $(this).data('id');

					if ( id == '#booking-form' ) {
						that.find('#request-form').hide();
					}

					if ( id == '#request-form' ) {
						that.find('#booking-form').hide();
					}
					
					$(id).show();
				});
			});
		});

		/* Product Tabs */
		elementorFrontend.hooks.addAction('frontend/element_ready/ovabrw_product_tabs.default', function(){

			/* Tabs */
			$('.ova-tabs-product').each( function() {
				var that = $(this);
				var item = that.find('.tabs .item');

				if ( item.length > 0 ) {
					item.each( function( index ) {
					  	if ( index == 0 ) {
					  		$(this).addClass('active');
					  		var id = $(this).data('id');
					  		$(id).show();
					  	}
					});
				}

				item.on('click', function() {
					item.removeClass('active');
					$(this).addClass('active');
					var id = $(this).data('id');

					if ( id == '#tour-description' ) {
						that.find('#tour-included-excluded, #tour-plan, #ova-tour-map, #ova-tour-review ').hide();
					}

					if ( id == '#tour-included-excluded' ) {
						that.find('#tour-description, #tour-plan, #ova-tour-map, #ova-tour-review ').hide();
					}

					if ( id == '#tour-plan' ) {
						that.find('#tour-included-excluded, #tour-description, #ova-tour-map, #ova-tour-review ').hide();
					}

					if ( id == '#ova-tour-map' ) {
						that.find('#tour-included-excluded, #tour-plan, #tour-description, #ova-tour-review ').hide();
					}

					if ( id == '#ova-tour-review' ) {
						that.find('#tour-included-excluded, #tour-plan, #ova-tour-map, #tour-description ').hide();
					}
					
					$(id).show();
				});
			});

			$('.item-tour-plan').each( function() {

				var that = $(this);
				var item = that.find('.tour-plan-title');

				item.on('click', function() {
					$(this).closest('.item-tour-plan').toggleClass('active');
					// change icon
		        	if ( that.hasClass('active') ) {
		        		$(this).find('i').removeClass('icomoon-chevron-down');
		        		$(this).find('i').addClass('icomoon-chevron-up');
		        	} else {
		        		$(this).find('i').removeClass('icomoon-chevron-up');
		        		$(this).find('i').addClass('icomoon-chevron-down');
		        	}
				});
			});

		});

		/* related slide */
		elementorFrontend.hooks.addAction('frontend/element_ready/ovabrw_product_related.default', function(){

			$(".elementor-ralated-slide .elementor-ralated").each(function(){
		        var owlsl      = $(this) ;
		        var owlsl_ops  = owlsl.data('options') ? owlsl.data('options') : {};

		        var responsive_value = {
		            0:{
                        items:1,
                    },
                    576:{
                        items:1,
                    },
                    767: {
                    	items:2,
                    },
                    960:{
                        items:owlsl_ops.items - 1,
                    },
                    1200:{
                        items:owlsl_ops.items
                    }
		        };
		        
		        owlsl.owlCarousel({
		            margin: owlsl_ops.margin,
		            items: owlsl_ops.items,
		            loop: owlsl_ops.loop,
		            autoplay: owlsl_ops.autoplay,
		            autoplayTimeout: owlsl_ops.autoplayTimeout,
		            nav: owlsl_ops.nav,
		            dots: true,
		            autoplayHoverPause: owlsl_ops.autoplayHoverPause,
		            slideBy: owlsl_ops.slideBy,
		            smartSpeed: owlsl_ops.smartSpeed,
		            rtl: owlsl_ops.rtl,
		            navText:[
			            '<i class="icomoon icomoon-pre-small"></i>',
			            '<i class="icomoon icomoon-next-small"></i>'
		            ],
		            responsive: responsive_value,
		        });

		      	/* Fixed WCAG */
				owlsl.find(".owl-nav button.owl-prev").attr("title", "Previous");
				owlsl.find(".owl-nav button.owl-next").attr("title", "Next");
				owlsl.find(".owl-dots button").attr("title", "Dots");

		    });
		});

		/* Tour Plan Toggled */
		elementorFrontend.hooks.addAction('frontend/element_ready/ovabrw_product_plan.default', function(){

			$('.item-tour-plan').each( function() {

				var that = $(this);
				var item = that.find('.tour-plan-title');

				item.on('click', function() {
					$(this).closest('.item-tour-plan').toggleClass('active');
					// change icon
		        	if ( that.hasClass('active') ) {
		        		$(this).find('i').removeClass('icomoon-chevron-down');
		        		$(this).find('i').addClass('icomoon-chevron-up');
		        	} else {
		        		$(this).find('i').removeClass('icomoon-chevron-up');
		        		$(this).find('i').addClass('icomoon-chevron-down');
		        	}
				});

			});

		});

		/* Product Content - Tour Map JS */
		elementorFrontend.hooks.addAction('frontend/element_ready/ovabrw_product_map.default', function(){

			// Tour Map
			if ( $('.tripgo-tour-map').length > 0 ) {
				$('.tripgo-tour-map').each(function() {
					var that 		= $(this);
					var input 		= $('#pac-input')[0];
					var address 	= that.find('.address');
					var latitude 	= address.attr('latitude');
					var longitude 	= address.attr('longitude');

					
					if ( typeof google !== 'undefined' && latitude && longitude ) {
						var map = new google.maps.Map( $('#tour-show-map')[0], {
		                    center: {
		                        lat: parseFloat(latitude),
		                        lng: parseFloat(longitude)
		                    },
		                    zoom: 17,
		                    gestureHandling: 'cooperative',
		                });

		                var autocomplete = new google.maps.places.Autocomplete(input);

			            autocomplete.bindTo('bounds', map);

			            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

			            var mapIWcontent = $('#pac-input').val();
			            var infowindow = new google.maps.InfoWindow({
			               content: mapIWcontent,
			            });

			            var marker = new google.maps.Marker({
		                   map: map,
		                   position: map.getCenter(),
		                });

		                marker.addListener('click', function() {
		                   infowindow.open(map, marker);
		                });
					}

				});
			}

		});

	});


})(jQuery);
