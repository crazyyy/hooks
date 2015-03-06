(function( jQuery ) {


	/*
	 *  acf/setup_fields
	 *
	 *  This event is triggered when ACF adds any new elements to the DOM.
	 *
	 *  @type	function
	 *  @since	1.0.0
	 *  @date	01/01/12
	 *
	 *  @param	event		e: an event object. This can be ignored
	 *  @param	Element		postbox: An element which contains the new HTML
	 *
	 *  @return	N/A
	 */

	jQuery( document ).live( 'acf/setup_fields', function( e, postbox ) {

		jQuery( '.cat-toggle-btn' ).click( function() {
			var $cat_container = jQuery( this ).parent().parent().find( '.cat-container' );
			var $cat_toggle = jQuery( this ).parent().find( '.cat-toggle-btn' );
			$cat_container.slideToggle( 300, function() {
				if ( $cat_container.is( ':hidden' ) ) {
					$cat_toggle.html( '+ Show Categories' );
				} else {
					$cat_toggle.html( '- Hide Categories' );
				}
			} );

			return false;
		} );

		jQuery( this ).find( '.select-all' ).click( function() {
			var $acfCheckbox = jQuery( this ).parent().parent().find( '.cat-categories-check' );
			$acfCheckbox.attr( 'checked', 'checked' );

			return false;
		} );

		jQuery( '.deselect-all' ).click( function() {
			var $acfCheckbox = jQuery( this ).parent().parent().find( '.cat-categories-check' );
			$acfCheckbox.removeAttr( 'checked' );

			return false;
		} );

		jQuery( '.select-main' ).click( function() {
			var $acfCheckbox = jQuery( this ).parent().parent().find( '.cat-categories-check' );
			var $acfMainCat = jQuery( this ).parent().parent().find( '.cat-categories-check' ).not( '.cat-subcategories-check' );
			$acfCheckbox.removeAttr( 'checked' );
			$acfMainCat.attr( 'checked', 'checked' );

			return false;
		} );

		jQuery( postbox ).find( '.acf-categories' ).each( function() {

		} );
	} );

})( jQuery );
