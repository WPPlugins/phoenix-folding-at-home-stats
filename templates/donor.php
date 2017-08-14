<?php
$show_donor_teams = ( empty( $show_donor_teams ) || ( isset( $show_donor_teams ) && ( $show_donor_teams === 'false' || $show_donor_teams === 'no' || !$show_donor_teams ) ) ) || empty( $teams ) || count( $teams ) < 1 ? false : true;

$numerical_id = !empty( $numerical_id ) ? $numerical_id : null;
$credit = !empty( $credit ) ? $credit : null;
$wus = !empty( $wus ) ? $wus : null;
$report_date = !empty( $report_date ) ? $report_date : null;
$last = !empty( $last ) ? $last : null;

$show_id = empty( $show_id ) || ( isset( $show_id ) && ( $show_id === 'false' || $show_id === 'no' || !$show_id ) ) ? false : true;
$show_logo = isset( $show_logo ) && ( $show_logo === 'false' || $show_logo === 'no' || !$show_logo ) ? false : true;

$show_tagline = ( isset( $show_tagline ) && ( $show_tagline === 'false' || $show_tagline === 'no' || !$show_tagline ) ) ? false : true;

?>
<div class="phoenix-fah-stats">
    <table class="fah-donor<?php echo !empty( $class ) ? ' ' . $class : ''; ?>">
        <thead>
        <tr>
            <th colspan="2"><a href="https://foldingathome.stanford.edu/">Folding@Home</a> <?php
                if ( !empty( $name ) ) {
                    _e( 'contribution stats for donor ', 'ph_folding' );
                    echo !empty( $url ) ? '<a href=' . $url . '>' . ucfirst( $name ) . '</a>' : ucfirst( $name );
                } else
                    __( 'contribution stats', 'ph_folding' ); ?>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ( $show_logo )
            ph_get_template( 'logo.php' );
        if ( $show_id )
            ph_get_template( 'row.php', array( 'val' => $numerical_id, 'string' => __( 'Donor ID', 'ph_folding' ), 'missing_string' => 'Donor ID missing - Check in later', 'class' => 'fah-id' ) );
        ph_get_template( 'row.php', array( 'val' => number_format( $credit ), 'string' => __( 'Grand score (points)', 'ph_folding' ), 'missing_string' => 'Grand Score missing - Check in later', 'class' => 'fah-grand_score' ) );
        ph_get_template( 'row.php', array( 'val' => number_format( $wus ), 'string' => __( 'Work units completed', 'ph_folding' ), 'missing_string' => 'Work units missing - Check in later', 'class' => 'fah-work_units' ) );
        ?>
        <?php if ( !empty( $rank ) ) :
            // check for $rank before row.php because $rank is likely to be missing. Rank threshold to be reached before Folding provides rank through API.
            $rank = !empty( $total_teams ) ? sprintf( __( '%s of %s', 'ph_folding' ), number_format( $rank ), number_format( $total_teams ) ) : number_format( $rank );
            ph_get_template( 'row.php', array( 'val' => $rank, 'string' => __( 'Donor Ranking', 'ph_folding' ), 'missing_string' => '', 'class' => 'fah-rank' ) );
        endif;
        ph_get_template( 'row.php', array( 'val' => ph_format_date( $last ), 'string' => __( 'Last work unit completed', 'ph_folding' ), 'missing_string' => 'Most recent work unit completion date missing - Check in later', 'class' => 'fah-last_completion_date' ) );
        ph_get_template( 'row.php', array( 'val' => ph_format_date( $report_date ), 'string' => __( 'Report generated on', 'ph_folding' ), 'missing_string' => 'Report date missing - Check in later', 'class' => 'fah-generated_date' ) ); ?>
        <?php if ( $show_donor_teams ) : ?>
            <?php if ( count( $teams ) > 1 ) : ?>
                <tr class="fah-donor_teams_header">
                <th class="fah-string" colspan="2">
                <?php printf( __( '%s contributes to %d teams', 'ph_folding' ), ucfirst( $name ), $number_donor_teams );
                if ( !empty( $number_donor_teams ) && !empty( $max_donor_teams ) && $max_donor_teams < $number_donor_teams )
                    printf( ' <small>(' . __( 'showing top %d of %d teams', 'ph_folding' ) . ')<small>', $max_donor_teams, $number_donor_teams );
                ?></th><?php
            else :
                $team_name = !empty( $teams[ 0 ][ 'url' ] ) ? ' <a href="' . $teams[ 0 ][ 'url' ] . '">' . $teams[ 0 ][ 'name' ] . '</a>' : $teams[ 0 ][ 'name' ];
                ph_get_template( 'row.php', array( 'val' => $team_name, 'string' => __( 'Donor team', 'ph_folding' ), 'missing_string' => 'Donor\'s team missing - Check in later', 'class' => 'donor-team' ) );
            endif;
            ?>

            <?php if ( count( $teams ) > 1 ) :
                foreach ( $teams as $team ) :
                    ?>
                    <tr class="fah-donor_teams">
                        <th class="value name"><?php echo !empty( $team[ 'url' ] ) ? '<a href="' . $team[ 'url' ] . '">' . $team[ 'name' ] . '</a>' : $team[ 'name' ]; ?></th>
                        <td class="value score"><?php echo number_format( $team[ 'credit' ] ) . '&nbsp' . __( 'points', 'ph_folding' ); ?></td>
                    </tr>
                <?php endforeach;
            endif;
        endif;

        if ( $show_tagline )
            ph_get_template( 'tagline.php' ); ?>
        </tbody>
    </table>
</div>