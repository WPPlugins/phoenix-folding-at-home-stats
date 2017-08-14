<?php $class = !empty($class) ? $class : ''; ?>
<tr class="<?php echo $class; ?>">
    <?php if ( !empty( $val ) ) : ?>
        <th class="string"><?php echo $string; ?></th>
        <td class="value"><?php echo $val; ?></td>
    <?php else : ?>
        <td class="missing" colspan="2"><?php _e( $missing_string, 'ph_folding' ); ?></td>
    <?php endif; ?>
</tr>