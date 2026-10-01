/**
 * hero.js — home hero slider.
 *
 * - Waits for the loader curtain, then plays slide 1's copy in.
 * - Next slide image is fetched + decoded BEFORE the transition (no half-painted frames).
 * - Desktop: directional clip wipe with a gold light sweep (WAAPI, one layer at a time).
 *   Touch / small screens: soft cross-fade. Reduced motion: instant, no autoplay.
 * - Autoplay with progress bars; pauses on hover, focus, hidden tab and when off-screen.
 * - Swipe, keyboard arrows, clickable bars, rolling counter, aria-live announcements.
 * - Single-slide heroes only get the entrance.
 */
( function () {
	'use strict';

	var d = document;
	var w = window;
	var html = d.documentElement;
	var $$ = function ( sel, ctx ) { return Array.prototype.slice.call( ( ctx || d ).querySelectorAll( sel ) ); };
	var reduced = html.classList.contains( 'qaf-rm' ) || ( w.matchMedia && matchMedia( '(prefers-reduced-motion: reduce)' ).matches );
	var rtl = html.getAttribute( 'dir' ) === 'rtl';
	var EASE = 'cubic-bezier(.76,0,.24,1)';

	function whenStarted( fn ) {
		if ( html.classList.contains( 'qaf-anim-started' ) || ! d.body.classList.contains( 'qaf-anim-on' ) || reduced ) {
			fn();
			return;
		}
		var done = false;
		var once = function () { if ( ! done ) { done = true; fn(); } };
		d.addEventListener( 'qaf:anim-start', once, { once: true } );
		setTimeout( once, 3800 );
	}

	function loadImage( media ) {
		var img = media && media.querySelector( 'img' );
		if ( ! img ) { return Promise.resolve(); }
		if ( img.getAttribute( 'data-src' ) ) {
			if ( img.getAttribute( 'data-srcset' ) ) { img.srcset = img.getAttribute( 'data-srcset' ); img.removeAttribute( 'data-srcset' ); }
			img.src = img.getAttribute( 'data-src' );
			img.removeAttribute( 'data-src' );
		}
		var ready = img.complete && img.naturalWidth ? Promise.resolve() : new Promise( function ( res ) {
			img.addEventListener( 'load', res, { once: true } );
			img.addEventListener( 'error', res, { once: true } );
		} );
		return ready.then( function () {
			return img.decode ? img.decode().catch( function () {} ) : null;
		} );
	}

	function setup( hero ) {
		if ( hero.dataset.qafHeroBound ) { return; }
		hero.dataset.qafHeroBound = '1';

		var contents = $$( '.qaf-hero__content', hero );
		var medias = $$( '.qaf-hero__media', hero );
		var bars = $$( '.qaf-hero__bar', hero );
		var sweep = hero.querySelector( '.qaf-hero__sweep' );
		var digitsWrap = hero.querySelector( '.qaf-hero__num' );
		var count = contents.length;
		var multi = hero.hasAttribute( 'data-qaf-slider' ) && count > 1;
		var interval = parseInt( hero.getAttribute( 'data-interval' ), 10 ) || 7000;
		var autoplay = multi && hero.getAttribute( 'data-autoplay' ) !== '0' && ! reduced;
		var index = 0;
		var busy = false;
		var timer = 0;
		var startedAt = 0;
		var remaining = interval;
		var paused = { hover: false, focus: false, hidden: d.hidden, view: false };

		try {
			hero.classList.add( 'is-armed' );
			if ( ! autoplay ) { hero.classList.add( 'is-static' ); }

			/* ---------------------------------------------- Entrance */
			whenStarted( function () {
				requestAnimationFrame( function () { contents[ 0 ] && contents[ 0 ].classList.add( 'is-in' ); } );
				if ( multi ) {
					schedule( interval );
					var warm = function () { loadImage( medias[ 1 ] ); };
					if ( w.requestIdleCallback ) { requestIdleCallback( warm, { timeout: 2500 } ); } else { setTimeout( warm, 1800 ); }
				}
			} );
		} catch ( e ) {
			contents.forEach( function ( c ) { c.classList.add( 'is-in' ); } );
			return;
		}

		if ( ! multi ) { return; }

		/* ---------------------------------------------- Timing */
		function isPaused() { return paused.hover || paused.focus || paused.hidden || paused.view; }

		function schedule( ms ) {
			clearTimeout( timer );
			if ( ! autoplay ) { return; }
			remaining = ms;
			if ( isPaused() ) { return; }
			startedAt = performance.now();
			timer = setTimeout( function () { go( index + 1, 1 ); }, ms );
		}

		function syncPause() {
			var p = isPaused();
			hero.classList.toggle( 'is-paused', p );
			if ( ! autoplay ) { return; }
			if ( p ) {
				if ( timer ) {
					clearTimeout( timer );
					timer = 0;
					remaining = Math.max( 0, remaining - ( performance.now() - startedAt ) );
				}
			} else if ( ! timer && ! busy ) {
				startedAt = performance.now();
				timer = setTimeout( function () { go( index + 1, 1 ); }, remaining );
			}
		}

		/* ---------------------------------------------- Counter */
		function roll( n, dir ) {
			if ( ! digitsWrap ) { return; }
			var old = digitsWrap.querySelector( '[data-current]' );
			var el = d.createElement( 'span' );
			el.className = 'qaf-hero__digits';
			el.setAttribute( 'data-current', '' );
			el.textContent = ( n + 1 < 10 ? '0' : '' ) + ( n + 1 );
			el.style.transform = 'translate3d(0,' + ( dir > 0 ? 100 : -100 ) + '%,0)';
			el.style.opacity = '0';
			el.style.filter = 'blur(4px)';
			digitsWrap.appendChild( el );
			if ( old ) {
				old.removeAttribute( 'data-current' );
				old.classList.add( 'is-leaving' );
			}
			requestAnimationFrame( function () {
				requestAnimationFrame( function () {
					el.style.transform = 'none';
					el.style.opacity = '1';
					el.style.filter = 'none';
					if ( old ) {
						old.style.transform = 'translate3d(0,' + ( dir > 0 ? -100 : 100 ) + '%,0)';
						old.style.opacity = '0';
						old.style.filter = 'blur(4px)';
						setTimeout( function () { old.remove(); }, 800 );
					}
				} );
			} );
		}

		/* ---------------------------------------------- Transition */
		function go( target, dir ) {
			if ( busy ) { return; }
			var next = ( ( target % count ) + count ) % count;
			if ( next === index ) { return; }
			busy = true;
			clearTimeout( timer );
			timer = 0;
			dir = dir || ( next > index ? 1 : -1 );

			var prev = index;
			var fromMedia = medias[ prev ];
			var toMedia = medias[ next ];
			var fromText = contents[ prev ];
			var toText = contents[ next ];

			/* Copy leaves immediately — feels responsive while the image decodes. */
			fromText.classList.remove( 'is-in' );
			fromText.classList.add( 'is-out' );

			bars.forEach( function ( b, i ) {
				b.classList.remove( 'is-active' );
				b.classList.toggle( 'is-done', i < next );
				b.setAttribute( 'aria-selected', i === next ? 'true' : 'false' );
			} );
			roll( next, dir );

			loadImage( toMedia ).then( function () {
				var wide = w.matchMedia && matchMedia( '(min-width: 901px) and (hover: hover)' ).matches;

				fromMedia.classList.add( 'is-leaving' );
				fromMedia.classList.remove( 'is-active' );
				toMedia.classList.add( 'is-active' );

				if ( wide && ! reduced && toMedia.animate ) {
					/* Directional wipe. "Forward" moves with the reading direction. */
					var forward = dir > 0;
					var fromLeft = rtl ? ! forward : forward;
					var start = fromLeft ? 'inset(0 100% 0 0)' : 'inset(0 0 0 100%)';
					toMedia.style.transition = 'none';
					toMedia.animate( [ { clipPath: start }, { clipPath: 'inset(0 0 0 0)' } ], { duration: 1300, easing: EASE } );
					var img = toMedia.querySelector( 'img' );
					if ( img ) {
						img.animate( [ { transform: 'scale(1.18) translate3d(' + ( fromLeft ? '-3%' : '3%' ) + ',0,0)' }, { transform: 'scale(1.1)' } ], { duration: 1500, easing: 'cubic-bezier(.16,1,.3,1)' } );
					}
					if ( sweep && sweep.animate ) {
						var wpx = hero.clientWidth;
						sweep.style.left = '0';
						sweep.style.right = 'auto';
						var a = fromLeft ? 0 : wpx;
						var b = fromLeft ? wpx : 0;
						sweep.animate(
							[
								{ transform: 'translate3d(' + a + 'px,0,0)', opacity: 0 },
								{ opacity: 1, offset: 0.12 },
								{ opacity: 1, offset: 0.85 },
								{ transform: 'translate3d(' + b + 'px,0,0)', opacity: 0 }
							],
							{ duration: 1300, easing: EASE }
						);
					}
					setTimeout( function () { toMedia.style.transition = ''; }, 1320 );
				}

				setTimeout( function () { fromMedia.classList.remove( 'is-leaving' ); }, wide ? 1350 : 1300 );

				/* Swap copy once the old words are gone. */
				setTimeout( function () {
					fromText.classList.remove( 'is-active', 'is-out' );
					fromText.setAttribute( 'aria-hidden', 'true' );
					fromText.setAttribute( 'inert', '' );
					toText.classList.add( 'is-active' );
					toText.removeAttribute( 'aria-hidden' );
					toText.removeAttribute( 'inert' );
					void toText.offsetWidth;
					requestAnimationFrame( function () { toText.classList.add( 'is-in' ); } );
				}, 480 );

				index = next;
				bars[ next ] && bars[ next ].classList.add( 'is-active' );
				loadImage( medias[ ( next + 1 ) % count ] );

				setTimeout( function () {
					busy = false;
					schedule( interval );
				}, 950 );
			} );
		}

		/* ---------------------------------------------- Inputs */
		$$( '.qaf-hero__arrow', hero ).forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				var dir = parseInt( btn.getAttribute( 'data-dir' ), 10 ) || 1;
				go( index + dir, dir );
			} );
		} );
		bars.forEach( function ( bar ) {
			bar.addEventListener( 'click', function () {
				var n = parseInt( bar.getAttribute( 'data-go' ), 10 );
				go( n, n > index ? 1 : -1 );
			} );
		} );

		hero.addEventListener( 'keydown', function ( e ) {
			if ( e.key !== 'ArrowLeft' && e.key !== 'ArrowRight' ) { return; }
			var forwardKey = rtl ? 'ArrowLeft' : 'ArrowRight';
			var dir = e.key === forwardKey ? 1 : -1;
			e.preventDefault();
			go( index + dir, dir );
		} );

		/* Swipe (horizontal intent only, ignores taps on links/buttons) */
		var sx = 0;
		var sy = 0;
		var tracking = false;
		hero.addEventListener( 'pointerdown', function ( e ) {
			if ( e.pointerType === 'mouse' || e.target.closest( 'a, button' ) ) { return; }
			tracking = true;
			sx = e.clientX;
			sy = e.clientY;
		}, { passive: true } );
		hero.addEventListener( 'pointerup', function ( e ) {
			if ( ! tracking ) { return; }
			tracking = false;
			var dx = e.clientX - sx;
			var dy = e.clientY - sy;
			if ( Math.abs( dx ) < 50 || Math.abs( dx ) < Math.abs( dy ) * 1.4 ) { return; }
			var dir = ( dx < 0 ) !== rtl ? 1 : -1;
			go( index + dir, dir );
		}, { passive: true } );
		hero.addEventListener( 'pointercancel', function () { tracking = false; } );

		/* Pause rules */
		if ( w.matchMedia && matchMedia( '(hover: hover)' ).matches ) {
			hero.addEventListener( 'pointerenter', function () { paused.hover = true; syncPause(); } );
			hero.addEventListener( 'pointerleave', function () { paused.hover = false; syncPause(); } );
		}
		hero.addEventListener( 'focusin', function () { paused.focus = true; syncPause(); } );
		hero.addEventListener( 'focusout', function ( e ) {
			if ( ! hero.contains( e.relatedTarget ) ) { paused.focus = false; syncPause(); }
		} );
		d.addEventListener( 'visibilitychange', function () { paused.hidden = d.hidden; syncPause(); } );
		if ( 'IntersectionObserver' in w ) {
			new IntersectionObserver( function ( en ) {
				paused.view = ! en[ 0 ].isIntersecting;
				syncPause();
			}, { threshold: 0.25 } ).observe( hero );
		}
	}

	function init( root ) {
		$$( '[data-qaf-hero]', root || d ).forEach( function ( hero ) {
			if ( hero.classList.contains( 'qaf-hero' ) ) { setup( hero ); }
		} );
	}

	if ( d.readyState === 'loading' ) { d.addEventListener( 'DOMContentLoaded', function () { init(); } ); } else { init(); }
	w.addEventListener( 'elementor/frontend/init', function () {
		if ( w.elementorFrontend && elementorFrontend.hooks ) {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/qb-hero.default', function ( $scope ) {
				if ( $scope && $scope[ 0 ] ) { init( $scope[ 0 ] ); }
			} );
		}
	} );
} )();
