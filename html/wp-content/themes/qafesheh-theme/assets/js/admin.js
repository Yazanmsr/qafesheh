/**
 * admin.js — Theme Options UI + importer runner. (jQuery allowed here only: color picker & media.)
 */
( function ( $ ) {
	'use strict';

	var cfg = window.QAFAdmin || {};

	/* Tabs */
	$( document ).on( 'click', '.qaf-opts__tab[data-tab]', function () {
		var tab = $( this ).data( 'tab' );
		$( '.qaf-opts__tab' ).removeClass( 'is-active' );
		$( this ).addClass( 'is-active' );
		$( '.qaf-opts__pane' ).removeClass( 'is-active' ).filter( '[data-pane="' + tab + '"]' ).addClass( 'is-active' );
		$( 'input[name="qaf_active_tab"]' ).val( tab );
		var url = new URL( window.location.href );
		url.searchParams.set( 'tab', tab );
		url.searchParams.delete( 'qaf-saved' );
		url.searchParams.delete( 'qaf-reset' );
		window.history.replaceState( null, '', url.toString() );
	} );

	/* Language tabs */
	$( document ).on( 'click', '.qaf-i18n__tab', function () {
		var wrap = $( this ).closest( '.qaf-i18n' );
		var lang = $( this ).data( 'lang' );
		wrap.find( '.qaf-i18n__tab' ).removeClass( 'is-active' );
		$( this ).addClass( 'is-active' );
		wrap.find( '.qaf-i18n__pane' ).removeClass( 'is-active' ).filter( '[data-lang="' + lang + '"]' ).addClass( 'is-active' );
	} );

	/* Colour pickers */
	$( '.qaf-color' ).wpColorPicker();

	/* Range output */
	$( document ).on( 'input', '.qaf-range input', function () {
		$( this ).siblings( 'output' ).text( this.value );
	} );

	/* Media */
	var resolve = function ( v ) { return /^assets\//.test( v ) ? cfg.themeUri + v : v; };
	$( document ).on( 'click', '.qaf-media__pick', function ( e ) {
		e.preventDefault();
		var wrap = $( this ).closest( '.qaf-media' );
		var frame = wp.media( { title: cfg.i18n.pick, button: { text: cfg.i18n.use }, multiple: false, library: { type: 'image' } } );
		frame.on( 'select', function () {
			var att = frame.state().get( 'selection' ).first().toJSON();
			wrap.find( '.qaf-media__url' ).val( att.url ).trigger( 'change' );
		} );
		frame.open();
	} );
	$( document ).on( 'click', '.qaf-media__remove', function ( e ) {
		e.preventDefault();
		$( this ).closest( '.qaf-media' ).find( '.qaf-media__url' ).val( '' ).trigger( 'change' );
	} );
	$( document ).on( 'change input', '.qaf-media__url', function () {
		var wrap = $( this ).closest( '.qaf-media' );
		var v = $.trim( this.value );
		wrap.toggleClass( 'has-image', !! v );
		wrap.find( '.qaf-media__preview' ).html( v ? $( '<img alt="">' ).attr( 'src', resolve( v ) ) : '' );
	} );

	/* Reset confirm */
	$( document ).on( 'click', '.qaf-opts__reset', function ( e ) {
		if ( ! window.confirm( cfg.i18n.reset ) ) { e.preventDefault(); }
	} );

	/* ------------------------------------------------ Importer */
	function runStep( step ) {
		var row = $( '.qaf-import__step[data-step="' + step + '"]' );
		row.removeClass( 'is-done is-failed' ).addClass( 'is-running' );
		row.find( '.qaf-import__result' ).text( cfg.i18n.running );
		return $.post( cfg.ajax, { action: 'qaf_import_step', step: step, nonce: cfg.nonce, force: $( '#qaf-import-force' ).is( ':checked' ) ? 1 : 0 } )
			.then( function ( res ) {
				row.removeClass( 'is-running' );
				if ( res && res.success ) {
					row.addClass( 'is-done' ).find( '.qaf-import__result' ).text( res.data.summary );
					return true;
				}
				row.addClass( 'is-failed' ).find( '.qaf-import__result' ).text( cfg.i18n.failed + ': ' + ( res && res.data && res.data.message ? res.data.message : '' ) );
				return $.Deferred().reject().promise();
			}, function ( xhr ) {
				row.removeClass( 'is-running' ).addClass( 'is-failed' );
				var msg = xhr && xhr.responseJSON && xhr.responseJSON.data ? xhr.responseJSON.data.message : xhr.statusText;
				row.find( '.qaf-import__result' ).text( cfg.i18n.failed + ': ' + msg );
				return $.Deferred().reject().promise();
			} );
	}

	$( document ).on( 'click', '.qaf-import__run', function () {
		var btn = $( this ).prop( 'disabled', true );
		runStep( btn.data( 'step' ) ).always( function () { btn.prop( 'disabled', false ); } );
	} );

	$( '#qaf-import-all' ).on( 'click', function () {
		var btn = $( this ).prop( 'disabled', true );
		var steps = $( '.qaf-import__step' ).map( function () { return $( this ).data( 'step' ); } ).get();
		var bar = $( '.qaf-import__bar span' ).css( 'inline-size', '0' );
		var i = 0;
		var next = function () {
			if ( i >= steps.length ) {
				btn.prop( 'disabled', false ).text( cfg.i18n.done + ' ✓' );
				return;
			}
			runStep( steps[ i ] ).then( function () {
				i++;
				bar.css( 'inline-size', ( i / steps.length * 100 ) + '%' );
				next();
			}, function () { btn.prop( 'disabled', false ); } );
		};
		next();
	} );
} )( jQuery );

/* ------------------------------------------------ Gallery + repeater controls */
( function ( $ ) {
	'use strict';

	function syncGallery( wrap ) {
		var ids = wrap.find( '.qaf-gallery__list li' ).map( function () { return $( this ).data( 'id' ); } ).get();
		wrap.find( '.qaf-gallery__ids' ).val( ids.join( ',' ) );
	}

	function initSortables( ctx ) {
		if ( ! $.fn.sortable ) { return; }
		$( '.qaf-gallery__list', ctx ).each( function () {
			var list = $( this );
			if ( list.data( 'ui-sortable' ) ) { return; }
			list.sortable( { update: function () { syncGallery( list.closest( '.qaf-gallery' ) ); } } );
		} );
		$( '.qaf-repeater__rows', ctx ).each( function () {
			var rows = $( this );
			if ( rows.data( 'ui-sortable' ) ) { return; }
			rows.sortable( { handle: '.qaf-repeater__handle', update: function () { renumber( rows.closest( '.qaf-repeater' ) ); } } );
		} );
	}

	function renumber( rep ) {
		var base = rep.data( 'name' );
		rep.find( '> .qaf-repeater__rows > .qaf-repeater__row' ).each( function ( i ) {
			$( this ).find( '[name]' ).each( function () {
				var n = $( this ).attr( 'name' );
				if ( n.indexOf( base + '[' ) === 0 ) {
					$( this ).attr( 'name', n.replace( /^([^\[]+\[[^\]]+\])\[[^\]]+\]/, '$1[' + i + ']' ) );
				}
			} );
		} );
	}

	$( document ).on( 'click', '.qaf-gallery__add', function ( e ) {
		e.preventDefault();
		var wrap = $( this ).closest( '.qaf-gallery' );
		var frame = wp.media( { title: 'اختيار صور', button: { text: 'إضافة' }, multiple: 'add', library: { type: 'image' } } );
		frame.on( 'select', function () {
			frame.state().get( 'selection' ).each( function ( att ) {
				att = att.toJSON();
				var thumb = att.sizes && att.sizes.thumbnail ? att.sizes.thumbnail.url : att.url;
				wrap.find( '.qaf-gallery__list' ).append( $( '<li>' ).attr( 'data-id', att.id ).append( $( '<img alt="">' ).attr( 'src', thumb ), '<button type="button" class="qaf-gallery__x" aria-label="remove">×</button>' ) );
			} );
			syncGallery( wrap );
		} );
		frame.open();
	} );
	$( document ).on( 'click', '.qaf-gallery__x', function ( e ) {
		e.preventDefault();
		var wrap = $( this ).closest( '.qaf-gallery' );
		$( this ).closest( 'li' ).remove();
		syncGallery( wrap );
	} );

	$( document ).on( 'click', '.qaf-repeater__add', function ( e ) {
		e.preventDefault();
		var rep = $( this ).closest( '.qaf-repeater' );
		var rows = rep.children( '.qaf-repeater__rows' );
		var html = rep.children( '.qaf-repeater__tpl' ).html().split( '__i__' ).join( rows.children().length );
		var row = $( html ).appendTo( rows );
		row.find( '.qaf-color' ).wpColorPicker();
		renumber( rep );
	} );
	$( document ).on( 'click', '.qaf-repeater__remove', function ( e ) {
		e.preventDefault();
		var rep = $( this ).closest( '.qaf-repeater' );
		$( this ).closest( '.qaf-repeater__row' ).remove();
		renumber( rep );
	} );

	$( function () { initSortables( document ); } );
} )( jQuery );
