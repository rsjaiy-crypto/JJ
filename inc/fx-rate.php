<?php
/**
 * GBP → USD reference rate, cached and refreshed daily.
 *
 * Powers a single, muted "approx $X USD" note next to a trip's price —
 * nothing transactional. Actual payment currency is decided at checkout
 * (Stripe), entirely separate from this. If this rate is ever stale or
 * unavailable, the display should degrade to GBP-only rather than guess.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;

define( 'JJ_FX_RATE_OPTION', 'jj_fx_gbp_usd_rate' );
define( 'JJ_FX_UPDATED_OPTION', 'jj_fx_gbp_usd_updated' );
define( 'JJ_FX_CRON_HOOK', 'jj_fx_refresh_rate' );

/** A rate this old is treated as no rate at all — see jj_fx_gbp_usd_rate(). */
define( 'JJ_FX_MAX_AGE', 3 * DAY_IN_SECONDS );


/**
 * Schedule the daily refresh, and do an immediate one-off fetch the very
 * first time this runs (so the first visitor after deploy gets a real rate
 * today, rather than waiting for tomorrow's cron tick).
 *
 * WP-Cron only fires on a site visit rather than a true system clock, which
 * is the practical ceiling on a host reached over SFTP with no shell cron
 * access — fine for a once-a-day rate that only needs to be roughly current.
 */
function jj_fx_schedule_refresh() {
    if ( ! wp_next_scheduled( JJ_FX_CRON_HOOK ) ) {
        wp_schedule_event( time(), 'daily', JJ_FX_CRON_HOOK );
    }

    if ( false === get_option( JJ_FX_RATE_OPTION, false ) ) {
        jj_fx_refresh_rate();
    }
}
add_action( 'wp', 'jj_fx_schedule_refresh' );
add_action( JJ_FX_CRON_HOOK, 'jj_fx_refresh_rate' );


/**
 * Clear the scheduled event if this theme is ever switched away from, so it
 * doesn't keep firing forever with nothing left to use its result.
 */
function jj_fx_clear_schedule() {
    $timestamp = wp_next_scheduled( JJ_FX_CRON_HOOK );
    if ( $timestamp ) {
        wp_unschedule_event( $timestamp, JJ_FX_CRON_HOOK );
    }
}
add_action( 'switch_theme', 'jj_fx_clear_schedule' );


/**
 * Fetch the current GBP→USD rate and cache it.
 *
 * Uses frankfurter.app — free, no API key, backed by European Central Bank
 * reference rates. On any failure the previously cached rate is left
 * untouched; jj_fx_gbp_usd_rate() is what decides whether a stale rate is
 * still safe to show.
 */
function jj_fx_refresh_rate() {
    $response = wp_remote_get( 'https://api.frankfurter.dev/v1/latest?from=GBP&to=USD', [
        'timeout' => 8,
    ] );

    if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
        return;
    }

    $body = json_decode( wp_remote_retrieve_body( $response ), true );
    $rate = isset( $body['rates']['USD'] ) ? (float) $body['rates']['USD'] : 0;

    if ( $rate <= 0 ) {
        return;
    }

    update_option( JJ_FX_RATE_OPTION, $rate, false );
    update_option( JJ_FX_UPDATED_OPTION, time(), false );
}


/**
 * The cached GBP→USD rate, or 0 if there isn't a trustworthy one.
 *
 * Returns 0 (rather than a guessed fallback number) when nothing has been
 * fetched yet or the cache has gone stale beyond JJ_FX_MAX_AGE — e.g. the
 * API has been failing for days. Callers should treat 0 as "don't show a
 * USD estimate right now" rather than inventing a rate.
 *
 * @return float
 */
function jj_fx_gbp_usd_rate() {
    $updated = (int) get_option( JJ_FX_UPDATED_OPTION, 0 );

    if ( ! $updated || ( time() - $updated ) > JJ_FX_MAX_AGE ) {
        return 0.0;
    }

    return (float) get_option( JJ_FX_RATE_OPTION, 0 );
}


/**
 * Format a GBP amount as an approximate USD string for display.
 *
 * Rounded to the nearest $50 — an exact-to-the-cent conversion on a "from"
 * price reads as false precision it can't actually deliver.
 *
 * @param int|float $gbp_amount Amount in GBP.
 * @return string e.g. "$4,100", or '' if there's no trustworthy rate right now.
 */
function jj_fx_format_usd_estimate( $gbp_amount ) {
    $rate = jj_fx_gbp_usd_rate();

    if ( $rate <= 0 || ! $gbp_amount ) {
        return '';
    }

    $rounded = (int) round( ( (float) $gbp_amount * $rate ) / 50 ) * 50;

    return '$' . number_format_i18n( $rounded );
}
