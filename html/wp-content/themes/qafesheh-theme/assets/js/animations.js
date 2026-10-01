/**
 * animations.js — IntersectionObserver entrance engine.
 *
 * [data-qaf-anim]         up | down | start | end | fade | zoom | blur | reveal | line | plinth | words
 * [data-qaf-stagger]      children with data-qaf-anim get incremental delays automatically
 * [data-qaf-count]        numbers roll up when visible
 *
 * Observes CONTAINERS where possible (stagger groups) instead of every card.
 * Waits for the loader to finish before revealing what is already on screen, so the
 * first frame after the curtain is a choreographed entrance, not a pop.
 * Reveals everything immediately when motion is off, reduced, or IO is missing.
 */
( function () {
	'use strict';

	var d = document;
	var html = d.documentElement;
	var cfg = window.QAFCfg || {};
	var $$ = function ( sel, ctx ) { return Array.prototype.slice.call( ( ctx || d ).querySelectorAll( sel ) ); };

	var reduced = html.classList.contains( 'qaf-rm' ) || ( window.matchMedia && matchMedia( '(prefers-reduced-motion: reduce)' ).matches );
	var enabled = d.body.classList.contains( 'qaf-anim-on' ) && ! reduced && 'IntersectionObserver' in window;

	var staggerStep = parseInt( getComputedStyle( html ).getPropertyValue( '--qaf-anim-stagger' ), 10 ) || 90;

	/* ------------------------------------------------ Word split (keeps words intact → Arabic shaping stays correct) */
	function splitWords( el ) {
		if ( el.dataset.qafSplit ) { return; }
		el.dataset.qafSplit = '1';
		var i = 0;
		var walk = function ( node ) {
			Array.prototype.slice.call( node.childNodes ).forEach( function ( child ) {
				if ( child.nodeType === 3 ) {
					var parts = child.textContent.split( /(\s+)/ );
					var frag = d.createDocumentFragment();
					parts.forEach( function ( part ) {
						if ( ! part ) { return; }
						if ( /^\s+$/.test( part ) ) {
							frag.appendChild( d.createTextNode( part ) );
							return;
						}
						var w = d.createElement( 'span' );
						w.className = 'qaf-w';
						w.style.setProperty( '--qaf-i', i++ );
						w.textContent = part;
						frag.appendChild( w );
					} );
					child.parentNode.replaceChild( frag, child );
				} else if ( child.nodeType === 1 && child.tagName !== 'BR' ) {
					walk( child );
				}
			} );
		};
		walk( el );
	}

	/* ------------------------------------------------ Count up */
	function countUp( el ) {
		if ( el.dataset.qafCounted ) { return; }
		el.dataset.qafCounted = '1';
		var raw = ( el.getAttribute( 'data-qaf-count' ) || '' ).trim();
		var target = parseFloat( raw.replace( /[^\d.]/g, '' ) );
		if ( ! cfg.anim || ! cfg.anim.count || reduced || isNaN( target ) ) { return; }
		var pad = /^0\d/.test( raw ) ? raw.length : 0;
		var dur = 1600;
		var start = null;
		var ease = function ( t ) { return 1 - Math.pow( 1 - t, 4 ); };
		var tick = function ( now ) {
			if ( start === null ) { start = now; }
			var p = Math.min( 1, ( now - start ) / dur );
			var v = String( Math.round( target * ease( p ) ) );
			while ( pad && v.length < pad ) { v = '0' + v; }
			el.textContent = v;
			if ( p < 1 ) { requestAnimationFrame( tick ); }
		};
		el.textContent = pad ? new Array( pad + 1 ).join( '0' ) : '0';
		requestAnimationFrame( tick );
	}

	function reveal( el ) {
		el.classList.add( 'qaf-in' );
		$$( '[data-qaf-count]', el ).forEach( countUp );
	}

	function revealAll( ctx ) {
		$$( '[data-qaf-anim]', ctx ).forEach( function ( el ) { el.classList.add( 'qaf-in' ); } );
	}

	/* ------------------------------------------------ Observer */
	var io = null;
	var queue = [];
	var ready = false;

	function prepare( ctx ) {
		$$( '[data-qaf-stagger]', ctx ).forEach( function ( group ) {
			var n = 0;
			$$( '[data-qaf-anim]', group ).forEach( function ( child ) {
				if ( child.closest( '[data-qaf-stagger]' ) !== group || child.dataset.qafStaggered ) { return; }
				child.dataset.qafStaggered = '1';
				if ( ! child.style.getPropertyValue( '--qaf-delay' ) ) {
					child.style.setProperty( '--qaf-delay', ( n * staggerStep ) + 'ms' );
				}
				n++;
			} );
		} );
		$$( '[data-qaf-anim="words"]', ctx ).forEach( splitWords );
	}

	function observe( ctx ) {
		prepare( ctx );
		$$( '[data-qaf-anim]', ctx ).forEach( function ( el ) {
			if ( el.dataset.qafObserved ) { return; }
			el.dataset.qafObserved = '1';
			io.observe( el );
		} );
	}

	function onIntersect( entries ) {
		entries.forEach( function ( entry ) {
			if ( ! entry.isIntersecting ) { return; }
			io.unobserve( entry.target );
			if ( ready ) {
				reveal( entry.target );
			} else {
				queue.push( entry.target );
			}
		} );
	}

	function flush() {
		var list = queue;
		queue = [];
		list.forEach( reveal );
	}

	function start() {
		if ( ! ready ) {
			ready = true;
			html.classList.add( 'qaf-anim-started' );
			var hero = d.querySelector( '[data-qaf-hero]' );
			if ( hero ) { hero.classList.add( 'is-live' ); }
			d.dispatchEvent( new CustomEvent( 'qaf:anim-start' ) );
		}
		/* Always flush: anything queued before start (or by a late Elementor init) must never stay hidden. */
		flush();
	}

	/* Watchdog: anything already on (or above) the screen that is still hidden gets revealed.
	   Covers IntersectionObserver edge cases (transformed / overflow parents, content swapped by
	   page builders, scripts delayed by optimisation plugins). Cheap: only walks unrevealed nodes. */
	var watching = false;
	function watchdog() {
		if ( watching ) { return; }
		watching = true;
		requestAnimationFrame( function () {
			watching = false;
			if ( ! ready ) { return; }
			var vh = window.innerHeight || d.documentElement.clientHeight;
			$$( '[data-qaf-anim]:not(.qaf-in)' ).forEach( function ( el ) {
				var r = el.getBoundingClientRect();
				if ( r.top < vh * 0.98 && ( r.bottom > 0 || r.top < 0 ) && ( r.width || r.height ) ) {
					if ( io ) { io.unobserve( el ); }
					reveal( el );
				}
			} );
		} );
	}

	function init( ctx ) {
		ctx = ctx || d;
		if ( ! enabled ) {
			prepare( ctx );
			revealAll( ctx );
			$$( '[data-qaf-count]', ctx ).forEach( function ( el ) { el.dataset.qafCounted = '1'; } );
			var hero = d.querySelector( '[data-qaf-hero]' );
			if ( hero ) { hero.classList.add( 'is-live' ); }
			return;
		}
		if ( ! io ) {
			io = new IntersectionObserver( onIntersect, { rootMargin: '0px 0px -8% 0px', threshold: 0.01 } );
		}
		observe( ctx );
	}

	try {
		/* Script arrived late (e.g. delayed by an optimisation plugin) and the CSS failsafe already
		   showed the content: keep what is on screen visible instead of hiding it again. */
		if ( window.performance && performance.now() > 3900 ) {
			prepare( d );
			ready = true;
			var vhLate = window.innerHeight;
			$$( '[data-qaf-anim]' ).forEach( function ( el ) {
				if ( el.getBoundingClientRect().top < vhLate ) { el.classList.add( 'qaf-in' ); }
			} );
		}

		html.classList.add( 'qaf-anim-ready' );

		/* Loader tells us when the curtain has started lifting. */
		d.addEventListener( 'qaf:revealed', start );
		if ( ! cfg.loader || ! cfg.loader.on || reduced || html.classList.contains( 'qaf-loaded' ) ) {
			start();
		}
		/* Safety: never wait more than 3.5s for the loader. */
		setTimeout( start, 3500 );

		init( d );

		window.addEventListener( 'scroll', watchdog, { passive: true } );
		window.addEventListener( 'resize', watchdog, { passive: true } );
		window.addEventListener( 'load', function () { setTimeout( watchdog, 400 ); } );
		setTimeout( watchdog, 4000 );
	} catch ( err ) {
		/* Never leave content hidden because of a script error. */
		html.classList.add( 'qaf-anim-ready' );
		ready = true;
		revealAll( d );
		if ( window.console ) { console.error( '[qaf] animations disabled:', err ); }
	}

	/* Re-run after DOM changes (AJAX, Elementor editor). */
	d.addEventListener( 'qaf:refresh', function ( e ) { init( ( e.detail && e.detail.root ) || d ); if ( ready ) { flush(); watchdog(); } } );
	window.addEventListener( 'elementor/frontend/init', function () {
		if ( window.elementorFrontend && elementorFrontend.hooks ) {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/global', function ( $scope ) {
				var root = $scope && $scope[ 0 ];
				if ( ! root ) { return; }
				if ( window.elementorFrontend.isEditMode() ) {
					prepare( root );
					revealAll( root );
					return;
				}
				/* Do NOT flip "ready" here — that used to strand queued elements at opacity 0. */
				init( root );
				if ( ready ) { flush(); watchdog(); }
			} );
		}
	} );

	window.QAFAnim = { init: init, reveal: revealAll };
} )();
