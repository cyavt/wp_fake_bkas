(function($){
	"use strict";

	$(window).on('elementor/frontend/init', function () {

        /*  Our destination element */
        elementorFrontend.hooks.addAction('frontend/element_ready/ova_destination.default', function(){
            // Mansory
            $('.content-destination').each( function() { 
                var grid = $(this);
                var run  = grid.masonry({
                    itemSelector: '.item-destination',
                    columnWidth: '.grid-sizer',
                    gutter: 0,
                    percentPosition: true,
                    transitionDuration: 0,
                });

                run.imagesLoaded().progress( function() {
                    run.masonry();
                });
                
            });
        });

        /* end Our destination element */


        /* destination slider */
        elementorFrontend.hooks.addAction('frontend/element_ready/ova_destination_slider.default', function(){
            $(".slide-destination").each(function(){
                var owlsl = $(this) ;
                var owlsl_ops = owlsl.data('options') ? owlsl.data('options') : {};

                var responsive_value = {
                    0:{
                        items:1,
                    },
                    576:{
                        items:2,
                    },
                    992:{
                        items:owlsl_ops.items - 1,
                    },
                    1170:{
                        items:owlsl_ops.items
                    }
                };
                  
                owlsl.owlCarousel({
                    margin: owlsl_ops.margin,
                    items: owlsl_ops.items,
                    loop: owlsl_ops.loop,
                    autoplay: owlsl_ops.autoplay,
                    autoplayTimeout: owlsl_ops.autoplayTimeout,
                    center: owlsl_ops.center,
                    nav: owlsl_ops.nav,
                    dots: true,
                    autoWidth: false,
                    thumbs: owlsl_ops.thumbs,
                    autoplayHoverPause: owlsl_ops.autoplayHoverPause,
                    slideBy: owlsl_ops.slideBy,
                    smartSpeed: owlsl_ops.smartSpeed,
                    navText:[
                    '<i class="icomoon icomoon-pre-small" ></i>',
                    '<i class="icomoon icomoon-next-small" ></i>'
                    ],
                    responsive: responsive_value,
                  });

                /* Fixed WCAG */
                owlsl.find(".owl-nav button.owl-prev").attr("title", "Previous");
                owlsl.find(".owl-nav button.owl-next").attr("title", "Next");
                owlsl.find(".owl-dots button").attr("title", "Dots");


                /*****First Load ( add class for change size image destination )********/
                owlsl.find('.owl-item.active').each( function(i) {
                    if ( i === 1 ) {
                        $(this).addClass('active-main-destination');
                    }
                });

                /*****On Changed********/
                owlsl.on('changed.owl.carousel', function(event) {
                    var that = $(this);
                    that.find('.owl-item').removeClass('active-main-destination');
                    var index = event.item.index;
                    that.find('.owl-item').each( function(i) {
                        if ( i === (index + 1) ) {
                            $(this).addClass('active-main-destination');
                        }
                    });
                });

                /*****On Dragged********/
                owlsl.on('dragged.owl.carousel', function(event) {
                    var that = $(this);
                    that.find('.owl-item').removeClass('active-main-destination');
                    var index = event.item.index;
                    that.find('.owl-item').each( function(i) {
                        if ( i === (index + 1) ) {
                            $(this).addClass('active-main-destination');
                        }
                    });
                }); 

            });
        });
        /* end destination slider */

   });
  
})(jQuery);
