/**
 * careers.js — vacancy filters (client-side, animated).
 */
( function () {
	'use strict';

	var d = document;
	var w = window;
	var cfg = w.QAFCfg || {};
	var $$ = function ( sel, ctx ) { return Array.prototype.slice.call( ( ctx || d ).querySelectorAll( sel ) ); };
	var bind = function ( el, key ) {
		var k = 'qafBound' + key;
		if ( ! el || el.dataset[ k ] ) { return false; }
		el.dataset[ k ] = '1';
		return true;
	};

	/* ------------------------------------------------ Filters */
	function filters( root ) {
		$$( '[data-qaf-jobs]', root ).forEach( function ( wrap ) {
			if ( ! bind( wrap, 'Jobs' ) ) { return; }
			var cards = $$( '.qaf-jcard', wrap );
			var count = wrap.querySelector( '[data-qaf-jobs-count]' );
			var empty = wrap.querySelector( '.qaf-jobs__empty' );
			var state = { dept: '', loc: '', type: '' };

			var apply = function () {
				var shown = 0;
				cards.forEach( function ( card ) {
					var ok = Object.keys( state ).every( function ( g ) {
						return ! state[ g ] || ( ' ' + ( card.getAttribute( 'data-' + g ) || '' ) + ' ' ).indexOf( ' ' + state[ g ] + ' ' ) > -1;
					} );
					if ( ok ) {
						shown++;
						if ( card.hidden ) {
							card.hidden = false;
							card.classList.add( 'is-hiding' );
							requestAnimationFrame( function () { requestAnimationFrame( function () { card.classList.remove( 'is-hiding' ); } ); } );
						}
					} else if ( ! card.hidden ) {
						card.classList.add( 'is-hiding' );
						setTimeout( function () { card.hidden = true; card.classList.remove( 'is-hiding' ); }, 380 );
					}
				} );
				if ( count ) { count.textContent = shown; }
				if ( empty ) { empty.hidden = shown > 0; }
			};

			$$( '.qaf-jfilters__group', d ).forEach( function ( group ) {
				var name = group.getAttribute( 'data-group' );
				var buttons = $$( '.qaf-jfilter', group );
				buttons.forEach( function ( btn ) {
					btn.addEventListener( 'click', function () {
						var value = btn.getAttribute( 'data-value' );
						var active = btn.classList.contains( 'is-active' );
						state[ name ] = active && value ? '' : value;
						buttons.forEach( function ( b ) {
							var on = b.getAttribute( 'data-value' ) === state[ name ] && ( state[ name ] !== '' || name === 'dept' );
							b.classList.toggle( 'is-active', on );
							b.setAttribute( 'aria-pressed', on ? 'true' : 'false' );
						} );
						apply();
					} );
				} );
			} );
		} );
	}

	function init( root ) {
		filters( root || d );
	}
	if ( d.readyState === 'loading' ) { d.addEventListener( 'DOMContentLoaded', function () { init(); } ); } else { init(); }
	d.addEventListener( 'qaf:refresh', function ( e ) { init( ( e.detail && e.detail.root ) || d ); } );
} )();
