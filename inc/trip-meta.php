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
            isset( $box['context'] ) ? $box['context'] : 'normal',
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

    if ( 'sales' === $box_id ) {
        jj_trip_render_live_status( $post->ID );
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

            // A value already stored that no longer matches any option — e.g.
            // the schema's choices changed since this was saved — gets its own
            // option so it stays visibly selected instead of silently falling
            // back to whatever option happens to be first. Otherwise the next
            // save (even an unrelated field) would quietly overwrite it.
            if ( '' !== (string) $value && ! isset( $field['options'][ $value ] ) ) {
                printf(
                    '<option value="%s" selected>%s</option>',
                    esc_attr( $value ),
                    esc_html( sprintf( /* translators: %s: the stored value */ __( '%s (previously saved; pick a current option to update)', 'jaiye-journeys' ), $value ) )
                );
            }

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

        case 'tags':
            $selected_keys = array_filter( array_map( 'trim', explode( ',', (string) $value ) ) );
            $max           = isset( $field['max'] ) ? (int) $field['max'] : 3;

            echo '<div class="jj-trip-tags" data-max="' . esc_attr( $max ) . '">';
            foreach ( $field['options'] as $tag_key => $definition ) {
                $checkbox_id = $id . '-' . sanitize_html_class( $tag_key );
                printf(
                    '<label class="jj-trip-tags__item" title="%s"><input type="checkbox" id="%s" name="%s[]" value="%s" %s> %s</label>',
                    esc_attr( $definition ),
                    esc_attr( $checkbox_id ),
                    esc_attr( $name ),
                    esc_attr( $tag_key ),
                    checked( in_array( $tag_key, $selected_keys, true ), true, false ),
                    esc_html( jj_vibe_tag_label( $tag_key ) )
                );
            }
            echo '</div>';
            printf(
                '<p class="jj-trip-tags__count description"><span class="jj-trip-tags__count-num">%d</span> / %d selected</p>',
                count( $selected_keys ),
                $max
            );
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

        case 'datetime':
            $date = DateTimeImmutable::createFromFormat( 'Y-m-d H:i', (string) $value, wp_timezone() );
            printf(
                '<input type="datetime-local" id="%s" name="%s" value="%s" class="widefat">',
                esc_attr( $id ),
                esc_attr( $name ),
                esc_attr( $date ? $date->format( 'Y-m-d\\TH:i' ) : '' )
            );
            break;

        case 'date':
            printf(
                '<input type="date" id="%s" name="%s" value="%s" class="widefat">',
                esc_attr( $id ),
                esc_attr( $name ),
                esc_attr( $value )
            );
            break;

        case 'money':
            printf(
                '<input type="number" id="%s" name="%s" value="%s" class="widefat" placeholder="%s" min="0" step="0.01" inputmode="decimal">',
                esc_attr( $id ),
                esc_attr( $name ),
                esc_attr( $value ),
                esc_attr( $ph )
            );
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

            // A checkbox-group of known tag keys, submitted as an array.
            // Re-validated against the central definitions and re-capped at
            // the field's max here — the admin's JS only disables further
            // clicks, it doesn't stop a crafted request.
            if ( 'tags' === $field['type'] ) {
                $submitted = isset( $_POST[ $key ] ) && is_array( $_POST[ $key ] )
                    ? array_map( 'sanitize_text_field', wp_unslash( $_POST[ $key ] ) )
                    : [];

                $known = array_keys( isset( $field['options'] ) ? $field['options'] : [] );
                $valid = array_slice( array_values( array_intersect( $submitted, $known ) ), 0, isset( $field['max'] ) ? (int) $field['max'] : 3 );

                if ( empty( $valid ) ) {
                    delete_post_meta( $post_id, $meta_key );
                } else {
                    update_post_meta( $post_id, $meta_key, implode( ',', $valid ) );
                }
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


/**
 * One-line summary of what visitors see now, and what changes next.
 *
 * Shared by the Sales Status box and the Trips list column so the two can
 * never describe the same trip differently.
 *
 * @param int $post_id Post ID.
 * @return string Plain text.
 */
function jj_trip_status_summary( $post_id ) {
    $status = jj_trip_status( $post_id );
    $now    = current_datetime();
    $open   = jj_trip_datetime( 'sales_open_at', $post_id );
    $close  = jj_trip_datetime( 'sales_close_at', $post_id );
    $format = get_option( 'date_format' ) . ', ' . get_option( 'time_format' );
    $next   = '';

    if ( jj_trip_is_presale( $post_id ) && $open ) {
        /* translators: %s: date and time */
        $next = sprintf( __( 'On sale %s', 'jaiye-journeys' ), wp_date( $format, $open->getTimestamp() ) );
    } elseif ( 'open' === $status && $close && $close > $now ) {
        /* translators: %s: date and time */
        $next = sprintf( __( 'Sold out from %s', 'jaiye-journeys' ), wp_date( $format, $close->getTimestamp() ) );
    }

    return $next ? jj_trip_status_label( $status ) . ' · ' . $next : jj_trip_status_label( $status );
}


/**
 * "Right now visitors see …" line at the top of the Sales Status box.
 *
 * Reflects the last save, not unsaved edits in the form below it.
 *
 * @param int $post_id Post ID.
 */
function jj_trip_render_live_status( $post_id ) {
    printf(
        '<p class="jj-trip-live jj-trip-live--%s"><strong>%s</strong> %s</p>',
        esc_attr( jj_trip_status( $post_id ) ),
        esc_html__( 'Live now:', 'jaiye-journeys' ),
        esc_html( jj_trip_status_summary( $post_id ) )
    );
    printf(
        '<p class="description">%s</p>',
        esc_html(
            sprintf(
                /* translators: %s: timezone name */
                __( 'Times are in the site timezone (%s), set under Settings → General.', 'jaiye-journeys' ),
                wp_timezone_string()
            )
        )
    );
}


/**
 * Sales Status column on the Trips list, beside the Date column.
 *
 * @param array $columns List table columns.
 * @return array
 */
function jj_trip_admin_columns( $columns ) {
    $out = [];
    foreach ( $columns as $key => $label ) {
        if ( 'date' === $key ) {
            $out['jj_sales_status'] = __( 'Sales Status', 'jaiye-journeys' );
        }
        $out[ $key ] = $label;
    }
    return $out;
}
add_filter( 'manage_trip_posts_columns', 'jj_trip_admin_columns' );


/**
 * Render the Sales Status column.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 */
function jj_trip_admin_column_content( $column, $post_id ) {
    if ( 'jj_sales_status' !== $column ) {
        return;
    }

    // data-jj-status carries the saved value (not the live one, which a
    // "Go on sale at" time may have moved on) for Quick Edit to preselect.
    printf(
        '<span class="jj-trip-live jj-trip-live--%s" data-jj-status="%s">%s</span>',
        esc_attr( jj_trip_status( $post_id ) ),
        esc_attr( jj_trip_field( 'status', $post_id, 'open' ) ),
        esc_html( jj_trip_status_summary( $post_id ) )
    );
}
add_action( 'manage_trip_posts_custom_column', 'jj_trip_admin_column_content', 10, 2 );


/**
 * Sales Status in Quick Edit and Bulk Edit on the Trips list.
 *
 * Both rows live inside the same list form, so they use different field
 * names and the save handler only reads the one for the action actually
 * submitted — otherwise a bulk edit would also send the hidden Quick Edit
 * select and overwrite every ticked trip with it.
 *
 * @param string $column    Column key.
 * @param string $post_type Post type.
 */
function jj_trip_inline_edit_box( $column, $post_type ) {
    if ( 'jj_sales_status' !== $column || 'trip' !== $post_type ) {
        return;
    }

    $bulk    = ( 'bulk_edit_custom_box' === current_filter() );
    $fields  = jj_trip_all_fields();
    $options = $fields['status']['options'];

    if ( $bulk ) {
        $options = [ '' => __( 'No change', 'jaiye-journeys' ) ] + $options;
    }

    wp_nonce_field( 'jj_trip_inline_status', 'jj_trip_inline_status_nonce' );
    ?>
    <fieldset class="inline-edit-col-right">
      <div class="inline-edit-col">
        <label class="inline-edit-status">
          <span class="title"><?php esc_html_e( 'Sales Status', 'jaiye-journeys' ); ?></span>
          <select name="<?php echo esc_attr( $bulk ? 'jj_trip_bulk_status' : 'jj_trip_quick_status' ); ?>">
            <?php foreach ( $options as $value => $label ) : ?>
              <option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
            <?php endforeach; ?>
          </select>
        </label>
      </div>
    </fieldset>
    <?php
}
add_action( 'quick_edit_custom_box', 'jj_trip_inline_edit_box', 10, 2 );
add_action( 'bulk_edit_custom_box', 'jj_trip_inline_edit_box', 10, 2 );


/**
 * Save Sales Status from Quick Edit or Bulk Edit. Touches nothing else.
 *
 * @param int $post_id Post ID.
 */
function jj_trip_save_inline_status( $post_id ) {
    if ( wp_is_post_revision( $post_id ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
        return;
    }
    if ( ! isset( $_REQUEST['jj_trip_inline_status_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_REQUEST['jj_trip_inline_status_nonce'] ) ), 'jj_trip_inline_status' ) ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_REQUEST['bulk_edit'] ) ) {
        $key = 'jj_trip_bulk_status';
    } elseif ( isset( $_POST['action'] ) && 'inline-save' === $_POST['action'] ) {
        $key = 'jj_trip_quick_status';
    } else {
        return;
    }

    $value  = isset( $_REQUEST[ $key ] ) ? sanitize_key( wp_unslash( $_REQUEST[ $key ] ) ) : '';
    $fields = jj_trip_all_fields();

    // Blank is Bulk Edit's "No change"; anything not a real option is ignored.
    if ( '' === $value || ! isset( $fields['status']['options'][ $value ] ) ) {
        return;
    }

    update_post_meta( $post_id, JJ_TRIP_META_PREFIX . 'status', $value );
}
add_action( 'save_post_trip', 'jj_trip_save_inline_status' );


/**
 * Preselect the trip's saved status when Quick Edit opens.
 *
 * @param string $hook Current admin page.
 */
function jj_trip_inline_edit_script( $hook ) {
    $screen = get_current_screen();
    if ( 'edit.php' !== $hook || ! $screen || 'trip' !== $screen->post_type ) {
        return;
    }

    wp_add_inline_script(
        'inline-edit-post',
        "jQuery( function ( $ ) {
            if ( typeof inlineEditPost === 'undefined' ) { return; }
            var coreEdit = inlineEditPost.edit;
            inlineEditPost.edit = function ( id ) {
                coreEdit.apply( this, arguments );
                var postId = typeof id === 'object' ? parseInt( this.getId( id ), 10 ) : parseInt( id, 10 );
                if ( ! postId ) { return; }
                var status = $( '#post-' + postId ).find( '.jj-trip-live' ).data( 'jjStatus' );
                $( '#edit-' + postId ).find( 'select[name=\"jj_trip_quick_status\"]' ).val( status || 'open' );
            };
        } );"
    );
}
add_action( 'admin_enqueue_scripts', 'jj_trip_inline_edit_script' );


/**
 * Status colours for the Trips list.
 *
 * The edit screen gets these from trip-meta.css; the list screen doesn't load
 * that file, and a few lines don't justify a second stylesheet.
 */
function jj_trip_admin_list_styles() {
    $screen = get_current_screen();
    if ( ! $screen || 'edit-trip' !== $screen->id ) {
        return;
    }
    ?>
    <style>
      .jj-trip-live { font-weight: 600; }
      .jj-trip-live--announced,
      .jj-trip-live--coming-soon { color: #996800; }
      .jj-trip-live--open { color: #00753a; }
      .jj-trip-live--sold-out { color: #b32d2e; }
    </style>
    <?php
}
add_action( 'admin_head', 'jj_trip_admin_list_styles' );


/**
 * One-off review when the Coming soon status arrived.
 *
 * Every pre-existing pre-sale trip was saved as Waitlist. Trips with no Day by
 * Day itinerary would now show a thin teaser, so this lists them on the Trips
 * screen for the owner to move to Coming soon. Nothing changes until they tick
 * trips and submit; submitting (even with nothing ticked) closes the review.
 * Marrakesh is never pre-ticked — it stays on Waitlist by request.
 */
define( 'JJ_TRIP_PRESALE_REVIEW', 'jj_trip_presale_review_done' );

/**
 * Waitlist trips with no Day by Day rows.
 *
 * @return WP_Post[]
 */
function jj_trip_presale_review_candidates() {
    $trips = get_posts( [
        'post_type'      => 'trip',
        'post_status'    => [ 'publish', 'draft', 'pending', 'private', 'future' ],
        'posts_per_page' => -1,
        'meta_key'       => JJ_TRIP_META_PREFIX . 'status', // phpcs:ignore WordPress.DB.SlowDBQuery -- admin-only, a handful of trips.
        'meta_value'     => 'coming-soon',                  // phpcs:ignore WordPress.DB.SlowDBQuery
    ] );

    return array_values( array_filter( $trips, function ( $trip ) {
        return ! jj_trip_rows( 'days', $trip->ID );
    } ) );
}

/**
 * Show the review panel above the Trips list.
 */
function jj_trip_presale_review_notice() {
    $screen = get_current_screen();
    if ( ! $screen || 'edit-trip' !== $screen->id || get_option( JJ_TRIP_PRESALE_REVIEW ) || ! current_user_can( 'edit_posts' ) ) {
        return;
    }

    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display-only flag set by our own redirect.
    if ( isset( $_GET['jj_presale_switched'] ) ) {
        return;
    }

    $candidates = jj_trip_presale_review_candidates();
    if ( ! $candidates ) {
        return;
    }
    ?>
    <div class="notice notice-warning">
      <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" name="action" value="jj_trip_presale_review">
        <?php wp_nonce_field( 'jj_trip_presale_review' ); ?>
        <p><strong><?php esc_html_e( 'New status: Coming soon', 'jaiye-journeys' ); ?></strong></p>
        <p><?php esc_html_e( 'Waitlist pages now show the full trip page, without prices or booking. These Waitlist trips have no Day by Day itinerary yet. Tick the ones to move to Coming soon (the short page they show today). Unticked trips stay on Waitlist.', 'jaiye-journeys' ); ?></p>
        <ul>
          <?php foreach ( $candidates as $trip ) : ?>
            <?php $keep = ( false !== stripos( $trip->post_name . ' ' . $trip->post_title, 'marrake' ) ); ?>
            <li>
              <label>
                <input type="checkbox" name="jj_trip_ids[]" value="<?php echo esc_attr( $trip->ID ); ?>" <?php checked( ! $keep ); ?>>
                <?php echo esc_html( get_the_title( $trip ) ); ?>
                <?php if ( $keep ) : ?>
                  <em><?php esc_html_e( '(stays on Waitlist unless you tick it)', 'jaiye-journeys' ); ?></em>
                <?php endif; ?>
              </label>
            </li>
          <?php endforeach; ?>
        </ul>
        <p><button type="submit" class="button button-primary"><?php esc_html_e( 'Move ticked trips to Coming soon', 'jaiye-journeys' ); ?></button></p>
      </form>
    </div>
    <?php
}
add_action( 'admin_notices', 'jj_trip_presale_review_notice' );

/**
 * Apply the review: move ticked Waitlist trips to Coming soon.
 */
function jj_trip_presale_review_save() {
    check_admin_referer( 'jj_trip_presale_review' );

    $ids   = isset( $_POST['jj_trip_ids'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['jj_trip_ids'] ) ) : [];
    $moved = 0;

    foreach ( $ids as $id ) {
        // Re-checked here: only a trip still saved as Waitlist is touched.
        if ( 'trip' !== get_post_type( $id ) || ! current_user_can( 'edit_post', $id ) ) {
            continue;
        }
        if ( 'coming-soon' !== get_post_meta( $id, JJ_TRIP_META_PREFIX . 'status', true ) ) {
            continue;
        }
        update_post_meta( $id, JJ_TRIP_META_PREFIX . 'status', 'announced' );
        $moved++;
    }

    update_option( JJ_TRIP_PRESALE_REVIEW, current_time( 'mysql' ), false );

    wp_safe_redirect( add_query_arg( [ 'post_type' => 'trip', 'jj_presale_switched' => $moved ], admin_url( 'edit.php' ) ) );
    exit;
}
add_action( 'admin_post_jj_trip_presale_review', 'jj_trip_presale_review_save' );

/**
 * Confirm how many trips moved.
 */
function jj_trip_presale_review_done_notice() {
    $screen = get_current_screen();
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display-only.
    if ( ! $screen || 'edit-trip' !== $screen->id || ! isset( $_GET['jj_presale_switched'] ) ) {
        return;
    }

    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $moved = absint( $_GET['jj_presale_switched'] );
    printf(
        '<div class="notice notice-success is-dismissible"><p>%s</p></div>',
        esc_html(
            sprintf(
                /* translators: %d: number of trips */
                _n( '%d trip moved to Coming soon.', '%d trips moved to Coming soon.', $moved, 'jaiye-journeys' ),
                $moved
            )
        )
    );
}
add_action( 'admin_notices', 'jj_trip_presale_review_done_notice' );
