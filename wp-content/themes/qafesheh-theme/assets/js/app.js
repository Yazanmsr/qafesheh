/**
 * app.js — page chrome: loader + page transitions, sticky header, nav ink, mobile menu,
 * scroll progress, back-to-top, hero parallax. Vanilla, deferred, idempotent.
 */
( function () {
	'use strict';

	var d = document;
	var w = window;
	var html = d.documentElement;
	var body = d.body;
	var cfg = w.QAFCfg || {};
	var $ = function ( sel, ctx ) { return ( ctx || d ).querySelector( sel ); };
	var $$ = function ( sel, ctx ) { return Array.prototype.slice.call( ( ctx || d ).querySelectorAll( sel ) ); };
	var on = function ( el, ev, fn, opt ) { if ( el ) { el.addEventListener( ev, fn, opt || false ); } };
	var reduced = html.classList.contains( 'qaf-rm' );
	var rtl = html.getAttribute( 'dir' ) === 'rtl';

	function bind( el, key ) {
		var k = 'qafBound' + key;
		if ( ! el || el.dataset[ k ] ) { return false; }
		el.dataset[ k ] = '1';
		return true;
	}

	/* ==================================================== Loader */
	var loader = $( '#qaf-loader' );

	function loaderInit() {
		var lc = cfg.loader || {};
		if ( ! loader || ! lc.on || reduced ) {
			html.classList.add( 'qaf-loaded' );
			if ( loader ) { loader.classList.add( 'is-done' ); }
			d.dispatchEvent( new CustomEvent( 'qaf:revealed' ) );
			return;
		}

		var quick = html.classList.contains( 'qaf-loader-quick' );
		var t0 = performance.now();
		var minFull = Math.max( 900, lc.min || 1700 );
		var bar = $( '.qaf-loader__bar', loader );
		var count = $( '.qaf-loader__count b', loader );
		var shown = 0;
		var target = 0;
		var finished = false;

		try { sessionStorage.setItem( 'qafSeen', '1' ); } catch ( e ) {}

		/* Real progress: fonts + above-the-fold images. Display value eases toward it. */
		var tasks = [];
		if ( d.fonts && d.fonts.ready ) { tasks.push( d.fonts.ready ); }
		$$( 'img' ).filter( function ( img ) {
			var r = img.getBoundingClientRect();
			return r.top < w.innerHeight * 1.2 && img.loading !== 'lazy';
		} ).forEach( function ( img ) {
			tasks.push( img.complete ? Promise.resolve() : new Promise( function ( res ) { img.addEventListener( 'load', res, { once: true } ); img.addEventListener( 'error', res, { once: true } ); } ) );
		} );
		var total = Math.max( 1, tasks.length );
		var done = 0;
		tasks.forEach( function ( p ) { p.then( function () { done++; target = Math.max( target, done / total ); } ); } );

		function paint() {
			var elapsed = performance.now() - t0;
			var timeShare = Math.min( 1, elapsed / minFull );
			var goal = Math.min( target, timeShare );
			shown += ( goal - shown ) * 0.12;
			if ( goal >= 1 && shown > 0.995 ) { shown = 1; }
			if ( bar ) { bar.style.setProperty( '--qaf-p', shown.toFixed( 4 ) ); }
			if ( count ) { count.textContent = Math.round( shown * 100 ); }
			if ( ! finished ) { requestAnimationFrame( paint ); }
		}

		function finish() {
			if ( finished ) { return; }
			finished = true;
			var lift = function () {
				html.classList.add( 'qaf-loaded' );
				d.dispatchEvent( new CustomEvent( 'qaf:revealed' ) );
				setTimeout( function () { loader.classList.add( 'is-done' ); }, 1300 );
			};
			if ( quick ) {
				lift();
				return;
			}
			if ( bar ) { bar.style.setProperty( '--qaf-p', 1 ); }
			if ( count ) { count.textContent = '100'; }
			loader.classList.add( 'is-leaving' );
			setTimeout( lift, 380 );
		}

		if ( quick ) {
			var goQuick = function () { setTimeout( finish, 120 ); };
			( d.fonts && d.fonts.ready ? d.fonts.ready : Promise.resolve() ).then( goQuick );
			setTimeout( finish, 1400 );
			return;
		}

		requestAnimationFrame( paint );
		Promise.all( tasks ).then( function () {
			var wait = Math.max( 0, minFull - ( performance.now() - t0 ) );
			target = 1;
			setTimeout( finish, wait + 260 );
		} );
		setTimeout( function () { target = 1; setTimeout( finish, 400 ); }, 4200 );
	}

	/* Page leave: soft curtain, then navigate */
	function pageTransitions() {
		var lc = cfg.loader || {};
		if ( ! loader || ! lc.on || ! lc.fx || reduced ) { return; }

		on( d, 'click', function ( e ) {
			var a = e.target.closest ? e.target.closest( 'a' ) : null;
			if ( ! a || e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey ) { return; }
			if ( a.hasAttribute( 'download' ) || a.hasAttribute( 'data-qaf-nofx' ) || ( a.target && a.target !== '_self' ) ) { return; }
			var href = a.getAttribute( 'href' ) || '';
			if ( ! href || href.charAt( 0 ) === '#' || /^(mailto|tel|javascript|whatsapp):/i.test( href ) ) { return; }
			var url;
			try { url = new URL( a.href, w.location.href ); } catch ( err ) { return; }
			if ( url.origin !== w.location.origin ) { return; }
			if ( url.pathname === w.location.pathname && url.search === w.location.search && url.hash ) { return; }
			if ( /\/wp-admin|\/wp-login|elementor|preview=|\.(pdf|zip|jpe?g|png|webp|svg|mp4|docx?|xlsx?)$/i.test( url.pathname + url.search ) ) { return; }
			if ( body.classList.contains( 'elementor-editor-active' ) ) { return; }

			e.preventDefault();
			closeMenu( true );
			html.classList.add( 'qaf-leaving' );
			setTimeout( function () { w.location.href = url.href; }, 640 );
		} );

		/* Back/forward cache restores a covered page — uncover it. */
		on( w, 'pageshow', function ( e ) {
			if ( e.persisted ) {
				html.classList.remove( 'qaf-leaving' );
				html.classList.add( 'qaf-loaded' );
				loader.classList.add( 'is-done' );
			}
		} );
	}

	/* ==================================================== Header */
	var header = $( '#qaf-header' );
	var topbar = header ? $( '.qaf-topbar', header ) : null;

	function measure() {
		if ( topbar ) { html.style.setProperty( '--qaf-topbar-h', topbar.offsetHeight + 'px' ); }
		if ( header ) { html.style.setProperty( '--qaf-header-h', header.offsetHeight + 'px' ); }
	}

	/* ==================================================== Scroll loop (one rAF for everything) */
	var progress = $( '.qaf-progress' );
	var topBtn = $( '.qaf-top' );
	var heroMedia = $( '[data-qaf-hero] .qaf-hero__slides' ) || $( '[data-qaf-hero] .qaf-hero__media' );
	var lastY = w.scrollY;
	var ticking = false;
	var hideDelta = 0;

	function onScroll() {
		var y = w.scrollY;
		var hc = cfg.header || {};

		if ( header ) {
			var past = y > ( topbar ? topbar.offsetHeight : 0 ) + 4;
			header.classList.toggle( 'is-scrolled', past );
			if ( hc.sticky && hc.autohide && ! body.classList.contains( 'qaf-menu-open' ) ) {
				var dy = y - lastY;
				hideDelta = ( dy > 0 ) === ( hideDelta > 0 ) ? hideDelta + dy : dy;
				if ( y > 420 && hideDelta > 24 ) {
					header.classList.add( 'is-hidden' );
				} else if ( hideDelta < -10 || y < 200 ) {
					header.classList.remove( 'is-hidden' );
				}
			}
		}

		if ( progress ) {
			var max = d.documentElement.scrollHeight - w.innerHeight;
			html.style.setProperty( '--qaf-scroll', max > 0 ? ( y / max ).toFixed( 4 ) : 0 );
		}
		if ( topBtn ) { topBtn.classList.toggle( 'is-visible', y > w.innerHeight * 0.9 ); }

		if ( heroMedia && cfg.anim && cfg.anim.parallax && ! reduced && y < w.innerHeight * 1.2 ) {
			heroMedia.style.setProperty( '--qaf-parallax', ( y * 0.22 ).toFixed( 1 ) + 'px' );
		}

		lastY = y;
		ticking = false;
	}

	function requestScroll() {
		if ( ! ticking ) {
			ticking = true;
			requestAnimationFrame( onScroll );
		}
	}

	/* ==================================================== Nav ink (slides between items) */
	function navInk() {
		var nav = $( '.qaf-nav' );
		if ( ! nav || ! bind( nav, 'Ink' ) ) { return; }
		var ink = $( '.qaf-nav__ink', nav );
		var list = $( '.qaf-nav__list', nav );
		if ( ! ink || ! list ) { return; }
		var current = $( ':scope > li.current-menu-item > a, :scope > li.current-menu-ancestor > a, :scope > li.current_page_parent > a', list );

		function moveTo( a ) {
			if ( ! a ) {
				nav.style.setProperty( '--qaf-ink-o', 0 );
				return;
			}
			var navRect = nav.getBoundingClientRect();
			var r = a.getBoundingClientRect();
			var x = rtl ? -( navRect.right - r.right ) : r.left - navRect.left;
			nav.style.setProperty( '--qaf-ink-x', x + 'px' );
			nav.style.setProperty( '--qaf-ink-w', r.width );
			nav.style.setProperty( '--qaf-ink-o', 1 );
		}

		$$( ':scope > li > a', list ).forEach( function ( a ) {
			on( a, 'mouseenter', function () { moveTo( a ); } );
			on( a, 'focus', function () { moveTo( a ); } );
		} );
		on( nav, 'mouseleave', function () { moveTo( current ); } );
		on( w, 'resize', function () { moveTo( current ); }, { passive: true } );
		if ( d.fonts && d.fonts.ready ) { d.fonts.ready.then( function () { moveTo( current ); } ); } else { moveTo( current ); }
	}

	/* ==================================================== Mobile menu */
	var menu = $( '#qaf-mobile' );
	var burger = $( '.qaf-burger' );
	var lastFocus = null;

	function openMenu() {
		if ( ! menu ) { return; }
		lastFocus = d.activeElement;
		menu.removeAttribute( 'inert' );
		menu.setAttribute( 'aria-hidden', 'false' );
		menu.classList.add( 'is-open' );
		body.classList.add( 'qaf-menu-open' );
		if ( burger ) { burger.setAttribute( 'aria-expanded', 'true' ); }
		var close = $( '.qaf-mobile__close', menu );
		setTimeout( function () { if ( close ) { close.focus( { preventScroll: true } ); } }, 350 );
	}

	function closeMenu( silent ) {
		if ( ! menu || ! menu.classList.contains( 'is-open' ) ) { return; }
		menu.classList.remove( 'is-open' );
		menu.setAttribute( 'aria-hidden', 'true' );
		menu.setAttribute( 'inert', '' );
		body.classList.remove( 'qaf-menu-open' );
		if ( burger ) { burger.setAttribute( 'aria-expanded', 'false' ); }
		if ( ! silent && lastFocus ) { lastFocus.focus( { preventScroll: true } ); }
	}

	function mobileMenu() {
		if ( ! menu || ! bind( menu, 'Menu' ) ) { return; }
		$$( '.qaf-mobile__list > li', menu ).forEach( function ( li, i ) { li.style.setProperty( '--qaf-i', i ); } );
		on( burger, 'click', openMenu );
		on( $( '.qaf-mobile__close', menu ), 'click', function () { closeMenu(); } );
		on( $( '.qaf-mobile__bg', menu ), 'click', function () { closeMenu(); } );
		on( d, 'keydown', function ( e ) {
			if ( ! menu.classList.contains( 'is-open' ) ) { return; }
			if ( e.key === 'Escape' ) { closeMenu(); }
			if ( e.key === 'Tab' ) {
				var f = $$( 'a[href], button:not([disabled])', menu ).filter( function ( el ) { return el.offsetParent !== null; } );
				if ( ! f.length ) { return; }
				if ( e.shiftKey && d.activeElement === f[ 0 ] ) { e.preventDefault(); f[ f.length - 1 ].focus(); }
				else if ( ! e.shiftKey && d.activeElement === f[ f.length - 1 ] ) { e.preventDefault(); f[ 0 ].focus(); }
			}
		} );
		$$( 'a[href^="#"]', menu ).forEach( function ( a ) { on( a, 'click', function () { closeMenu( true ); } ); } );
		on( w, 'resize', function () { if ( w.innerWidth > 1240 ) { closeMenu( true ); } }, { passive: true } );
	}

	/* ==================================================== Back to top + smooth anchors */
	function anchors() {
		on( topBtn, 'click', function () { w.scrollTo( { top: 0, behavior: reduced ? 'auto' : 'smooth' } ); } );
		on( d, 'click', function ( e ) {
			var a = e.target.closest ? e.target.closest( 'a[href*="#"]' ) : null;
			if ( ! a ) { return; }
			var url;
			try { url = new URL( a.href, w.location.href ); } catch ( err ) { return; }
			if ( url.pathname !== w.location.pathname || ! url.hash || url.hash.length < 2 ) { return; }
			var target = d.getElementById( decodeURIComponent( url.hash.slice( 1 ) ) );
			if ( ! target ) { return; }
			e.preventDefault();
			if ( header ) { header.classList.remove( 'is-hidden' ); }
			target.scrollIntoView( { behavior: reduced ? 'auto' : 'smooth', block: 'start' } );
			history.replaceState( null, '', url.hash );
		} );
	}

	/* ==================================================== Offer countdowns */
	function countdowns() {
		var boxes = $$( '[data-qaf-countdown]' );
		if ( ! boxes.length ) { return; }
		function pad( n ) { return ( n < 10 ? '0' : '' ) + n; }
		function tick() {
			var now = Math.floor( Date.now() / 1000 );
			boxes.forEach( function ( box ) {
				var left = Math.max( 0, parseInt( box.getAttribute( 'data-qaf-countdown' ), 10 ) - now );
				var v = { d: Math.floor( left / 86400 ), h: Math.floor( ( left % 86400 ) / 3600 ), m: Math.floor( ( left % 3600 ) / 60 ), s: left % 60 };
				Object.keys( v ).forEach( function ( k ) {
					var el = $( '[data-cd="' + k + '"]', box );
					if ( el ) { el.textContent = pad( v[ k ] ); }
				} );
				if ( ! left && ! box.hasAttribute( 'data-done' ) ) {
					box.setAttribute( 'data-done', '' );
					var card = box.closest( '.qaf-offer' );
					if ( card ) { card.style.transition = 'opacity .8s'; card.style.opacity = '.45'; }
				}
			} );
		}
		tick();
		setInterval( tick, 1000 );
	}

	/* ==================================================== Boot */
	function init() {
		countdowns();
		measure();
		navInk();
		mobileMenu();
		anchors();
		pageTransitions();
		onScroll();
		on( w, 'scroll', requestScroll, { passive: true } );
		on( w, 'resize', function () { measure(); requestScroll(); }, { passive: true } );
	}

	loaderInit();
	if ( d.readyState === 'loading' ) {
		d.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}

	w.QAF = { openMenu: openMenu, closeMenu: closeMenu, bind: bind };
} )();
