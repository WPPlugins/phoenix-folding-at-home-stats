<?php

class Ph_Folding_Stats
{

    public $base_url = 'http://folding.stanford.edu/stats/';
    public $type = 'team';
    public $folding_id = 1;
    public $folding_stats = null; //object

    public $team_base_url = 'http://fah-web.stanford.edu/cgi-bin/main.py?qtype=teampage&teamnum=';
    public $donor_base_url = 'http://fah-web.stanford.edu/cgi-bin/main.py?qtype=userpage&username=';

    function __construct( $id = null, $type = null )
    {
        //add_filter('phoenix_folding_display_table', array($this, 'display_table'), 10, 2);
        if ( !empty( $id ) ) {

            if ( is_numeric( $id ) )
                $this->folding_id = absint( $id );
            else
                $this->folding_id = $name = str_replace( ' ', '_', trim( $id ) );
        }
        //default team
        if ( !empty( $type ) && $type == 'donor' )
            $this->type = $type;

        $this->max_team_donors = apply_filters( 'ph_folding_max_team_donors', 5, $id );
        $this->max_donor_teams = apply_filters( 'ph_folding_max_donor_teams', 5, $id );
    }

    public function get_remote_json( $endpoint )
    {

        $url = $this->base_url . $endpoint;
        $data = array();
        $response = wp_remote_get( $url );
        /*
        if ( 200 == $response['response']['code'] ) {
            $body = $response['body'];
            // perform action with the content.
        }
        */
        if ( is_wp_error( $response ) ) {
            return $response;
        } else {
            $body = $response[ 'body' ];
            return json_decode( $body );
        }
    }

    /*
     * Return the folding id
     */
    public function get_folding_id()
    {
        return $this->folding_id;
    }

    /*
     * Return the type of folding statistics
     */
    public function get_type()
    {
        return $this->type;
    }

    public function get_stats( $args = array() )
    {
        if ( !empty( $this->folding_stats ) )
            return $this->folding_stats;

        $folding_id = $this->get_folding_id();
        $type = $this->get_type();

        $folding_options = get_option( 'phoenix_folding_stats' );
        if(!empty($folding_options[$type][$folding_id]))
            $this->folding_stats = $folding_options[$type][$folding_id];
        if ( empty( $this->folding_stats->report_date ) || strtotime( $this->folding_stats->report_date ) <= apply_filters( 'ph_folding_refresh_time', strtotime( "-3 hours" ) ) ) {
            $folding_stats = $this->get_remote_json( 'api/' . $type . '/' . $folding_id );
            if ( !is_wp_error( $folding_stats ) ) {
                $this->folding_stats = new stdClass();
                $this->folding_stats->report_date = date( "Y-m-d H:i:s" );
                $this->folding_stats->name = $folding_stats->name;
                $this->folding_stats->credit = $folding_stats->credit;
                $this->folding_stats->rank = $folding_stats->rank;
                $this->folding_stats->wus = $folding_stats->wus;
                $this->folding_stats->last = $folding_stats->last;

                $i = 0;
                switch ( $this->type ) {
                    case 'donor':
                        $this->folding_stats->id = $folding_stats->id;
                        $this->folding_stats->total_entities = $folding_stats->total_users;
                        $max_donor_teams = 5;

                        $this->folding_stats->number_donor_teams = count( $folding_stats->teams );
                        usort( $folding_stats->teams, array( $this, 'sort_by_credit' ) );
                        //$teams = $this->sort_donor_teams_by_credit($folding_stats->teams);
                        foreach ( $folding_stats->teams as $team ) {
                            if ( $i < $max_donor_teams ) {
                                $team = array(
                                    'name' => $team->name,
                                    'credit' => $team->credit,
                                    'url' => $this->team_base_url . $team->team,
                                    'wus' => $folding_stats->wus
                                );
                                $this->folding_stats->teams[] = $team;
                                $i++;
                            } else {
                                break;
                            }
                        }
                        $this->folding_stats->url = $this->donor_base_url . $folding_stats->name;


                        break;
                    case 'team':
                    case 'default':
                        $this->folding_stats->total_entities = $folding_stats->total_teams;
                        $this->folding_stats->team = $folding_stats->team;

                        $this->folding_stats->number_team_donors = count( $folding_stats->donors );
                        foreach ( $folding_stats->donors as $donor ) {
                            if ( $i < $this->max_team_donors ) {
                                $donor = array(
                                    'name' => $donor->name,
                                    'credit' => $donor->credit,
                                    'url' => $this->donor_base_url . $donor->name
                                );
                                $this->folding_stats->donors[] = $donor;
                                $i++;
                            } else {
                                break;
                            }
                        }
                        //url http://fah-web2.stanford.edu/generic.html should be skipped
                        if ( !empty( $folding_stats->url ) && strpos( $folding_stats->url, 'generic' ) == false )
                            $this->folding_stats->url = $folding_stats->url;
                        else
                            $this->folding_stats->url = $this->team_base_url . $folding_stats->team;
                        break;
                }
                $folding_options[$type][$folding_id] = $this->folding_stats;
                update_option( 'phoenix_folding_stats', $folding_options );
            } else {
                    //return outdated folding stats if they're okay and that's all we've got. If not even that, return error.
                    if ( empty( $this->folding_stats->report_date )  )
                        return $folding_stats;
            }
        }
        //var_dump($folding_stats);

        return $this->folding_stats;
    }

    public function sort_donor_teams_by_credit( $teams )
    {
        return $teams;

    }

    public function display_table( $atts = array() )
    {
        $folding_stats = $this->get_stats();
        if ( !is_wp_error( $folding_stats ) ) {
            switch ( $this->type ) {
                case 'donor':
                    $table = $this->display_donor_table( $atts );
                    break;
                case 'team':
                case 'default':
                    $table = $this->display_team_table( $atts );
                    break;
            }
            return $table;
        } else {
            return $this->ph_print_error( '<h4>Folding@Home Error</h4><p><strong>' . $folding_stats->get_error_code() . ':</strong> ' . $folding_stats->get_error_message() . '</p>' );
        }
    }

    public function display_team_table( $atts = array() )
    {
        return ph_get_template_html( 'team.php', array_merge( $this->get_template_args(), $atts ) );
    }

    public function display_donor_table( $atts = array() )
    {
        return ph_get_template_html( 'donor.php', array_merge( $this->get_template_args(), $atts ) );
    }

    function get_template_args()
    {

        $args = array(
            'name' => $this->folding_stats->name,
            'credit' => $this->folding_stats->credit,
            'rank' => $this->folding_stats->rank,
            'total_teams' => $this->folding_stats->total_entities,
            'wus' => $this->folding_stats->wus,
            'last' => $this->folding_stats->last,
            'max_team_donors' => $this->max_team_donors,
            'report_date' => $this->folding_stats->report_date,
            'url' => $this->folding_stats->url,
        );
        switch ( $this->type ) {
            case 'donor':
                $args[ 'numerical_id' ] = $this->folding_stats->id;
                $args[ 'teams' ] = $this->folding_stats->teams;
                $args[ 'number_donor_teams' ] = !empty( $this->folding_stats->number_donor_teams ) ? $this->folding_stats->number_donor_teams : null;
                break;
            case 'team':
            case 'default':
                $args[ 'team' ] = $this->folding_stats->team;
                $args[ 'donors' ] = $this->folding_stats->donors;
                $args[ 'number_team_donors' ] = !empty( $this->folding_stats->number_team_donors ) ? $this->folding_stats->number_team_donors : null;
                break;
        }
        return $args;
    }

    function ph_print_error( $message )
    {
        return ph_get_template_html( "error.php", array(
            'messages' => array( $message )
        ) );
    }

    function sort_by_credit( $a, $b )
    {
        return $b->credit - $a->credit;
    }
}


