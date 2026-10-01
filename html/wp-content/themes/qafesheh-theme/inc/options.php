<?php
/**
 * Options API — defaults, getters, i18n, languages, sanitiser.
 * Everything is stored in ONE option: qaf_options.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Flat field map key => field (static cache). Notes and headings excluded.
 *
 * @return array
 */
function qaf_options_fields() {
	static $fields = null;
	if ( null === $fields ) {
		$fields = array();
		foreach ( qaf_options_schema() as $tab ) {
			foreach ( $tab['fields'] as $key => $field ) {
				if ( in_array( $field['type'], array( 'note', 'heading' ), true ) ) {
					continue;
				}
				$fields[ $key ] = $field;
			}
		}
	}
	return $fields;
}

/**
 * Base-language defaults.
 *
 * @return array
 */
function qaf_options_defaults() {
	static $defaults = null;
	if ( null === $defaults ) {
		$defaults = array();
		foreach ( qaf_options_fields() as $key => $field ) {
			$defaults[ $key ] = $field['default'] ?? ( 'repeater' === $field['type'] ? array() : '' );
		}
	}
	return $defaults;
}

/**
 * Stored options merged over defaults (static cache, reset on save).
 *
 * @param bool $reset Clear cache.
 * @return array
 */
function qaf_options( $reset = false ) {
	static $opts = null;
	if ( $reset ) {
		$opts = null;
	}
	if ( null === $opts ) {
		$stored = get_option( 'qaf_options', array() );
		$opts   = array_merge( qaf_options_defaults(), is_array( $stored ) ? $stored : array() );
	}
	return $opts;
}

/**
 * Read an option. Empty string / null falls back to the default.
 *
 * @param string $key     Key.
 * @param mixed  $default Fallback when no schema default.
 * @return mixed
 */
function qaf_opt( $key, $default = null ) {
	$opts  = qaf_options();
	$value = $opts[ $key ] ?? null;

	if ( null === $value || '' === $value ) {
		$field = qaf_options_fields()[ $key ] ?? array();
		return $field['default'] ?? $default;
	}
	return $value;
}

/**
 * Per-language option: key__{lang} → schema default__{lang} → base.
 *
 * @param string $key Key.
 * @return mixed
 */
function qaf_opt_i18n( $key ) {
	$lang = qaf_current_lang();

	if ( $lang !== qaf_default_lang() ) {
		$opts  = qaf_options();
		$value = $opts[ $key . '__' . $lang ] ?? '';
		if ( '' !== $value && null !== $value ) {
			return $value;
		}
		$field = qaf_options_fields()[ $key ] ?? array();
		if ( isset( $field[ 'default__' . $lang ] ) ) {
			return $field[ 'default__' . $lang ];
		}
	}
	return qaf_opt( $key );
}

/**
 * Escaped translated text option.
 *
 * @param string $key Key.
 * @return string
 */
function qaf_text( $key ) {
	return esc_html( (string) qaf_opt_i18n( $key ) );
}

/**
 * URL option — resolves theme-relative (assets/…) and site-relative (/path) values.
 *
 * @param string $key  Key.
 * @param bool   $i18n Per-language.
 * @return string
 */
function qaf_opt_url( $key, $i18n = false ) {
	$value = (string) ( $i18n ? qaf_opt_i18n( $key ) : qaf_opt( $key ) );
	return qaf_resolve_url( $value );
}

/**
 * Resolve a stored URL value.
 *
 * @param string $value Raw.
 * @return string
 */
function qaf_resolve_url( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}
	if ( 0 === strpos( $value, 'assets/' ) ) {
		return QAF_URI . $value;
	}
	if ( '/' === $value[0] && ( ! isset( $value[1] ) || '/' !== $value[1] ) ) {
		return home_url( $value );
	}
	return $value;
}

/* ================================================= Languages */

/**
 * Current language code (WPML → Polylang → locale).
 *
 * @return string
 */
function qaf_current_lang() {
	$lang = apply_filters( 'wpml_current_language', null );
	if ( ! $lang && function_exists( 'pll_current_language' ) ) {
		$lang = pll_current_language();
	}
	if ( ! $lang || 'all' === $lang ) {
		$lang = substr( determine_locale(), 0, 2 );
	}
	return sanitize_key( $lang );
}

/**
 * Default language code.
 *
 * @return string
 */
function qaf_default_lang() {
	$lang = apply_filters( 'wpml_default_language', null );
	if ( ! $lang && function_exists( 'pll_default_language' ) ) {
		$lang = pll_default_language();
	}
	if ( ! $lang ) {
		$lang = substr( get_locale(), 0, 2 );
	}
	return sanitize_key( $lang );
}

/**
 * Active languages: code => [ code, name, url, active ].
 *
 * @return array
 */
function qaf_active_languages() {
	$out   = array();
	$langs = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );

	if ( is_array( $langs ) && $langs ) {
		foreach ( $langs as $l ) {
			$out[ $l['code'] ] = array(
				'code'   => $l['code'],
				'name'   => $l['native_name'] ?? $l['code'],
				'url'    => $l['url'] ?? home_url( '/' ),
				'active' => ! empty( $l['active'] ),
			);
		}
	} elseif ( function_exists( 'pll_the_languages' ) ) {
		foreach ( (array) pll_the_languages( array( 'raw' => 1 ) ) as $l ) {
			$out[ $l['slug'] ] = array(
				'code'   => $l['slug'],
				'name'   => $l['name'],
				'url'    => $l['url'],
				'active' => ! empty( $l['current_lang'] ),
			);
		}
	}

	if ( ! $out ) {
		$code         = qaf_current_lang();
		$out[ $code ] = array(
			'code'   => $code,
			'name'   => strtoupper( $code ),
			'url'    => home_url( '/' ),
			'active' => true,
		);
	}
	return $out;
}

/**
 * Language codes the options panel shows inputs for (always includes en for this project).
 *
 * @return string[]
 */
function qaf_panel_languages() {
	$codes = array_keys( qaf_active_languages() );
	$def   = qaf_default_lang();
	if ( ! in_array( 'en', $codes, true ) && 'en' !== $def ) {
		$codes[] = 'en';
	}
	$codes = array_values( array_unique( array_merge( array( $def ), $codes ) ) );
	return $codes;
}

/* ================================================= Sanitiser */

/**
 * Sanitise one value by field type.
 *
 * @param mixed $value Raw.
 * @param array $field Field.
 * @return mixed
 */
function qaf_sanitize_field( $value, $field ) {
	switch ( $field['type'] ) {
		case 'switch':
			return empty( $value ) ? 0 : 1;
		case 'number':
		case 'range':
			$num = is_numeric( $value ) ? $value + 0 : ( $field['default'] ?? 0 );
			if ( isset( $field['min'] ) ) {
				$num = max( $field['min'], $num );
			}
			if ( isset( $field['max'] ) ) {
				$num = min( $field['max'], $num );
			}
			return $num;
		case 'color':
			$hex = sanitize_hex_color( (string) $value );
			return $hex ? $hex : ( $field['default'] ?? '' );
		case 'image':
		case 'url':
			$value = trim( (string) $value );
			if ( 'image' === $field['type'] && ctype_digit( $value ) ) {
				return absint( $value );
			}
			if ( 0 === strpos( $value, 'assets/' ) ) {
				return sanitize_text_field( $value );
			}
			if ( '' !== $value && '/' === $value[0] ) {
				return esc_url_raw( home_url( $value ) ) ? sanitize_text_field( $value ) : '';
			}
			return esc_url_raw( $value );
		case 'email':
			return sanitize_email( (string) $value );
		case 'gallery':
			return implode( ',', array_filter( array_map( 'absint', explode( ',', (string) $value ) ) ) );
		case 'select':
			$value = (string) $value;
			return array_key_exists( $value, $field['options'] ?? array() ) ? $value : ( $field['default'] ?? '' );
		case 'textarea':
			return sanitize_textarea_field( (string) $value );
		case 'repeater':
			$rows = array();
			foreach ( (array) $value as $row ) {
				if ( ! is_array( $row ) ) {
					continue;
				}
				$clean = array();
				foreach ( $field['fields'] as $sub_key => $sub ) {
					$clean[ $sub_key ] = qaf_sanitize_field( $row[ $sub_key ] ?? '', $sub );
					if ( ! empty( $sub['i18n'] ) ) {
						foreach ( qaf_panel_languages() as $lang ) {
							if ( isset( $row[ $sub_key . '__' . $lang ] ) ) {
								$clean[ $sub_key . '__' . $lang ] = qaf_sanitize_field( $row[ $sub_key . '__' . $lang ], $sub );
							}
						}
					}
				}
				if ( array_filter( $clean, static fn( $v ) => '' !== $v && null !== $v && 0 !== $v ) ) {
					$rows[] = $clean;
				}
			}
			return $rows;
		default:
			return sanitize_text_field( (string) $value );
	}
}

/**
 * Sanitise the whole options array from the schema.
 * Keeps language variants of languages not currently active.
 *
 * @param array $input Raw POST.
 * @return array
 */
function qaf_sanitize_options( $input ) {
	$input    = is_array( $input ) ? $input : array();
	$previous = get_option( 'qaf_options', array() );
	$previous = is_array( $previous ) ? $previous : array();
	$output   = array();

	foreach ( qaf_options_fields() as $key => $field ) {
		$output[ $key ] = qaf_sanitize_field( $input[ $key ] ?? ( 'switch' === $field['type'] ? 0 : '' ), $field );

		if ( ! empty( $field['i18n'] ) ) {
			foreach ( $previous as $prev_key => $prev_val ) {
				if ( 0 === strpos( $prev_key, $key . '__' ) ) {
					$output[ $prev_key ] = $prev_val;
				}
			}
			foreach ( qaf_panel_languages() as $lang ) {
				if ( isset( $input[ $key . '__' . $lang ] ) ) {
					$output[ $key . '__' . $lang ] = qaf_sanitize_field( $input[ $key . '__' . $lang ], $field );
				}
			}
		}
	}

	foreach ( array( 'slug_company', 'slug_service', 'slug_branch', 'slug_job' ) as $slug_key ) {
		$output[ $slug_key ] = sanitize_title( $output[ $slug_key ] );
		if ( ( $previous[ $slug_key ] ?? '' ) !== $output[ $slug_key ] ) {
			update_option( 'qaf_flush_rewrite', 1, false );
		}
	}

	return $output;
}
