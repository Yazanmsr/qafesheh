/**
 * forms.js — application, contact and complaint forms: validation, file picker, AJAX submit,
 * company → branch narrowing, and complaint status lookup. Forms work without JS (admin-post).
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

	/* ------------------------------------------------ Forms */
	function forms( root ) {
		$$( '[data-qaf-apply], [data-qaf-form]', root ).forEach( function ( form ) {
			if ( ! bind( form, 'Apply' ) ) { return; }
			var status = form.querySelector( '.qaf-apply__status' );
			var fileWrap = form.querySelector( '.qaf-file' );
			var fileInput = fileWrap ? fileWrap.querySelector( 'input[type="file"]' ) : null;
			var hint = fileWrap ? fileWrap.querySelector( '.qaf-file__hint' ) : null;
			var submit = form.querySelector( '.qaf-apply__submit .qaf-btn__label' );
			var submitText = submit ? submit.textContent : '';
			var max = parseInt( form.getAttribute( 'data-max' ), 10 ) || 5242880;

			var setError = function ( name, msg ) {
				var slot = form.querySelector( '.qaf-field__error[data-for="' + name + '"]' );
				var holder = slot ? slot.closest( '.qaf-field, .qaf-file, .qaf-check' ) : null;
				if ( slot ) { slot.textContent = msg || ''; }
				if ( holder ) { holder.classList.toggle( 'is-invalid', !! msg ); }
			};
			var clearErrors = function () {
				$$( '.qaf-field__error', form ).forEach( function ( s ) { s.textContent = ''; } );
				$$( '.is-invalid', form ).forEach( function ( el ) { el.classList.remove( 'is-invalid' ); } );
			};
			var showStatus = function ( msg, isError ) {
				if ( ! status ) { return; }
				status.hidden = false;
				status.textContent = msg;
				status.classList.toggle( 'is-error', !! isError );
			};

			/* Textarea label float */
			$$( '.qaf-field--area textarea', form ).forEach( function ( t ) {
				var sync = function () { t.closest( '.qaf-field' ).classList.toggle( 'has-value', t.value.trim() !== '' ); };
				t.addEventListener( 'input', sync );
				sync();
			} );

			/* File picker */
			var fileOk = function () {
				if ( ! fileInput || ! fileInput.files || ! fileInput.files[ 0 ] ) { return false; }
				var f = fileInput.files[ 0 ];
				var re = form.hasAttribute( 'data-qaf-form' ) ? /\.(jpe?g|png|webp|pdf)$/i : /\.(pdf|docx?)$/i;
				return re.test( f.name ) && f.size <= max && f.size > 100;
			};
			if ( fileInput ) {
				fileInput.addEventListener( 'change', function () {
					var f = fileInput.files && fileInput.files[ 0 ];
					fileWrap.classList.toggle( 'has-file', !! f );
					if ( hint ) { hint.textContent = f ? f.name + ' — ' + ( f.size / 1048576 ).toFixed( 1 ) + ' MB' : hint.getAttribute( 'data-default' ); }
					setError( fileInput.name, f && ! fileOk() ? form.getAttribute( 'data-bad-file' ) : '' );
				} );
				[ 'dragenter', 'dragover' ].forEach( function ( ev ) { fileWrap.addEventListener( ev, function () { fileWrap.classList.add( 'is-drag' ); } ); } );
				[ 'dragleave', 'drop' ].forEach( function ( ev ) { fileWrap.addEventListener( ev, function () { fileWrap.classList.remove( 'is-drag' ); } ); } );
			}

			var validate = function () {
				clearErrors();
				var req = form.getAttribute( 'data-required' );
				var ok = true;
				var first = null;
				var fail = function ( name, msg ) { ok = false; setError( name, msg ); if ( ! first ) { first = form.querySelector( '[name="' + name + '"]' ); } };
				var val = function ( n ) { var el = form.querySelector( '[name="' + n + '"]' ); return el ? el.value.trim() : ''; };
				if ( form.querySelector( '[name="qaf_name"]' ) && val( 'qaf_name' ).length < 2 ) { fail( 'qaf_name', req ); }
				if ( val( 'qaf_phone' ).replace( /\D/g, '' ).length < 7 ) { fail( 'qaf_phone', req ); }
				if ( ! /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test( val( 'qaf_email' ) ) ) { fail( 'qaf_email', form.getAttribute( 'data-bad-email' ) ); }
				if ( fileInput ) {
					var hasFile = fileInput.files && fileInput.files[ 0 ];
					if ( ( fileInput.required || hasFile ) && ! fileOk() ) { fail( fileInput.name, hasFile ? form.getAttribute( 'data-bad-file' ) : req ); }
				}
				if ( form.querySelector( '[name="qaf_subject"]' ) && val( 'qaf_subject' ).length < 2 ) { fail( 'qaf_subject', req ); }
				if ( form.querySelector( '[name="qaf_message"][required]' ) && val( 'qaf_message' ).length < 2 ) { fail( 'qaf_message', req ); }
				var consent = form.querySelector( '[name="qaf_consent"]' );
				if ( consent && ! consent.checked ) { fail( 'qaf_consent', req ); }
				if ( first ) { first.focus( { preventScroll: false } ); }
				return ok;
			};

			$$( 'input, textarea', form ).forEach( function ( el ) {
				el.addEventListener( 'input', function () { if ( el.name ) { setError( el.name, '' ); } } );
			} );

			form.addEventListener( 'submit', function ( e ) {
				if ( ! w.fetch || ! w.FormData ) { return; }
				e.preventDefault();
				if ( form.classList.contains( 'is-sending' ) || ! validate() ) { return; }

				var data = new FormData( form );
				form.classList.add( 'is-sending' );
				if ( submit ) { submit.textContent = form.getAttribute( 'data-sending' ); }
				if ( status ) { status.hidden = true; }

				fetch( cfg.ajax, { method: 'POST', body: data, credentials: 'same-origin' } )
					.then( function ( r ) { return r.json().catch( function () { return { success: false, data: {} }; } ); } )
					.then( function ( res ) {
						form.classList.remove( 'is-sending' );
						if ( submit ) { submit.textContent = submitText; }
						if ( res && res.success ) {
							showStatus( res.data.message, false );
							form.classList.add( 'is-done' );
							form.scrollIntoView( { behavior: 'smooth', block: 'center' } );
							return;
						}
						var errs = res && res.data && res.data.errors ? res.data.errors : {};
						Object.keys( errs ).forEach( function ( k ) { setError( k, errs[ k ] ); } );
						showStatus( ( res && res.data && res.data.message ) || form.getAttribute( 'data-error' ), true );
					} )
					.catch( function () {
						form.classList.remove( 'is-sending' );
						if ( submit ) { submit.textContent = submitText; }
						showStatus( form.getAttribute( 'data-error' ), true );
					} );
			} );
		} );
	}


	/* ------------------------------------------------ Company → branch */
	function branches( root ) {
		$$( '[data-qaf-form]', root ).forEach( function ( form ) {
			var company = form.querySelector( 'select[name="qaf_company"]' );
			var branch = form.querySelector( 'select[name="qaf_branch"]' );
			if ( ! company || ! branch || ! bind( company, 'Branch' ) ) { return; }
			var options = $$( 'option', branch );
			company.addEventListener( 'change', function () {
				var opt = company.options[ company.selectedIndex ];
				var brand = opt ? opt.getAttribute( 'data-brand' ) : '';
				options.forEach( function ( o ) {
					if ( ! o.value ) { return; }
					var ok = ! brand || ( ' ' + ( o.getAttribute( 'data-brands' ) || '' ) + ' ' ).indexOf( ' ' + brand + ' ' ) > -1;
					o.hidden = ! ok;
					o.disabled = ! ok;
				} );
				if ( branch.selectedOptions[ 0 ] && branch.selectedOptions[ 0 ].disabled ) { branch.value = ''; }
			} );
		} );
	}

	/* ------------------------------------------------ Status lookup */
	function track( root ) {
		$$( '[data-qaf-track]', root ).forEach( function ( form ) {
			if ( ! bind( form, 'Track' ) ) { return; }
			var out = form.querySelector( '.qaf-track__result' );
			form.addEventListener( 'submit', function ( e ) {
				e.preventDefault();
				var data = new FormData();
				data.append( 'action', 'qaf_track' );
				data.append( 'nonce', form.getAttribute( 'data-nonce' ) );
				data.append( 'tracking', ( form.querySelector( '[name="tracking"]' ) || {} ).value || '' );
				data.append( 'email', ( form.querySelector( '[name="email"]' ) || {} ).value || '' );
				form.classList.add( 'is-sending' );
				fetch( cfg.ajax, { method: 'POST', body: data, credentials: 'same-origin' } )
					.then( function ( r ) { return r.json(); } )
					.then( function ( res ) {
						form.classList.remove( 'is-sending' );
						out.hidden = false;
						out.classList.toggle( 'is-ok', !! ( res && res.success ) );
						out.textContent = res && res.data && res.data.message ? res.data.message + ( res.success && res.data.date ? ' · ' + res.data.date : '' ) : form.getAttribute( 'data-none' );
					} )
					.catch( function () {
						form.classList.remove( 'is-sending' );
						out.hidden = false;
						out.textContent = form.getAttribute( 'data-none' );
					} );
			} );
		} );
	}

	/* ------------------------------------------------ Lazy map (contact) */
	function maps( root ) {
		$$( '[data-qaf-lazymap]', root ).forEach( function ( frame ) {
			if ( ! bind( frame, 'Map' ) ) { return; }
			var load = function () { frame.src = frame.getAttribute( 'data-src' ); };
			frame.addEventListener( 'load', function () { if ( frame.src ) { frame.classList.add( 'is-loaded' ); } } );
			if ( 'IntersectionObserver' in w ) {
				var io = new IntersectionObserver( function ( en ) { if ( en[ 0 ].isIntersecting ) { io.disconnect(); load(); } }, { rootMargin: '300px 0px' } );
				io.observe( frame );
			} else { load(); }
		} );
	}

	function init( root ) {
		root = root || d;
		forms( root );
		branches( root );
		track( root );
		maps( root );
	}
	if ( d.readyState === 'loading' ) { d.addEventListener( 'DOMContentLoaded', function () { init(); } ); } else { init(); }
	d.addEventListener( 'qaf:refresh', function ( e ) { init( ( e.detail && e.detail.root ) || d ); } );
	w.addEventListener( 'elementor/frontend/init', function () {
		if ( w.elementorFrontend && elementorFrontend.hooks ) {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/global', function ( $scope ) { if ( $scope && $scope[ 0 ] ) { init( $scope[ 0 ] ); } } );
		}
	} );
} )();
