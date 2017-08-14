<?php

// Creating the widget
class Ph_Folding_Widget extends WP_Widget
{
    function __construct()
    {
        parent::__construct( 'phoenix_folding_widget', __( 'Phoenix Folding@Home Widget', 'ph_folding' ),
            array(
                'description' => __( 'Display stats for your folding@home donor account or team', 'ph_folding' ),
                'classname' => 'ph_folding-widget'
            )
        );
        add_action( 'wp_enqueue_scripts', array( &$this, 'queue_css' ), 101 ); //after register in main class
    }

    public function queue_css()
    {
        if ( is_active_widget( false, false, $this->id_base, true ) ) {
            wp_enqueue_style( 'phoenix-folding' );
        }
    }

    // widget front-end
    public function widget( $args, $instance )
    {
        //print_r($instance);
        // before and after widget arguments are defined by themes
        echo $args[ 'before_widget' ];
        if ( !empty( $instance[ 'id' ] ) && !empty( $instance[ 'type' ] ) ) {
            $title = apply_filters( 'widget_title', $instance[ 'title' ] );
            if ( !empty( $title ) )
                echo $args[ 'before_title' ] . $title . $args[ 'after_title' ];
            $show_donor_teams_team_donors = false;
            if ( !empty( $instance[ 'show_donor_teams_team_donors' ] ) )
                $show_donor_teams_team_donors = true;
            $show_logo = false;
            if ( !empty( $instance[ 'show_logo' ] ) && $instance[ 'show_logo' ] )
                $show_logo = true;
            $show_tagline = false;
            if ( !empty( $instance[ 'show_tagline' ] ) && $instance[ 'show_tagline' ] )
                $show_tagline = true;
            $atts = array(
                'class' => $instance[ 'class' ],
                'show_id' => $instance[ 'show_id' ],
                'show_team_donors' => $show_donor_teams_team_donors,
                'show_donor_teams' => $show_donor_teams_team_donors,
                'show_logo' => $show_logo,
                'show_tagline' => $show_tagline
            );
            $folding_stats = new Ph_Folding_Stats( $instance[ 'id' ], $instance[ 'type' ] );
            echo apply_filters( 'ph_folding_display_table', $folding_stats->display_table( $atts ), $atts );
        }
        echo $args[ 'after_widget' ];
    }

// Widget Backend
    public function form( $instance )
    {
        $title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : __( 'Folding@Home Stats', 'ph_folding' );
        $type = isset( $instance[ 'type' ] ) ? $instance[ 'type' ] : '';
        if ( isset( $instance[ 'id' ] ) ) {
            $id = is_numeric( $instance[ 'id' ] ) ? absint( $instance[ 'id' ] ) : $instance[ 'id' ];
        } else $id = 1;
        $show_id = isset( $instance[ 'show_id' ] ) ? absint( $instance[ 'show_id' ] ) : false;
        $show_donor_teams_team_donors = isset( $instance[ 'show_donor_teams_team_donors' ] ) ? (bool)$instance[ 'show_donor_teams_team_donors' ] : true;
        $show_logo = isset( $instance[ 'show_logo' ] ) ? (bool)$instance[ 'show_logo' ] : true;
        $show_tagline = isset( $instance[ 'show_tagline' ] ) ? (bool)$instance[ 'show_tagline' ] : true;

        $class = isset( $instance[ 'class' ] ) ? $instance[ 'class' ] : '';

        $type_options = array( 'donor' => __( 'Donor', 'ph_folding' ), 'team' => __( 'Team', 'ph_folding' ) );
        // Widget admin form
        // title, select team or donor, id, display donor's teams?
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'ph-folding' ); ?>:</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
                   name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
                   value="<?php echo esc_attr( $title ); ?>"/>
        </p>
        <p>
            <label
                for="<?php echo $this->get_field_id( 'type' ); ?>"><?php _e( 'Display donor or team', 'ph-folding' ); ?>
                :</label>
            <select id="<?php echo $this->get_field_id( 'type' ); ?>"
                    name="<?php echo $this->get_field_name( 'type' ); ?>">
                <option value="0"><?php _e( '&mdash; Select &mdash;' ); ?></option>
                <?php foreach ( $type_options as $option => $label ) : ?>
                    <option value="<?php echo $option; ?>" <?php selected( $type, $option ); ?>>
                        <?php echo $label; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <p><label
                for="<?php echo $this->get_field_id( 'id' ); ?>"><?php _e( 'Team/donor ID. If showing a donor you can write their username', 'ph-folding' ); ?>
                :</label>
            <input class="regular-text" id="<?php echo $this->get_field_id( 'id' ); ?>"
                   name="<?php echo $this->get_field_name( 'id' ); ?>" type="text"
                   value="<?php echo $id; ?>" size="3"/>
        </p>
        <p>
            <input class="checkbox" type="checkbox"<?php checked( $show_id ); ?>
                   id="<?php echo $this->get_field_id( 'show_id' ); ?>"
                   name="<?php echo $this->get_field_name( 'show_id' ); ?>"/>
            <label
                for="<?php echo $this->get_field_id( 'show_id' ); ?>"><?php _e( 'Show donor/team numerical ID in table', 'ph-folding' ); ?>
                :</label>
        </p>
        <p>
            <input class="checkbox" type="checkbox"<?php checked( $show_donor_teams_team_donors ); ?>
                   id="<?php echo $this->get_field_id( 'show_donor_teams_team_donors' ); ?>"
                   name="<?php echo $this->get_field_name( 'show_donor_teams_team_donors' ); ?>"/>
            <label
                for="<?php echo $this->get_field_id( 'show_donor_teams_team_donors' ); ?>"><?php _e( 'Show donor\'s teams/team\'s donors?', 'ph-folding' ); ?>
                :</label>
        </p>
        <p>
            <input class="checkbox" type="checkbox"<?php checked( $show_logo ); ?>
                   id="<?php echo $this->get_field_id( 'show_logo' ); ?>"
                   name="<?php echo $this->get_field_name( 'show_logo' ); ?>"/>
            <label
                for="<?php echo $this->get_field_id( 'show_logo' ); ?>"><?php _e( 'Show Top folding logo?', 'ph-folding' ); ?>
                :</label>
        </p>
        <p>
            <input class="checkbox" type="checkbox"<?php checked( $show_tagline ); ?>
                   id="<?php echo $this->get_field_id( 'show_tagline' ); ?>"
                   name="<?php echo $this->get_field_name( 'show_tagline' ); ?>"/>
            <label
                for="<?php echo $this->get_field_id( 'show_tagline' ); ?>"><?php _e( 'Show F@H tagline?', 'ph-folding' ); ?>
                :</label>
        </p>
        <p>
            <label
                for="<?php echo $this->get_field_id( 'class' ); ?>"><?php _e( 'Table container CSS Class', 'ph-folding' ); ?>
                :</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'class' ); ?>"
                   name="<?php echo $this->get_field_name( 'class' ); ?>" type="text"
                   value="<?php echo esc_attr( $class ); ?>"/>
        </p>
        <?php
    }

// Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance )
    {
        $instance = array();
        $instance[ 'title' ] = ( !empty( $new_instance[ 'title' ] ) ) ? strip_tags( $new_instance[ 'title' ] ) : '';
        $instance[ 'type' ] = ( !empty( $new_instance[ 'type' ] ) ) ? strip_tags( $new_instance[ 'type' ] ) : '';
        $instance[ 'id' ] = ( !empty( $new_instance[ 'id' ] ) ) ? strip_tags( $new_instance[ 'id' ] ) : 1;
        $instance[ 'show_id' ] = isset( $new_instance[ 'show_id' ] ) ? (bool)$new_instance[ 'show_id' ] : false;
        $instance[ 'show_donor_teams_team_donors' ] = isset( $new_instance[ 'show_donor_teams_team_donors' ] ) ? (bool)$new_instance[ 'show_donor_teams_team_donors' ] : false;
        $instance[ 'show_logo' ] = isset( $new_instance[ 'show_logo' ] ) ? (bool)$new_instance[ 'show_logo' ] : false;
        $instance[ 'show_tagline' ] = isset( $new_instance[ 'show_tagline' ] ) ? (bool)$new_instance[ 'show_tagline' ] : false;

        $instance[ 'class' ] = ( !empty( $new_instance[ 'class' ] ) ) ? strip_tags( $new_instance[ 'class' ] ) : '';
        return $instance;
    }
} // Class Phoenix_Folding_Widget ends here