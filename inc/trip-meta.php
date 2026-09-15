<?php
/**
 * Trip meta boxes — admin UI for the schema in trip-fields.php.
 *
 * Hand-rolled rather than ACF: the theme carries no plugin dependencies, and
 * ACF's free tier has no repeater field (six of these field groups repeat).
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;

define( 'JJ_TRIP_NONCE', 'jj_trip_meta_nonce' );


/**
 * Register one meta box per schema group.
 */
function jj_trip_add_meta_boxes() {
    foreach ( jj_trip_schema() as $box_id => $box ) {
        add_meta_box(
            'jj-trip-' . $box_id,
            $box['title'],
            'jj_trip_render_meta_box',
            'trip',
            'normal',
            'high',
            [ 'box_id' => $box_id ]
        );
    }
}
add_action( 'add_meta_boxes_trip', 'jj_trip_add_meta_boxes' );


/**
 * Render a meta box from its schema group.
 *
 * @param WP_Post $post Current post.
 * @param array   $args Callback args carrying box_id.
 */
function jj_trip_render_meta_box( $post, $args ) {
    $box_id = $args['args']['box_id'];
    $schema = jj_trip_schema();

    if ( ! isset( $schema[ $box_id ] ) ) {
        return;
    }

    $box = $schema[ $box_id ];

    // One nonce for the whole screen is enough; print it with the first box.
    static $nonce_printed = false;
    if ( ! $nonce_printed ) {
        wp_nonce_field( JJ_TRIP_NONCE, JJ_TRIP_NONCE );
        $nonce_printed = true;
    }

    echo '<div class="jj-trip-box">';

    if ( ! empty( $box['desc'] ) ) {
        echo '<p class="jj-trip-box__desc">' . esc_html( $box['desc'] ) . '</p>';
    }

    foreach ( $box['fields'] as $key => $field ) {
        if ( 'repeater' === $field['type'] ) {
            jj_trip_render_repeater( $key, $field, $post->ID );
        } else {
            jj_trip_render_field( $key, $field, jj_trip_field( $key, $post->ID ) );
        }
    }

    echo '</div>';
}


/**
 * Render a single (non-repeater) field.
 *
 * @param string $name  Input name.
 * @param array  $field Field definition.
 * @param mixed  $value Current value.
 * @param string $id    Optional explicit id.
 */
function jj_trip_render_field( $name, $field, $value, $id = '' ) {
    $id    = $id ? $id : 'jj-' . sanitize_key( $name );
    $type  = $field['type'];
    $label = isset( $field['label'] ) ? $field['label'] : '';
    $desc  = isset( $field['desc'] ) ? $field['desc'] : '';
    $ph    = isset( $field['placeholder'] ) ? $field['placeholder'] : '';

    echo '<div class="jj-trip-field jj-trip-field--' . esc_attr( $type ) . '">';

    if ( $label && 'checkbox' !== $type ) {
        echo '<label class="jj-trip-field__label" for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';
    }

    switch ( $type ) {

        case 'textarea':
            printf(
                '<textarea id="%s" name="%s" rows="%d" class="widefat" placeholder="%s">%s</textarea>',
                esc_attr( $id ),
                esc_attr( $name ),
                isset( $field['rows'] ) ? absint( $field['rows'] ) : 4,
                esc_attr( $ph ),
                esc_textarea( $value )
            );
            break;

        case 'select':
            printf( '<select id="%s" name="%s" class="widefat">', esc_attr( $id ), esc_attr( $name ) );
            foreach ( $field['options'] as $opt_value => $opt_label ) {
                printf(
                    '<option value="%s" %s>%s</option>',
                    esc_attr( $opt_value ),
                    selected( $value, $opt_value, false ),
                    esc_html( $opt_label )
                );
            }
            echo '</select>';
            break;

        case 'checkbox':
            printf(
                '<label class="jj-trip-field__checkbox"><input type="checkbox" id="%s" name="%s" value="1" %s> %s</label>',
                esc_attr( $id ),
                esc_attr( $name ),
                checked( $value, '1', false ),
                esc_html( $label )
            );
            break;

        case 'meter':
            $meter_value = '' === $value ? 50 : absint( $value );
            printf(
                '<div class="jj-trip-meter"><input type="range" id="%s" name="%s" min="0" max="100" step="5" value="%d" class="jj-trip-meter__range"><output class="jj-trip-meter__out">%d</output></div>',
                esc_attr( $id ),
                esc_attr( $name ),
                $meter_value,
                $meter_value
            );
            break;

        case 'image':
            $attachment_id = absint( $value );
            $preview       = $attachment_id ? wp_get_attachment_image( $attachment_id, 'thumbnail' ) : '';
            echo '<div class="jj-trip-media" data-multiple="0">';
            printf(
                '<input type="hidden" name="%s" value="%s" class="jj-trip-media__input">',
                esc_attr( $name ),
                esc_attr( $attachment_id ? $attachment_id : '' )
            );
            echo '<div class="jj-trip-media__preview">' . $preview . '</div>';
            echo '<button type="button" class="button jj-trip-media__select">' . esc_html__( 'Select image', 'jaiye-journeys' ) . '</button> ';
            echo '<button type="button" class="button-link jj-trip-media__clear">' . esc_html__( 'Clear', 'jaiye-journeys' ) . '</button>';
            echo '</div>';
            break;

        case 'gallery':
            $ids      = array_filter( array_map( 'absint', explode( ',', (string) $value ) ) );
            $previews = '';
            foreach ( $ids as $attachment_id ) {
                $previews .= wp_get_attachment_image( $attachment_id, 'thumbnail' );
            }
            echo '<div class="jj-trip-media" data-multiple="1">';
            printf(
                '<input type="hidden" name="%s" value="%s" class="jj-trip-media__input">',
                esc_attr( $name ),
                esc_attr( implode( ',', $ids ) )
            );
            echo '<div class="jj-trip-media__preview">' . $previews . '</div>';
            echo '<button type="button" class="button jj-trip-media__select">' . esc_html__( 'Select images', 'jaiye-journeys' ) . '</button> ';
            echo '<button type="button" class="button-link jj-trip-media__clear">' . esc_html__( 'Clear', 'jaiye-journeys' ) . '</button>';
            echo '</div>';
            break;

        case 'number':
            printf(
                '<input type="number" id="%s" name="%s" value="%s" class="widefat" placeholder="%s">',
                esc_attr( $id ),
                esc_attr( $name ),
                esc_attr( $value ),
                esc_attr( $ph )
            );
            break;

        case 'url':
            printf(
                '<input type="url" id="%s" name="%s" value="%s" class="widefat" placeholder="%s">',
                esc_attr( $id ),
                esc_attr( $name ),
                esc_attr( $value ),
                esc_attr( $ph )
            );
            break;

        case 'text':
        default:
            printf(
                '<input type="text" id="%s" name="%s" value="%s" class="widefat" placeholder="%s">',
                esc_attr( $id ),
                esc_attr( $name ),
                esc_attr( $value ),
                esc_attr( $ph )
            );
            break;
    }

    if ( $desc ) {
        echo '<p class="jj-trip-field__desc description">' . esc_html( $desc ) . '</p>';
    }

    echo '</div>';
}


/**
 * Render a repeater field: existing rows plus a JS row template.
 *
 * @param string $key     Field key.
 * @param array  $field   Field definition.
 * @param int    $post_id Post ID.
 */
function jj_trip_render_repeater( $key, $field, $post_id ) {
    $rows      = jj_trip_rows( $key, $post_id );
    $row_label = isset( $field['row_label'] ) ? $field['row_label'] : __( 'Row', 'jaiye-journeys' );

    echo '<div class="jj-trip-repeater" data-key="' . esc_attr( $key ) . '" data-row-label="' . esc_attr( $row_label ) . '">';
    echo '<h4 class="jj-trip-repeater__title">' . esc_html( $field['label'] ) . '</h4>';

    if ( ! empty( $field['desc'] ) ) {
        echo '<p class="description">' . esc_html( $field['desc'] ) . '</p>';
    }

    echo '<div class="jj-trip-repeater__rows">';
    foreach ( $rows as $index => $row ) {
        jj_trip_render_repeater_row( $key, $field, $index, $row, $row_label );
    }
    echo '</div>';

    printf(
        '<button type="button" class="button jj-trip-repeater__add">%s</button>',
        esc_html( sprintf( /* translators: %s: row label */ __( 'Add %s', 'jaiye-journeys' ), $row_label ) )
    );

    // Row template for JS cloning. __INDEX__ is replaced on insert.
    echo '<script type="text/html" class="jj-trip-repeater__template">';
    jj_trip_render_repeater_row( $key, $field, '__INDEX__', [], $row_label );
    echo '</script>';

    echo '</div>';
}


/**
 * Render one repeater row.
 *
 * @param string     $key       Field key.
 * @param array      $field     Field definition.
 * @param int|string $index     Row index, or __INDEX__ for the template.
 * @param array      $row       Row values.
 * @param string     $row_label Human label for the row.
 */
function jj_trip_render_repeater_row( $key, $field, $index, $row, $row_label ) {
    echo '<div class="jj-trip-row">';
    echo '<div class="jj-trip-row__header">';
    echo '<span class="jj-trip-row__handle dashicons dashicons-menu" aria-hidden="true"></span>';
    echo '<strong class="jj-trip-row__label">' . esc_html( $row_label ) . '</strong>';
    echo '<button type="button" class="button-link jj-trip-row__remove" aria-label="' . esc_attr__( 'Remove row', 'jaiye-journeys' ) . '">' . esc_html__( 'Remove', 'jaiye-journeys' ) . '</button>';
    echo '</div>';
    echo '<div class="jj-trip-row__body">';

    foreach ( $field['subfields'] as $sub_key => $sub_field ) {
        $name  = sprintf( '%s[%s][%s]', $key, $index, $sub_key );
        $id    = sprintf( 'jj-%s-%s-%s', sanitize_key( $key ), $index, sanitize_key( $sub_key ) );
        $value = isset( $row[ $sub_key ] ) ? $row[ $sub_key ] : ( isset( $sub_field['default'] ) ? $sub_field['default'] : '' );
        jj_trip_render_field( $name, $sub_field, $value, $id );
    }

    echo '</div>';
    echo '</div>';
}


/**
 * Save all trip meta.
 *
 * @param int $post_id Post ID.
 */
function jj_trip_save_meta( $post_id ) {

    // Bail on autosave, revisions and bulk edits — none of them carry our fields.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( wp_is_post_revision( $post_id ) ) {
        return;
    }
    if ( ! isset( $_POST[ JJ_TRIP_NONCE ] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST[ JJ_TRIP_NONCE ] ) ), JJ_TRIP_NONCE ) ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    foreach ( jj_trip_schema() as $box ) {
        foreach ( $box['fields'] as $key => $field ) {

            $meta_key = JJ_TRIP_META_PREFIX . $key;

            /* ── Repeaters ─────────────────────────────────────────── */
            if ( 'repeater' === $field['type'] ) {

                if ( empty( $_POST[ $key ] ) || ! is_array( $_POST[ $key ] ) ) {
                    delete_post_meta( $post_id, $meta_key );
                    continue;
                }

                // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- sanitised per subfield below.
                $raw_rows = wp_unslash( $_POST[ $key ] );
                $clean    = [];

                foreach ( $raw_rows as $row ) {
                    if ( ! is_array( $row ) ) {
                        continue;
                    }

                    $clean_row = [];
                    $has_value = false;

                    foreach ( $field['subfields'] as $sub_key => $sub_field ) {
                        $raw_value = isset( $row[ $sub_key ] ) ? $row[ $sub_key ] : '';
                        $value     = jj_trip_sanitise_value( $raw_value, $sub_field['type'] );

                        $clean_row[ $sub_key ] = $value;

                        if ( '' !== trim( (string) $value ) ) {
                            $has_value = true;
                        }
                    }

                    // Skip rows the editor added but never filled in.
                    if ( $has_value ) {
                        $clean[] = $clean_row;
                    }
                }

                if ( empty( $clean ) ) {
                    delete_post_meta( $post_id, $meta_key );
                } else {
                    update_post_meta( $post_id, $meta_key, $clean );
                }

                continue;
            }

            /* ── Scalars ───────────────────────────────────────────── */

            // Unchecked checkboxes aren't submitted at all.
            if ( 'checkbox' === $field['type'] ) {
                update_post_meta( $post_id, $meta_key, empty( $_POST[ $key ] ) ? '' : '1' );
                continue;
            }

            if ( ! isset( $_POST[ $key ] ) ) {
                continue;
            }

            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- sanitised by type.
            $value = jj_trip_sanitise_value( wp_unslash( $_POST[ $key ] ), $field['type'] );

            if ( '' === $value ) {
                delete_post_meta( $post_id, $meta_key );
            } else {
                update_post_meta( $post_id, $meta_key, $value );
            }
        }
    }
}
add_action( 'save_post_trip', 'jj_trip_save_meta' );


/**
 * Admin assets for the meta box UI.
 *
 * @param string $hook Current admin page.
 */
function jj_trip_admin_assets( $hook ) {
    if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) {
        return;
    }

    $screen = get_current_screen();
    if ( ! $screen || 'trip' !== $screen->post_type ) {
        return;
    }

    wp_enqueue_media();

    wp_enqueue_style(
        'jj-trip-admin',
        get_template_directory_uri() . '/assets/admin/trip-meta.css',
        [],
        filemtime( get_template_directory() . '/assets/admin/trip-meta.css' )
    );

    wp_enqueue_script(
        'jj-trip-admin',
        get_template_directory_uri() . '/assets/admin/trip-meta.js',
        [ 'jquery' ],
        filemtime( get_template_directory() . '/assets/admin/trip-meta.js' ),
        true
    );
}
add_action( 'admin_enqueue_scripts', 'jj_trip_admin_assets' );
