<?php
$show_team_donors = ( empty( $show_team_donors ) || ( isset( $show_team_donors ) && ( $show_team_donors === 'false' || $show_team_donors === 'no' || !$show_team_donors ) ) ) || empty( $donors ) || count( $donors ) < 1 ? false : true;

$team = !empty( $team ) ? $team : null;
$credit = !empty( $credit ) ? $credit : null;
$wus = !empty( $wus ) ? $wus : null;
$report_date = !empty( $report_date ) ? $report_date : null;
$last = !empty( $last ) ? $last : null;

$show_id = empty( $show_id ) || ( isset( $show_id ) && ( $show_id === 'false' || $show_id === 'no' || !$show_id ) ) ? false : true;
$show_logo = isset( $show_logo ) && ( $show_logo === 'false' || $show_logo === 'no' || !$show_logo ) ? false : true;
$show_tagline = isset( $show_tagline ) && ( $show_tagline === 'false' || $show_tagline === 'no' || !$show_tagline ) ? false : true;

?>
<div class="phoenix-fah-stats">
    <table class="fah-team<?php echo !empty( $class ) ? ' ' . $class : ''; ?>">
        <thead>
        <tr>
            <th colspan="2"><a
                    href="https://foldingathome.stanford.edu/">Folding@Home</a> <?php
                if ( !empty( $name ) ) {
                    _e( 'contribution stats for team', 'ph_folding' );
                    echo !empty( $url ) ? ' <a href="' . $url . '">' . ucfirst( $name ) . '</a>' : ' ' . ucfirst( $name );
                } else
                    _e( 'contribution stats', 'ph_folding' ); ?>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ( $show_logo )
            ph_get_template( 'logo.php' );
        if ( $show_id )
            ph_get_template( 'row.php', array( 'val' => $team, 'string' => __( 'Team ID', 'ph_folding' ), 'missing_string' => 'Team ID missing - Check in later', 'class' => 'fah-id' ) );
        ph_get_template( 'row.php', array( 'val' => number_format( $credit ), 'string' => __( 'Grand score (points)', 'ph_folding' ), 'missing_string' => 'Grand Score missing - Check in later', 'class' => 'fah-grand_score' ) );
        ph_get_template( 'row.php', array( 'val' => number_format( $wus ), 'string' => __( 'Work units completed', 'ph_folding' ), 'missing_string' => 'Work units missing - Check in later', 'class' => 'fah-work_units' ) );
        if ( !empty( $rank ) ) :
            // check for $rank before row.php because $rank is likely to be missing. Rank threshold to be reached before Folding provides rank through API.
            $rank = number_format( $rank );
            $rank .= !empty( $total_teams ) ? ' of ' . number_format( $total_teams ) . ' teams' : '';
            ph_get_template( 'row.php', array( 'val' => $rank, 'string' => __( 'Team Ranking', 'ph_folding' ), 'missing_string' => '', 'class' => 'fah-rank' ) );
        endif;
        ph_get_template( 'row.php', array( 'val' => ph_format_date( $last ), 'string' => __( 'Last work unit completed', 'ph_folding' ), 'missing_string' => 'Most recent work unit completion date missing - Check in later', 'class' => 'fah-last_completion_date' ) );
        ph_get_template( 'row.php', array( 'val' => ph_format_date( $report_date ), 'string' => __( 'Report generated on', 'ph_folding' ), 'missing_string' => 'Report date missing - Check in later', 'class' => 'fah-generated_date' ) ); ?>
        <?php if ( $show_team_donors ) : ?>
            <tr class="fah-team_donors_header">
            <th class="string" colspan="2"><?php
                echo !empty( $url ) && !empty( $name ) ? '<a href="' . $url . '">' . ucfirst( $name ) . '</a> ' : ' ';
                _e( 'Donors', 'ph_folding' );
                if ( !empty( $number_team_donors ) && !empty( $max_team_donors ) && $max_team_donors < $number_team_donors )
                    printf( ' <small>(' . __( 'top %d donors of %d', 'ph_folding' ) . ')<small>', $max_team_donors, $number_team_donors );
                ?></th>
            <?php foreach ( $donors as $donor ) :

                if ( !empty( $donor[ 'name' ] ) && !empty( $donor[ 'credit' ] ) ) : ?>
                    <tr class="fah-team_donors">
                        <th class="value name"><?php echo !empty( $donor[ 'url' ] ) ? '<a href="' . $donor[ 'url' ] . '">' . $donor[ 'name' ] . '</a>' : $donor[ 'name' ]; ?></th>
                        <td class="value score"><?php echo number_format( $donor[ 'credit' ] ); ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif;
        if ( $show_tagline )
            ph_get_template( 'tagline.php' ); ?>
        </tbody>
    </table>
</div>