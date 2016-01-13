/* Change Site Title
------------------------------------------------------------------- */
(function($){
  "use strict";

  $(document).ready(function() { 

    // Site Title
    wp.customize( 'blogname', function( value ) {
      value.bind( function( to ) {
        $( '.header-section .logo a' ).html( to );
      } );
    } );

    var css_data = {
      
      body_color: {
        css: [
          {
            selector: 'body, .primary-sidebar a',
            property: 'color'
          }
        ]
      },
      
      link_color: {
        css: [
          {
            selector: 'a, .entry-title, .entry-date span, .primary-sidebar a:hover, .primary-sidebar li:hover',
            property: 'color'
          }
        ]
      },
      
      header_text: {
        css: [
          {
            selector: '.top-nav a, .site-description',
            property: 'color'
          }
        ]
      },

      header_text_hover: {
        css: [
          {
            selector: '.top-nav .sfHover > a, .top-nav a:hover, .top-nav .current-menu-item > a, .top-nav .current_page_item > a',
            property: 'color'
          }
        ]
      },

      header_background: {
        css: [
          {
            selector: '.header-section',
            property: 'background-color'
          }
        ]
      },

      header_logo_text: {
        css: [
          {
            selector: '.header-section .logo a',
            property: 'color'
          }
        ]
      },

      footer_background: {
        css: [
          {
            selector: '.footer-section',
            property: 'background-color'
          }
        ]
      },

      footer_text: {
        css: [
          {
            selector: '.copyrights',
            property: 'color'
          }
        ]
      },
      
      footer_link_text: {
        css: [
          {
            selector: '.copyrights a',
            property: 'color'
          }
        ]
      },

      pagetitle_color: {
        css: [
          {
            selector: '.page-heading h3, .widget .widget-title, .news-section-container h1, .photograph-section-container h1',
            property: 'color'
          }
        ]
      },
      
      page_background: {
        css: [
          {
            selector: '.block-background',
            property: 'background-color'
          }
        ]
      },
      
      slider_color: {
        css: [
          {
            selector: '.slide-caption, .slide-title a, .slide-text, .main-slider-dirnav i',
            property: 'color'
          }
        ]
      },
      
      slider_background: {
        css: [
          {
            selector: '.slide-nav a, .slide-title a, .slide-caption, .slide-text',
            property: 'background-color'
          }
        ]
      },
      
      button_background: {
        css: [
          {
            selector: '.widget_colabs_subscribe input[type="submit"],.widget_search input[type="submit"], .widget_colabs_search input[type="submit"], .more a, .btn, .form-submit input[type="submit"], .colabs_contact-form .colabs_contact-form-control.colabs_contact-submit',
            property: 'background-color'
          }
        ]
      },
      
      button_text: {
        css: [
          {
            selector: '.widget_colabs_subscribe input[type="submit"],.widget_search input[type="submit"], .widget_colabs_search input[type="submit"], .more a, .btn, .form-submit input[type="submit"], .colabs_contact-form .colabs_contact-form-control.colabs_contact-submit',
            property: 'color'
          }
        ]
      },
      
    };

    /* Loop each css Data, and to wp customizer
    ------------------------------------------------------------------- */
    $.each( css_data, function(index, data) {
      var option_name = 'colabs_color_' + index;
      wp.customize( option_name, function( value ) {
        value.bind( function( to ) {
          $.each( data.css, function( data_index, data_list ) {
            $( data_list.selector ).css( data_list.property, to );
          });
        } );
      } );
    });

  });

})(jQuery);