/**
 * ui.js — interactive QB components: brand-card spotlight, branches map switcher.
 * Idempotent (bind()), runs on DOMContentLoaded and on Elementor element_ready.
 */
( function () {
	'use strict';

	var d = document;
	var w = window;
	var $$ = function ( sel, ctx ) { return Array.prototype.slice.call( ( ctx || d ).querySelectorAll( sel ) ); };
	var bind = function ( el, key ) {
		var k = 'qafBound' + key;
		if ( ! el || el.dataset[ k ] ) { return false; }
		el.dataset[ k ] = '1';
		return true;
	};
	var fine = w.matchMedia && matchMedia( '(hover: hover) and (pointer: fine)' ).matches;

	/* ------------------------------------------------ Brand spotlight follows the pointer */
	function spotlight( root ) {
		if ( ! fine ) { return; }
		$$( '.qaf-brand', root ).forEach( function ( card ) {
			if ( ! bind( card, 'Spot' ) ) { return; }
			var raf = 0;
			card.addEventListener( 'pointermove', function ( e ) {
				if ( raf ) { return; }
				raf = requestAnimationFrame( function () {
					var r = card.getBoundingClientRect();
					card.style.setProperty( '--qaf-mx', ( e.clientX - r.left ) + 'px' );
					card.style.setProperty( '--qaf-my', ( e.clientY - r.top ) + 'px' );
					raf = 0;
				} );
			} );
		} );
	}

	/* ------------------------------------------------ Branches: lazy map + switch on click */
	function branches( root ) {
		$$( '[data-qaf-branches]', root ).forEach( function ( wrap ) {
			if ( ! bind( wrap, 'Branches' ) ) { return; }
			var frame = wrap.querySelector( '.qaf-branches__frame' );
			var open = wrap.querySelector( '.qaf-branches__open' );
			var buttons = $$( '.qaf-branch', wrap );
			if ( ! frame ) { return; }

			var load = function ( src ) {
				frame.classList.remove( 'is-loaded' );
				setTimeout( function () { frame.src = src; }, frame.src ? 260 : 0 );
			};
			frame.addEventListener( 'load', function () { if ( frame.src ) { frame.classList.add( 'is-loaded' ); } } );

			if ( 'IntersectionObserver' in w ) {
				var io = new IntersectionObserver( function ( entries ) {
					if ( entries[ 0 ].isIntersecting ) {
						io.disconnect();
						load( frame.getAttribute( 'data-src' ) );
					}
				}, { rootMargin: '300px 0px' } );
				io.observe( wrap );
			} else {
				load( frame.getAttribute( 'data-src' ) );
			}

			buttons.forEach( function ( btn ) {
				btn.addEventListener( 'click', function () {
					if ( btn.classList.contains( 'is-active' ) ) { return; }
					buttons.forEach( function ( b ) { b.classList.remove( 'is-active' ); b.setAttribute( 'aria-pressed', 'false' ); } );
					btn.classList.add( 'is-active' );
					btn.setAttribute( 'aria-pressed', 'true' );
					load( btn.getAttribute( 'data-map' ) );
					if ( open ) {
						var q = new URL( btn.getAttribute( 'data-map' ) ).searchParams.get( 'q' );
						open.href = 'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent( q || '' );
					}
				} );
			} );
		} );
	}

	/* ------------------------------------------------ Branch finder: brand filter + sticky map */
	function finder( root ) {
		$$( '[data-qaf-brfinder]', root ).forEach( function ( wrap ) {
			if ( ! bind( wrap, 'Finder' ) ) { return; }
			var cards = $$( '.qaf-brcard', wrap );
			var chips = $$( '.qaf-chip', wrap );
			var frame = wrap.querySelector( '.qaf-brmap__frame' );
			var badge = wrap.querySelector( '.qaf-brmap__badge' );
			var empty = wrap.querySelector( '.qaf-brlist__empty' );
			var mapCol = wrap.querySelector( '.qaf-brmap' );
			if ( ! frame ) { return; }

			var loadFrame = function ( src ) {
				frame.classList.remove( 'is-loaded' );
				setTimeout( function () { frame.src = src; }, frame.src ? 240 : 0 );
			};
			frame.addEventListener( 'load', function () { if ( frame.src ) { frame.classList.add( 'is-loaded' ); } } );
			if ( 'IntersectionObserver' in w ) {
				var io = new IntersectionObserver( function ( e ) {
					if ( e[ 0 ].isIntersecting ) { io.disconnect(); loadFrame( frame.getAttribute( 'data-src' ) ); }
				}, { rootMargin: '300px 0px' } );
				io.observe( frame );
			} else {
				loadFrame( frame.getAttribute( 'data-src' ) );
			}

			var select = function ( card, scroll ) {
				if ( ! card || card.classList.contains( 'is-active' ) ) { return; }
				cards.forEach( function ( c ) {
					c.classList.toggle( 'is-active', c === card );
					var b = c.querySelector( '.qaf-brcard__map' );
					if ( b ) { b.setAttribute( 'aria-pressed', c === card ? 'true' : 'false' ); }
				} );
				loadFrame( card.getAttribute( 'data-map' ) );
				if ( badge ) {
					badge.classList.add( 'is-swapping' );
					setTimeout( function () {
						var name = badge.querySelector( '.qaf-brmap__name' );
						var open = badge.querySelector( '.qaf-brmap__open' );
						if ( name ) { name.textContent = card.getAttribute( 'data-name' ); }
						if ( open && card.getAttribute( 'data-link' ) ) { open.href = card.getAttribute( 'data-link' ); }
						badge.classList.remove( 'is-swapping' );
					}, 320 );
				}
				if ( scroll && mapCol && w.innerWidth <= 900 ) {
					mapCol.scrollIntoView( { behavior: 'smooth', block: 'start' } );
				}
			};

			cards.forEach( function ( card ) {
				var btn = card.querySelector( '.qaf-brcard__map' );
				if ( btn ) { btn.addEventListener( 'click', function () { select( card, true ); } ); }
			} );

			chips.forEach( function ( chip ) {
				chip.addEventListener( 'click', function () {
					var brand = chip.getAttribute( 'data-brand' );
					chips.forEach( function ( c ) {
						c.classList.toggle( 'is-active', c === chip );
						c.setAttribute( 'aria-pressed', c === chip ? 'true' : 'false' );
					} );
					var visible = [];
					cards.forEach( function ( card ) {
						var show = ! brand || ( ' ' + card.getAttribute( 'data-brands' ) + ' ' ).indexOf( ' ' + brand + ' ' ) > -1;
						if ( show ) {
							visible.push( card );
							if ( card.hidden ) {
								card.hidden = false;
								card.classList.add( 'is-hiding' );
								requestAnimationFrame( function () { requestAnimationFrame( function () { card.classList.remove( 'is-hiding' ); } ); } );
							}
						} else if ( ! card.hidden ) {
							card.classList.add( 'is-hiding' );
							setTimeout( function () { card.hidden = true; card.classList.remove( 'is-hiding' ); }, 420 );
						}
					} );
					if ( empty ) { empty.hidden = visible.length > 0; }
					var active = cards.filter( function ( c ) { return c.classList.contains( 'is-active' ); } )[ 0 ];
					if ( visible.length && visible.indexOf( active ) === -1 ) { select( visible[ 0 ], false ); }
				} );
			} );

			/* Deep link: /branches/#branch-slug */
			if ( w.location.hash && w.location.hash.indexOf( '#branch-' ) === 0 ) {
				var target = d.getElementById( w.location.hash.slice( 1 ) );
				if ( target ) { select( target, false ); }
			}
		} );
	}

	/* ------------------------------------------------ Steps: light up one by one when visible */
	function steps( root ) {
		$$( '[data-qaf-steps]', root ).forEach( function ( list ) {
			if ( ! bind( list, 'Steps' ) ) { return; }
			var items = $$( '.qaf-steps__item', list );
			if ( ! ( 'IntersectionObserver' in w ) || d.documentElement.classList.contains( 'qaf-rm' ) ) {
				items.forEach( function ( i ) { i.classList.add( 'is-active' ); } );
				return;
			}
			items.forEach( function ( i ) { i.classList.remove( 'is-active' ); } );
			var io = new IntersectionObserver( function ( e ) {
				if ( ! e[ 0 ].isIntersecting ) { return; }
				io.disconnect();
				items.forEach( function ( item, n ) { setTimeout( function () { item.classList.add( 'is-active' ); }, 250 + n * 320 ); } );
			}, { threshold: 0.4 } );
			io.observe( list );
		} );
	}

	function init( root ) {
		root = root || d;
		spotlight( root );
		branches( root );
		finder( root );
		steps( root );
	}

	if ( d.readyState === 'loading' ) { d.addEventListener( 'DOMContentLoaded', function () { init(); } ); } else { init(); }
	d.addEventListener( 'qaf:refresh', function ( e ) { init( ( e.detail && e.detail.root ) || d ); } );
	w.addEventListener( 'elementor/frontend/init', function () {
		if ( w.elementorFrontend && elementorFrontend.hooks ) {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/global', function ( $scope ) { if ( $scope && $scope[ 0 ] ) { init( $scope[ 0 ] ); } } );
		}
	} );
} )();

/* ------------------------------------------------ Timeline: track fills, milestones light up */
( function () {
	'use strict';
	var d = document;
	var run = function ( root ) {
		Array.prototype.slice.call( ( root || d ).querySelectorAll( '[data-qaf-timeline]' ) ).forEach( function ( el ) {
			if ( el.dataset.qafBoundTl ) { return; }
			el.dataset.qafBoundTl = '1';
			if ( ! ( 'IntersectionObserver' in window ) || d.documentElement.classList.contains( 'qaf-rm' ) ) { el.classList.add( 'is-in' ); return; }
			var io = new IntersectionObserver( function ( e ) { if ( e[ 0 ].isIntersecting ) { io.disconnect(); el.classList.add( 'is-in' ); } }, { threshold: 0.3 } );
			io.observe( el.querySelector( '.qaf-timeline__list' ) || el );
		} );
	};
	if ( d.readyState === 'loading' ) { d.addEventListener( 'DOMContentLoaded', function () { run(); } ); } else { run(); }
	d.addEventListener( 'qaf:refresh', function ( e ) { run( e.detail && e.detail.root ); } );
} )();
