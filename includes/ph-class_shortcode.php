<?php

class Ph_Shortcode
{
    private $base_url = "http://folding.stanford.edu/stats/";

    function __construct()
    {
        add_shortcode( 'phoenix_folding_stats', array( $this, 'shortcode_folding_stats' ) );
    }
    function shortcode_folding_stats( $atts, $content = "" )
    {
        $atts = shortcode_atts(
            array(
                'type' => 'team', //either team or donor
                'id' => '1', //can be numerical for either or string for donors
                'class' => '',
                'show_donor_teams' => true,
                'show_team_donors' => true,
                'show_id'   => false,
                'show_logo' => true,
                'show_tagline' => true
            ), $atts, 'phoenix_folding_stats' );
        $folding_stats = new Ph_Folding_Stats( $atts[ 'id' ], $atts[ 'type' ] );
        return apply_filters( 'ph_folding_display_table', $folding_stats->display_table( $atts ), $atts );
    }
}