jQuery( document ).ready( function( $ ) {

	if ( typeof $.fn.chosen == 'undefined' ) {
		return;
	}

	$( '.fieldset-products #products, #_products' ).chosen({
		no_results_text: wpjmp.no_resulst_text,
		max_selected_options: wpjmp.chosen_max_selected_options
	});

});