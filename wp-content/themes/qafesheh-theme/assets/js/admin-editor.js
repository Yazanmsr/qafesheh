/* Block editor: calm defaults for the group's team — no welcome tour, no fullscreen, no tips. */
( function ( wp ) {
	if ( ! wp || ! wp.data || ! wp.domReady ) { return; }
	wp.domReady( function () {
		try {
			var prefs = wp.data.dispatch( 'core/preferences' );
			if ( prefs && prefs.set ) {
				prefs.set( 'core/edit-post', 'welcomeGuide', false );
				prefs.set( 'core/edit-post', 'fullscreenMode', false );
				prefs.set( 'core', 'fullscreenMode', false );
				prefs.set( 'core/edit-post', 'isComplementaryAreaVisible', true );
			}
			var tips = wp.data.dispatch( 'core/nux' );
			if ( tips && tips.disableTips ) { tips.disableTips(); }
		} catch ( e ) {}
	} );
} )( window.wp );
