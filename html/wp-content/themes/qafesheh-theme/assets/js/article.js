/**
 * article.js — copy link + table-of-contents scroll spy.
 */
( function () {
	'use strict';

	var d = document;
	var w = window;

	function copy() {
		var btn = d.querySelector( '.qaf-share__copy' );
		if ( ! btn ) { return; }
		var label = btn.querySelector( 'span' );
		var original = label ? label.textContent : '';
		btn.addEventListener( 'click', function () {
			var url = btn.getAttribute( 'data-url' );
			var done = function () {
				btn.classList.add( 'is-done' );
				if ( label ) { label.textContent = btn.getAttribute( 'data-done' ); }
				setTimeout( function () { btn.classList.remove( 'is-done' ); if ( label ) { label.textContent = original; } }, 2200 );
			};
			if ( navigator.clipboard && w.isSecureContext ) {
				navigator.clipboard.writeText( url ).then( done );
			} else {
				var t = d.createElement( 'textarea' );
				t.value = url; t.setAttribute( 'readonly', '' ); t.style.position = 'fixed'; t.style.opacity = '0';
				d.body.appendChild( t ); t.select();
				try { d.execCommand( 'copy' ); done(); } catch ( e ) {}
				d.body.removeChild( t );
			}
		} );
	}

	function toc() {
		var nav = d.querySelector( '[data-qaf-toc]' );
		if ( ! nav || ! ( 'IntersectionObserver' in w ) ) { return; }
		var items = Array.prototype.slice.call( nav.querySelectorAll( '.qaf-toc__item' ) );
		var heads = items.map( function ( li ) { return d.getElementById( decodeURIComponent( li.querySelector( 'a' ).getAttribute( 'href' ).slice( 1 ) ) ); } ).filter( Boolean );
		var setActive = function ( id ) {
			items.forEach( function ( li ) { li.classList.toggle( 'is-active', li.querySelector( 'a' ).getAttribute( 'href' ) === '#' + id ); } );
		};
		var io = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( e ) { if ( e.isIntersecting ) { setActive( e.target.id ); } } );
		}, { rootMargin: '-20% 0px -70% 0px', threshold: 0 } );
		heads.forEach( function ( h ) { io.observe( h ); } );
	}

	var boot = function () { copy(); toc(); };
	if ( d.readyState === 'loading' ) { d.addEventListener( 'DOMContentLoaded', boot ); } else { boot(); }
} )();
