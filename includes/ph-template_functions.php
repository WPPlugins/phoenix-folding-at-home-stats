<?php
/**
 * @param $template_name
 * @param string $template_path
 * @param string $default_path
 * @return mixed|void
 */

//template system shamelessly stolen from WooCommerce

if ( !function_exists( 'ph_locate_template' ) ) {
    function ph_locate_template( $template_name, $template_path = '', $default_path = '' )
    {
        if ( !$template_path ) {
            $template_path = PHOENIX_FOLDING_PLUGIN_FOLDERNAME;
        }

        if ( !$default_path ) {
            $default_path = PHOENIX_FOLDING_PLUGIN_PATH . '/templates/';
        }

        // Look within passed path within the theme - this is priority.
        $template = locate_template(
            array(
                trailingslashit( $template_path ) . $template_name,
                $template_name
            )
        );

        // Get default template/
        if ( !$template /*|| PHOENIX_TEMPLATE_DEBUG_MODE*/ ) {
            $template = $default_path . $template_name;
        }

        // Return what we found.
        return apply_filters( 'ph_folding_locate_template', $template, $template_name, $template_path );
    }
}
/**
 * Get other templates (e.g. product attributes) passing attributes and including the file.
 *
 * @access public
 * @param string $template_name
 * @param array $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 */
if ( !function_exists( 'ph_get_template' ) ) {

    function ph_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' )
    {
        if ( !empty( $args ) && is_array( $args ) ) {
            extract( $args );
        }

        $located = ph_locate_template( $template_name, $template_path, $default_path );

        if ( !file_exists( $located ) ) {
            _doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '2.1' );
            return;
        }

        // Allow 3rd party plugin filter template file from their plugin.
        $located = apply_filters( 'ph_folding_get_template', $located, $template_name, $args, $template_path, $default_path );

        do_action( 'phoenix_before_template_part', $template_name, $template_path, $located, $args );

        include( $located );

        do_action( 'phoenix_after_template_part', $template_name, $template_path, $located, $args );
    }
}
if ( !function_exists( 'ph_get_template_html' ) ) {
    function ph_get_template_html( $template_name, $args = array(), $template_path = '', $default_path = '' )
    {
        ob_start();
        ph_get_template( $template_name, $args, $template_path, $default_path );
        return ob_get_clean();
    }
}


if ( !function_exists( 'ph_format_date' ) ) {
    function ph_format_date( $date )
    {
        $formatted_date = !empty( $date ) ? date( "j F Y, g:i a", strtotime( $date ) ) : '';
        return apply_filters( 'ph_folding_format_date', $formatted_date, $date );
    }
}