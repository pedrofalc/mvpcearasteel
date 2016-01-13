jQuery(document).ready(function() {

/* Navigation */

	jQuery('#submenu ul.sfmenu').superfish({ 
		delay:       500,								// 0.1 second delay on mouseout 
		animation:   { opacity:'show',height:'show'},	// fade-in and slide-down animation 
		dropShadows: true								// disable drop shadows 
	});	

/* Banner class */

	jQuery('.squarebanner ul li:nth-child(even)').addClass('rbanner');



   jQuery('#maximage').maximage({
        cycleOptions: {
            fx:'fade',
            speed: 500,
            timeout: 5000,
            prev: '#arrow_left',
            next: '#arrow_right'
        },
        onFirstImageLoaded: function(){
            jQuery('#cycle-loader').hide();
            jQuery('#maximage').fadeIn('fast');
        }
    });
	
});