/* Offers editor: scope panels, unit label, product search, quick durations. */
( function () {
	'use strict';
	var root = document.querySelector( '[data-qaf-ofx]' );
	if ( ! root ) { return; }
	function scope() {
		var cur = root.querySelector( '[data-qaf-ofx-scope]:checked' );
		var v = cur ? cur.value : 'all';
		root.querySelectorAll( '[data-qaf-ofx-panel]' ).forEach( function ( p ) { p.classList.toggle( 'is-on', p.getAttribute( 'data-qaf-ofx-panel' ) === v ); } );
	}
	root.addEventListener( 'change', function ( e ) {
		if ( e.target.matches( '[data-qaf-ofx-scope]' ) ) { scope(); }
		if ( e.target.name === 'qaf_offer[type]' ) {
			var u = root.querySelector( '[data-qaf-ofx-unit]' );
			if ( u ) { u.textContent = e.target.value === 'fixed' ? 'د.أ' : '%'; }
		}
	} );
	var search = root.querySelector( '[data-qaf-ofx-search]' );
	if ( search ) {
		search.addEventListener( 'input', function () {
			var q = search.value.trim().toLowerCase();
			root.querySelectorAll( '.qaf-ofx__prod' ).forEach( function ( el ) { el.hidden = q && el.getAttribute( 'data-name' ).indexOf( q ) === -1; } );
		} );
	}
	function pad( n ) { return ( n < 10 ? '0' : '' ) + n; }
	function local( d ) { return d.getFullYear() + '-' + pad( d.getMonth() + 1 ) + '-' + pad( d.getDate() ) + 'T' + pad( d.getHours() ) + ':' + pad( d.getMinutes() ); }
	root.querySelectorAll( '[data-qaf-ofx-days]' ).forEach( function ( b ) {
		b.addEventListener( 'click', function () {
			var start = root.querySelector( '[name="qaf_offer[start]"]' );
			var end = root.querySelector( '[data-qaf-ofx-end]' );
			var now = new Date();
			if ( start && ! start.value ) { start.value = local( now ); }
			var base = start && start.value ? new Date( start.value ) : now;
			var d = new Date( base.getTime() + parseInt( b.getAttribute( 'data-qaf-ofx-days' ), 10 ) * 864e5 );
			d.setHours( 23, 59, 0, 0 );
			end.value = local( d );
		} );
	} );
	scope();
} )();
