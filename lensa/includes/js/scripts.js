(function($){

var app = {};

"use strict";


/* Responsive Tabs
------------------------------------------------------------------- */
app.responsive_tabs = function( el ) {
  // Check if el not empty
  if( el == '' ) return;
  var $el;

  if( typeof el == 'object' ) {
    $el = el;
  } else {
    $el = $(el);
  }

  // Make sure $el is exists
  if( !$el.length ) return;

  this.el = {
    $element: $el
  };

  /**
   * Append tab heading element
   */
  this.setup_elements = function() {
    this.el.$tabs = this.el.$element.find('.tabs');
    this.el.$panels = this.el.$element.find('.panel');
    this.el.$window = $(window);

    // Get each of tabs title
    this.el.$tabs.children('li').each(function() {
      var $tab = $(this),
          tab_target = $tab.find('a').attr('href'),
          title_text = $tab.find('a').text(),
          $tab_title = $('<h2 class="responsive-tab-heading">'+ title_text +'</h2>');

      // Find the tab_target
      if( $(tab_target).length > 0 ) {
        $tab_title.insertBefore( $(tab_target) );
        $tab_title.data('id', tab_target);
      }
    });
  };

  this.event_binding = function() {
    this.el.$element.on('click', '.responsive-tab-heading', $.proxy(this.open_tab, this));
  };

  this.open_tab = function(e) {
    e.preventDefault();
    var $tab_title = $(e.currentTarget),
        tab_id = $tab_title.data('id');

    if( tab_id ) {
      // this.el.$tabs.find('a[href="'+ tab_id +'"]').trigger('click');

      // Remove all active class, and add class for clicked tab title
      $tab_title.addClass('active').siblings('.responsive-tab-heading').removeClass('active');

      // Show or hide tab
      $tab_title.siblings('.panel').slideUp();
      $(tab_id).slideDown();

      // Scroll to tab
      // this.el.$window.scrollTop( $tab_title.offset().top - 57);
    }
  };

  this.init = function() {
    this.setup_elements();
    this.event_binding();
  }

  this.init();
};

/* App Init
------------------------------------------------------------------- */
app.init = function() {
  app.responsive_tabs('.product_tabs');
};


/* ===================================================================
  Documents ready
=================================================================== */
$(document).ready(function(){

app.init();

/* Show the slideshow
------------------------------------------------------------------- */
$('body').on('click', '.page-heading', function(e){
  var $el = $(this),
      $body = $('body'),
      $footer = $('.footer-section'),
      $progress = $('.progress-wrapper'),
      headingHeight = $el.outerHeight(),
      headerHeight = $('.header-section').height(),
      windowHeight = $(window).height(),
      footerHeight = 45,
      position = windowHeight - headerHeight - headingHeight - footerHeight - 60,
      state;

    // Pause the slideshow
    if( $el.hasClass('active') ) {
      position = 0;
      footerPos = -40;
      progressPos = 0;
      state = 'pause';
      $el.removeClass('active');
      $body.removeClass('slider-active');
    // Play the slideshow
    } else {
      footerPos = 0;
      progressPos = 38;
      state = 'play';
      $el.addClass('active');
      $body.addClass('slider-active');
    }

    $el.animate({ 'margin-top': position });

    if( $footer.hasClass('static') ) {
      $footer.animate({ 'bottom': footerPos });
      $progress.animate({ 'bottom': progressPos });
    }

    if( typeof api !== "undefined" ) {
      api.playToggle(state);
    }
});

/* Responsive Video
------------------------------------------------------------------- */
$('.entry-post iframe, .entry-post object').parent().fitVids(); // Responsive video

/* Set footer position
 * Set fixed when main content height is lower than window height
------------------------------------------------------------------- */
function setFooterPos() {
  var $footer = $('.footer-section'),
      $main = $('.main-container'),
      viewport = $(window).height() - $('.header-section').height() - $('.footer-section').height();

  if( $main.height() > viewport ) {
    $footer.addClass('static');
    $('.progress-wrapper').addClass('bottom');
  }
};
// setFooterPos();
$('.main-container').imagesLoaded(function(){
  setFooterPos();
});



/* Fullscreen Slider
------------------------------------------------------------------- */
if( typeof slides !== "undefined" ) {
  var defaults = {
    slideshow: 1,
    autoplay: 0,
    slide_interval: 7 * 1000,
    transition: 1,
    slide_links: 0,
    thumb_links: 0,
    min_width: 0,
    min_height: 0,
    vertical_center: 1,
    horizontal_center: 1,
    fit_always: 0,
    fit_portrait: 1,
    fit_landscape: 0,
    slides: slides
  },
  options = $.extend( defaults, slider_config );

  $.supersized( options );
}


/* Superfish
------------------------------------------------------------------- */
$('.top-nav ul:first').addClass('sf-menu').superfish({
  delay: 300,
  animation: { opacity: 'show' },
  speed: 'fast',
  dropShadows: false,
  onInit: function() {
    var $el = $(this);
    // Replace submenu indicator
    $el.find('.sf-sub-indicator').html('<i class="icon-caret-down"></i>');
    $el.find('ul .sf-sub-indicator').html('<i class="icon-caret-right"></i>');
  }
});


/* ===================================================================
  #Post List - Masonry
=================================================================== */
var $listMasonry = $('.gallery-list');

$listMasonry.css('opacity', 0);
$listMasonry.imagesLoaded(function(){
  $(this).masonry({
    itemSelector: '.gallery-item',
     isAnimated: true
  });
  $listMasonry.delay(200).fadeTo(500, 1);
});

/* ===================================================================
  #Mobile Menu
=================================================================== */
$('#main-menu-slide').mmenu({
  isMenu: true,
  position: 'right',
  panelNodeType: "nav, div, ul, ol"
}, {
  panelClass: 'menu-panel',
  listClass: 'mobile-menu',
  pageSelector: '.main-wrapper'
});

/* ===================================================================
  #Fancybox
=================================================================== */
function fancyInit( $fancy ) {

  // Add index to fancy link
  $fancy.each(function(i){
    $fancy.attr('data-index', i);
  });

  // Initiate Fancybox
  $fancy.fancybox({
    transitionIn  : 'elastic',
    transitionOut : 'elastic',
    titlePosition: 'inside',
    cyclic: true,
    onComplete: function(el, i) {
      var $el = $fancy.eq(i),
          $like = $el.parent().find('.like'),
          $time = $el.parent().find('.time'),
          wrapper = '',
          url = $el.data('url');

      // Create wrapper
      wrapper += '<div class="fancy-wrapper">';
      if( $like.length > 0 )
        wrapper += '<div class="fancy-like">'+ $like.html() +'</div>';
      if( $time.length > 0)
        wrapper += '<div class="fancy-time">'+ $time.html() +'</div>';
      wrapper += '</div>';

      if( $el.attr('title') ) {
        $('#fancybox-wrap').removeClass('no-title');
      } else {
        $('#fancybox-wrap').addClass('no-title');
      }

      $(wrapper).insertAfter('#fancybox-title');

      // Resize Fancybox
      // $('#fancybox-content').height()

      // Add url for modalbox title if exists
      if( url ) {
        $('#fancybox-title-inside').wrapInner('<a href="'+ url +'" target="_blank"></a>');
      }
    },
    onCleanup: function() {
      $('.fancy-wrapper').remove();
    }
  });
}
fancyInit( $('a.grouped, a[rel^=lightbox]') );

/*
Plugin Placeholder
*/
$('input, textarea').placeholder();


/* Ajax Likes
------------------------------------------------------------------- */
// Jquery Likes
var ajaxMessage = 'Loading...';
$('.entry-likes').live('click', function(e){
  e.preventDefault();
  var 
    likeData  = $(this).data('like'),
    likeData  = likeData.split('_'),
    likeId    = likeData[1],
    liked     = ( $.cookie('like_' + likeId) == 'true' ) ? true : false,
    disable   = ( $(this).hasClass('disabled') || liked ) ? true : false;
    
    if( !disable ) {
      likethis( likeId, $(this) );
    } else {
      ajaxMessage = 'You have already liked this post';
      $('.loading-box').trigger('ajaxSend').trigger('ajaxComplete');
      return;
    }
});

function likethis(likeId, el) {
  $.ajax({
    url: config.ajaxurl,
    //cache: true,
    type: 'post',
    data: {
      action: 'like',
      id: likeId
    },
    beforeSend: function(){
      ajaxMessage = 'Loading...';
    },
    success: function(response, event) {
      $.cookie('like_' + likeId,'true');
      ajaxMessage = 'Liked!';
      
    var likeNumber  = el.find('span'),
      before      = parseInt(likeNumber.text());
      likeNumber.text( before + 1 );
    }
  });

} // end function like this yo!;

/* --- Append loading box for ajax request loading ---*/
var loadingBox = $('<div class="loading-box">').text('Loading...').appendTo('body');
loadingBox.bind( 'ajaxSend', function(res, req){
  $(this).text(ajaxMessage).delay(100).animate({ top: -1 });
})
.bind( 'ajaxComplete', function(res, req){
  var boxHeight = $(this).outerHeight();
  $(this).text( ajaxMessage ).delay(1000).animate({ top: -boxHeight-5 });
});


/* Infinite Scroll
------------------------------------------------------------------- */
$.extend($.infinitescroll.prototype,{

  /* --- Show Done Message --- */
  _showdonemsg_manual_trigger: function infscr_showdonemsg_manual_trigger() {
    var opts = this.options;

    ajaxMessage = "No More Post";
    $(this.element).infinitescroll('destroy');
  }
});


(function(){
  var $container = $('.gallery-list');

  /*$container.infinitescroll({
    behaviour: 'manual_trigger',
    navSelector: '.nav-previous',
    nextSelector: '.nav-previous a',
    itemSelector: '.gallery-item',
    loading: {
        msg: ""
      }
    },
    // Trigger Masonry as a callback
    function( newElements ) {
      // hide new items while they are loading
      var $newElems = $( newElements ).css({ opacity: 0 });
      // ensure that images load before adding to masonry layout
      $newElems.imagesLoaded(function(){
        // show elems now they're ready
        $newElems.animate({ opacity: 1 });
        $container.masonry( 'appended', $newElems, true );
        setFooterPos();
        fancyInit( $newElems.find('a[rel^=lightbox]') );
      });
    }
  );*/
})();

/* ===================================================================
  #Main Slider
=================================================================== */
function mainSlider() {
  var $mainSlider = $('.main-slider-container'),
      $mainSliderNav = $('.main-slider-dirnav'),
      $mainSliderPager = $('.main-slider-pager'),
      $prev = $mainSliderNav.find('.slide-prev'),
      $next = $mainSliderNav.find('.slide-next'),
      slideLen = $mainSlider.children().length,
      setNavThumb,

      // Animation Configuration
      isScrolling = false,
      animateLeft = {
        left: -50,
        opacity: 0
      },
      animateCurrent = {
        left: 0,
        opacity: 1
      },
      animateRight = {
        left: 50,
        opacity: 0
      }
      animSpeed = 550;


  // Set data-index for each slide
  $mainSlider.children().each(function(i){
    $(this).attr('data-index', i+1);
  });

  // Set direction nav thumbnail
  setNavThumb = function( current ) {
    var currentIndex = current.data('index'),
        prevIndex = currentIndex - 1,
        nextIndex = currentIndex + 1;

    prevIndex = ( prevIndex === 0 ) ? slideLen : prevIndex;
    nextIndex = ( nextIndex > slideLen ) ? 1 : nextIndex;
    $prev.find('.thumbnail').css('background-image', 'url(' + $mainSlider.find('a[data-index="'+ prevIndex +'"]').data('thumb') + ')');
    $next.find('.thumbnail').css('background-image', 'url(' + $mainSlider.find('a[data-index="'+ nextIndex +'"]').data('thumb') + ')');
  };


  $mainSlider.css('opacity', 0);
  $mainSlider.imagesLoaded(function(){
    var interval = 3000;

    $mainSlider.carouFredSel({
      auto: parseInt( interval ),
      // infinite: false,
      // circular: false,
      responsive: true,
      prev: {
        button: $prev,
        conditions: function() {
          return (!isScrolling);
        },
        onBefore: function(data) {
          isScrolling = true;
          $(this).delay(400);

          data.items.old.find('.brand-title')
            .delay(100)
            .animate( animateRight, animSpeed );

          data.items.old.find('.slide-title')
            .delay(200)
            .animate( animateRight, animSpeed );

          data.items.old.find('.more')
            .delay(300)
            .animate( animateRight, animSpeed );

          data.items.visible.find('.brand-title, .slide-title, .more').css(animateLeft);
        },
        onAfter: function(data) {
          data.items.old.find('.brand-title, .slide-title, .more').css( animateCurrent );

          data.items.visible.find('.brand-title')
            .css( animateLeft )
            .animate( animateCurrent, animSpeed );

          data.items.visible.find('.slide-title')
            .delay(100)
            .css( animateLeft )
            .animate( animateCurrent, animSpeed );

          data.items.visible.find('.more')
            .delay(200)
            .css( animateLeft )
            .animate( animateCurrent, animSpeed, function(){
              isScrolling = false;
            });

          isScrolling = false;
          setNavThumb( data.items.visible );
        }
      },
      next: {
        button: $next,
        conditions: function() {
          return (!isScrolling);
        },
        onBefore: function(data) {
          isScrolling = true;
          $(this).delay(400);

          data.items.old.find('.brand-title')
            .delay(100)
            .animate( animateLeft, animSpeed );

          data.items.old.find('.slide-title')
            .delay(200)
            .animate( animateLeft, animSpeed );

          data.items.old.find('.more')
            .delay(300)
            .animate( animateLeft, animSpeed );

          data.items.old.next().find('.brand-title, .slide-title, .more').css(animateRight);
        },
        onAfter: function(data) {
          data.items.old.find('.brand-title, .slide-title, .more').css( animateCurrent );

          data.items.visible.find('.brand-title')
            .css( animateRight )
            .animate( animateCurrent, animSpeed );

          data.items.visible.find('.slide-title')
            .delay(100)
            .css( animateRight )
            .animate( animateCurrent, animSpeed );

          data.items.visible.find('.more')
            .delay(200)
            .css( animateRight )
            .animate( animateCurrent, animSpeed, function(){
              isScrolling = false;
            });

          isScrolling = false;
          setNavThumb( data.items.visible );
        }
      },
      swipe: {
        onTouch: true
      },
      // pagination: $mainSliderPager,
      items: {
        visible: 1,
        height: 'variable'
      }
    }, {
      transition: true
    });
    setNavThumb($mainSlider.triggerHandler('currentVisible'));
    $mainSlider.delay(500).fadeTo(500, 1);
  });

}
if( $('.main-slider').length > 0 ) {
  mainSlider();
}

/* ===================================================================
  #Homepage - Photograph section
=================================================================== */
var $photosection = $('.homepage-photograph');

$photosection.imagesLoaded(function(){
  $(this).masonry({
    itemSelector: '.photo-item',
     isAnimated: true
  });
});


// Header Cart Dropdown
// --------------------
$('.top-menu-block > ul > li').hoverIntent(function(){
  $(this).children('div, ul').fadeIn('fast', function(){
    $(this).parent().addClass('sfHover');
  });
}, function(){
  $(this).removeClass('sfHover').children('div, ul').fadeOut('fast');
});


// Custom Selectbox
// ----------------
$(window).load(function(){
  $('table.variations select, .widget_categories select, .form-builder-input select').selectbox({ arrow: 'icon-chevron-down' });    
});


});
})(jQuery);