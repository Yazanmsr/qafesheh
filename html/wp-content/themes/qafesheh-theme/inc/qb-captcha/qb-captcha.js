/*!
 * QB Captcha 1.0.0 — portable bot protection for every form.
 * Providers: QB Shield (built-in proof-of-work), Cloudflare Turnstile, Google reCAPTCHA.
 *
 * Works on its own: any <form> that contains [data-qb-captcha] is guarded at
 * submit (capture phase), so theme AJAX handlers and native forms both receive
 * a fresh, single-use token in <input name="qb_captcha"> without extra code.
 * API: QBCaptcha.ensure( form ) → Promise<token>, QBCaptcha.reset( form ).
 */
( function () {
	'use strict';

	var C = window.QBC_CFG || {};
	var I = C.i18n || {};
	var TTL = 25 * 60 * 1000;
	var widgets = [];

	/* ---------------------------------------------------------------- SHA-256 (sync, fast) */
	function sha256( msg ) {
		var K = sha256.K || ( sha256.K = [ 0x428a2f98, 0x71374491, 0xb5c0fbcf, 0xe9b5dba5, 0x3956c25b, 0x59f111f1, 0x923f82a4, 0xab1c5ed5, 0xd807aa98, 0x12835b01, 0x243185be, 0x550c7dc3, 0x72be5d74, 0x80deb1fe, 0x9bdc06a7, 0xc19bf174, 0xe49b69c1, 0xefbe4786, 0x0fc19dc6, 0x240ca1cc, 0x2de92c6f, 0x4a7484aa, 0x5cb0a9dc, 0x76f988da, 0x983e5152, 0xa831c66d, 0xb00327c8, 0xbf597fc7, 0xc6e00bf3, 0xd5a79147, 0x06ca6351, 0x14292967, 0x27b70a85, 0x2e1b2138, 0x4d2c6dfc, 0x53380d13, 0x650a7354, 0x766a0abb, 0x81c2c92e, 0x92722c85, 0xa2bfe8a1, 0xa81a664b, 0xc24b8b70, 0xc76c51a3, 0xd192e819, 0xd6990624, 0xf40e3585, 0x106aa070, 0x19a4c116, 0x1e376c08, 0x2748774c, 0x34b0bcb5, 0x391c0cb3, 0x4ed8aa4a, 0x5b9cca4f, 0x682e6ff3, 0x748f82ee, 0x78a5636f, 0x84c87814, 0x8cc70208, 0x90befffa, 0xa4506ceb, 0xbef9a3f7, 0xc67178f2 ] );
		var W = sha256.W || ( sha256.W = new Int32Array( 64 ) );
		var l = msg.length, nb = ( ( l + 8 ) >> 6 ) + 1, i, t;
		var B = sha256.B || ( sha256.B = {} ), w = B[ nb ] || ( B[ nb ] = new Int32Array( nb * 16 ) );
		w.fill( 0 );
		for ( i = 0; i < l; i++ ) { w[ i >> 2 ] |= ( msg.charCodeAt( i ) & 255 ) << ( 24 - ( i & 3 ) * 8 ); }
		w[ l >> 2 ] |= 0x80 << ( 24 - ( l & 3 ) * 8 );
		w[ nb * 16 - 1 ] = l * 8;
		var h0 = 0x6a09e667, h1 = 0xbb67ae85 | 0, h2 = 0x3c6ef372, h3 = 0xa54ff53a | 0, h4 = 0x510e527f, h5 = 0x9b05688c | 0, h6 = 0x1f83d9ab, h7 = 0x5be0cd19;
		for ( var b = 0; b < nb * 16; b += 16 ) {
			for ( t = 0; t < 16; t++ ) { W[ t ] = w[ b + t ]; }
			for ( t = 16; t < 64; t++ ) {
				var x = W[ t - 15 ], y = W[ t - 2 ];
				W[ t ] = ( ( ( ( x >>> 7 ) | ( x << 25 ) ) ^ ( ( x >>> 18 ) | ( x << 14 ) ) ^ ( x >>> 3 ) ) + W[ t - 7 ] + ( ( ( y >>> 17 ) | ( y << 15 ) ) ^ ( ( y >>> 19 ) | ( y << 13 ) ) ^ ( y >>> 10 ) ) + W[ t - 16 ] ) | 0;
			}
			var a = h0, c = h2, d = h3, e = h4, f = h5, g = h6, h = h7, bb = h1;
			for ( t = 0; t < 64; t++ ) {
				var t1 = ( h + ( ( ( e >>> 6 ) | ( e << 26 ) ) ^ ( ( e >>> 11 ) | ( e << 21 ) ) ^ ( ( e >>> 25 ) | ( e << 7 ) ) ) + ( ( e & f ) ^ ( ~e & g ) ) + K[ t ] + W[ t ] ) | 0;
				var t2 = ( ( ( ( a >>> 2 ) | ( a << 30 ) ) ^ ( ( a >>> 13 ) | ( a << 19 ) ) ^ ( ( a >>> 22 ) | ( a << 10 ) ) ) + ( ( a & bb ) ^ ( a & c ) ^ ( bb & c ) ) ) | 0;
				h = g; g = f; f = e; e = ( d + t1 ) | 0; d = c; c = bb; bb = a; a = ( t1 + t2 ) | 0;
			}
			h0 = ( h0 + a ) | 0; h1 = ( h1 + bb ) | 0; h2 = ( h2 + c ) | 0; h3 = ( h3 + d ) | 0;
			h4 = ( h4 + e ) | 0; h5 = ( h5 + f ) | 0; h6 = ( h6 + g ) | 0; h7 = ( h7 + h ) | 0;
		}
		return [ h0, h1, h2, h3, h4, h5, h6, h7 ];
	}

	function search( c, d, from, to ) {
		var p = c + ':';
		for ( var n = from; n < to; n++ ) {
			if ( ( sha256( p + n )[ 0 ] >>> ( 32 - d ) ) === 0 ) { return n; }
		}
		return -1;
	}

	/* Solve in a Web Worker (keeps scrolling silky); fall back to chunks on the main thread. */
	var workerUrl = null;
	function solve( c, d ) {
		return new Promise( function ( resolve, reject ) {
			var worker = null;
			try {
				if ( ! workerUrl ) {
					var src = 'var sha256=' + sha256.toString() + ';var search=' + search.toString() +
						';onmessage=function(e){postMessage({n:search(e.data.c,e.data.d,0,1e9)});};';
					workerUrl = URL.createObjectURL( new Blob( [ src ], { type: 'text/javascript' } ) );
				}
				worker = new Worker( workerUrl );
			} catch ( err ) { worker = null; }
			if ( worker ) {
				var done = false;
				var timer = setTimeout( function () { if ( ! done ) { worker.terminate(); main(); } }, 20000 );
				worker.onmessage = function ( e ) { done = true; clearTimeout( timer ); worker.terminate(); if ( e.data.n >= 0 ) { resolve( e.data.n ); } else { reject( new Error( 'nosol' ) ); } };
				worker.onerror = function () { done = true; clearTimeout( timer ); worker.terminate(); main(); };
				worker.postMessage( { c: c, d: d } );
				return;
			}
			main();
			function main() {
				var n = 0, step = 25000;
				( function chunk() {
					var r = search( c, d, n, n + step );
					if ( r >= 0 ) { resolve( r ); return; }
					n += step;
					if ( n > 1e9 ) { reject( new Error( 'nosol' ) ); return; }
					setTimeout( chunk, 0 );
				}() );
			}
		} );
	}

	/* ---------------------------------------------------------------- Helpers */
	function wait( ms ) { return new Promise( function ( r ) { setTimeout( r, ms ); } ); }
	var scripts = {};
	function load( src ) {
		if ( ! scripts[ src ] ) {
			scripts[ src ] = new Promise( function ( resolve, reject ) {
				var s = document.createElement( 'script' );
				s.src = src; s.async = true; s.defer = true;
				s.onload = resolve;
				s.onerror = function () { delete scripts[ src ]; reject( new Error( 'load' ) ); };
				document.head.appendChild( s );
			} );
		}
		return scripts[ src ];
	}
	function fail( w, msg ) {
		var e = new Error( msg || I.failed || 'Please try again.' );
		e.qbc = true;
		say( w, e.message, true );
		return e;
	}
	function say( w, text, bad ) {
		if ( ! w.msg ) {
			w.msg = document.createElement( 'p' );
			w.msg.className = 'qbc-msg';
			w.msg.setAttribute( 'role', 'alert' );
			w.el.appendChild( w.msg );
		}
		w.msg.textContent = text || '';
		w.msg.hidden = ! text;
		w.el.classList.toggle( 'is-error', !! bad );
	}
	function state( w, s ) {
		w.el.classList.remove( 'is-busy', 'is-done' );
		if ( s ) { w.el.classList.add( 'is-' + s ); }
		if ( w.box ) { w.box.setAttribute( 'aria-checked', 'done' === s ? 'true' : 'false' ); w.box.setAttribute( 'aria-busy', 'busy' === s ? 'true' : 'false' ); }
		if ( w.label && w.labels ) { w.label.textContent = w.labels[ s || 'idle' ] || w.labels.idle; }
	}
	function fresh( w ) { return w.token && Date.now() - w.at < TTL; }
	function setToken( w, tok ) {
		w.token = tok; w.at = Date.now();
		w.input.value = tok;
	}

	/* ---------------------------------------------------------------- QB Shield */
	function shield( w ) {
		if ( w.pending ) { return w.pending; }
		var t0 = Date.now();
		w.pending = window.fetch( C.ajax + '?action=qb_captcha_challenge&ctx=' + encodeURIComponent( w.ctx ) + '&_=' + t0, { credentials: 'same-origin', cache: 'no-store' } )
			.then( function ( r ) { return r.json(); } )
			.then( function ( r ) {
				if ( ! r || ! r.success ) { throw new Error( 'challenge' ); }
				var bits = parseInt( r.data.c.split( '.' )[ 2 ], 10 );
				return solve( r.data.c, bits ).then( function ( n ) { return 'qs|' + r.data.c + '|' + r.data.s + '|' + n; } );
			} )
			.then( function ( tok ) {
				setToken( w, tok );
				w.pending = null;
				return tok;
			}, function ( err ) { w.pending = null; throw err; } );
		return w.pending;
	}

	/* ---------------------------------------------------------------- Cloudflare Turnstile */
	function turnstile( w ) {
		if ( w.rendered ) { return w.rendered; }
		w.rendered = load( 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit' )
			.then( function () {
				return new Promise( function ( resolve ) {
					( function ready() { if ( window.turnstile ) { resolve(); } else { setTimeout( ready, 50 ); } }() );
				} );
			} )
			.then( function () {
				w.id = window.turnstile.render( w.slot, {
					sitekey: C.key,
					action: w.ctx,
					theme: 'light',
					size: 'flexible',
					appearance: 'checkbox' === C.mode ? 'always' : 'interaction-only',
					callback: function ( tok ) { setToken( w, 'ts|' + tok ); state( w, 'done' ); say( w, '' ); if ( w.waiter ) { w.waiter(); } },
					'expired-callback': function () { setToken( w, '' ); w.token = ''; state( w, '' ); },
					'error-callback': function () { setToken( w, '' ); w.token = ''; }
				} );
			} );
		return w.rendered;
	}

	/* ---------------------------------------------------------------- Google reCAPTCHA */
	function recaptchaReady() {
		var v3 = 'invisible' === C.mode;
		return load( 'https://www.google.com/recaptcha/api.js?render=' + ( v3 ? encodeURIComponent( C.key ) : 'explicit' ) )
			.then( function () {
				return new Promise( function ( resolve ) {
					( function ready() { if ( window.grecaptcha && window.grecaptcha.ready ) { window.grecaptcha.ready( resolve ); } else { setTimeout( ready, 50 ); } }() );
				} );
			} );
	}
	function recaptchaV2( w ) {
		if ( w.rendered ) { return w.rendered; }
		w.rendered = recaptchaReady().then( function () {
			w.id = window.grecaptcha.render( w.slot, {
				sitekey: C.key,
				theme: 'light',
				callback: function ( tok ) { setToken( w, 'rc|' + tok ); state( w, 'done' ); say( w, '' ); },
				'expired-callback': function () { w.token = ''; w.input.value = ''; state( w, '' ); }
			} );
		} );
		return w.rendered;
	}

	/* ---------------------------------------------------------------- Ensure a token */
	function ensureW( w ) {
		if ( fresh( w ) ) { return Promise.resolve( w.token ); }
		var p = C.provider;
		if ( 'shield' === p ) {
			if ( 'checkbox' === C.mode && ! w.human ) {
				nudge( w );
				return Promise.reject( fail( w, I.tick ) );
			}
			if ( ! w.box ) { state( w, 'busy' ); }
			return shield( w ).then( function ( t ) { state( w, 'done' ); return t; }, function () { state( w, '' ); throw fail( w ); } );
		}
		if ( 'turnstile' === p ) {
			return turnstile( w ).then( function () {
				if ( fresh( w ) ) { return w.token; }
				return new Promise( function ( resolve, reject ) {
					var to = setTimeout( function () { w.waiter = null; nudge( w ); reject( fail( w, I.tick ) ); }, 'checkbox' === C.mode ? 1500 : 15000 );
					w.waiter = function () { clearTimeout( to ); w.waiter = null; resolve( w.token ); };
				} );
			} );
		}
		if ( 'recaptcha' === p && 'invisible' === C.mode ) {
			return recaptchaReady()
				.then( function () { return window.grecaptcha.execute( C.key, { action: w.ctx.replace( /[^A-Za-z_]/g, '_' ) } ); } )
				.then( function ( tok ) { setToken( w, 'rc|' + tok ); w.at = Date.now() - TTL + 100000; return w.token; }, function () { throw fail( w ); } );
		}
		if ( 'recaptcha' === p ) {
			return recaptchaV2( w ).then( function () { nudge( w ); throw fail( w, I.tick ); } );
		}
		return Promise.resolve( '' );
	}

	function nudge( w ) {
		w.el.classList.remove( 'is-nudge' );
		void w.el.offsetWidth; // Restart the animation.
		w.el.classList.add( 'is-nudge' );
		if ( w.box ) { w.box.focus( { preventScroll: true } ); }
		var r = w.el.getBoundingClientRect();
		if ( r.top < 0 || r.bottom > window.innerHeight ) { w.el.scrollIntoView( { behavior: 'smooth', block: 'center' } ); }
	}

	/* A used token is gone — prepare the next one quietly. */
	function consume( w ) {
		w.token = ''; w.at = 0; w.input.value = '';
		if ( 'shield' === C.provider ) {
			if ( 'checkbox' !== C.mode || w.human ) { shield( w ).catch( function () {} ); }
		} else if ( 'turnstile' === C.provider && window.turnstile && null != w.id ) {
			window.turnstile.reset( w.id ); state( w, '' );
		} else if ( 'recaptcha' === C.provider && 'checkbox' === C.mode && window.grecaptcha && null != w.id ) {
			window.grecaptcha.reset( w.id ); state( w, '' );
		}
	}

	/* ---------------------------------------------------------------- Mount */
	function mount( el ) {
		if ( el.qbc ) { return el.qbc; }
		var form = el.closest( 'form' );
		var w = {
			el: el,
			form: form,
			ctx: el.getAttribute( 'data-ctx' ) || 'form',
			input: el.querySelector( 'input[name="qb_captcha"]' ),
			box: el.querySelector( '[data-qbc-box]' ),
			slot: el.querySelector( '[data-qbc-slot]' ),
			label: el.querySelector( '[data-qbc-label]' ),
			token: '', at: 0
		};
		el.qbc = w;
		widgets.push( w );
		if ( w.label ) {
			w.labels = w.box
				? { idle: w.label.textContent, busy: I.verifying || 'Verifying…', done: I.verified || 'Verified' }
				: { idle: w.label.textContent, busy: w.label.textContent, done: I.human || w.label.textContent };
		}

		// Checkbox (QB Shield): a real click from a person starts the check.
		if ( w.box ) {
			w.box.addEventListener( 'click', function () {
				if ( w.el.classList.contains( 'is-busy' ) || fresh( w ) ) { return; }
				w.human = true;
				say( w, '' );
				state( w, 'busy' );
				var t0 = Date.now();
				shield( w )
					.then( function () { return wait( Math.max( 0, 950 - ( Date.now() - t0 ) ) ); } )
					.then( function () { state( w, 'done' ); }, function () { state( w, '' ); fail( w ); } );
			} );
		}

		// Warm up: start on the first sign of a person (or when a widget scrolls into view).
		var warmed = false;
		function warm() {
			if ( warmed ) { return; }
			warmed = true;
			if ( 'shield' === C.provider && 'invisible' === C.mode ) { shield( w ).catch( function () { warmed = false; } ); }
			if ( 'turnstile' === C.provider ) { turnstile( w ).catch( function () { warmed = false; } ); }
			if ( 'recaptcha' === C.provider ) {
				( 'checkbox' === C.mode ? recaptchaV2( w ) : recaptchaReady() ).catch( function () { warmed = false; } );
			}
		}
		if ( form ) {
			form.addEventListener( 'focusin', warm );
			form.addEventListener( 'pointerdown', warm );
		}
		if ( 'checkbox' === C.mode && 'shield' !== C.provider && 'IntersectionObserver' in window ) {
			var io = new IntersectionObserver( function ( en ) { if ( en[ 0 ].isIntersecting ) { io.disconnect(); warm(); } }, { rootMargin: '200px' } );
			io.observe( el );
		}
		return w;
	}

	function scan( root ) {
		Array.prototype.forEach.call( ( root || document ).querySelectorAll( '[data-qb-captcha]' ), mount );
	}

	/* ---------------------------------------------------------------- Guard every protected form */
	document.addEventListener( 'submit', function ( e ) {
		var form = e.target;
		var el = form && form.querySelector ? form.querySelector( '[data-qb-captcha]' ) : null;
		if ( ! el ) { return; }
		var w = mount( el );
		if ( fresh( w ) ) {
			// Let it through (theme handlers read the field now), then retire the token.
			setTimeout( function () { consume( w ); }, 0 );
			return;
		}
		e.preventDefault();
		e.stopImmediatePropagation();
		if ( w.busySubmit ) { return; }
		w.busySubmit = true;
		var submitter = e.submitter || null;
		form.classList.add( 'qbc-checking' );
		ensureW( w ).then( function () {
			w.busySubmit = false;
			form.classList.remove( 'qbc-checking' );
			say( w, '' );
			if ( form.requestSubmit ) {
				try { form.requestSubmit( submitter && submitter.form === form ? submitter : undefined ); } catch ( er ) { form.requestSubmit(); }
			} else {
				HTMLFormElement.prototype.submit.call( form );
			}
		}, function () {
			w.busySubmit = false;
			form.classList.remove( 'qbc-checking' );
		} );
	}, true );

	/* ---------------------------------------------------------------- Public API */
	window.QBCaptcha = {
		ensure: function ( form ) {
			var el = form && form.querySelector( '[data-qb-captcha]' );
			return el ? ensureW( mount( el ) ) : Promise.resolve( '' );
		},
		reset: function ( form ) {
			var el = form && form.querySelector( '[data-qb-captcha]' );
			if ( el ) { consume( mount( el ) ); }
		},
		scan: scan,
		_sha256: sha256
	};

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', function () { scan(); } );
	} else {
		scan();
	}
}() );
