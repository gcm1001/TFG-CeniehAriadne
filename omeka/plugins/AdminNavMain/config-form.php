<?php $view = get_view(); ?>

<style type="text/css">
.hide-boxes {
    text-align: center;
}
</style>
<table id="hide-elements-table">
    <thead>
        <tr>
            <th class="hide-boxes" rowspan="2"><?php echo __('Entry'); ?></th>
            <th class="hide-boxes" colspan=<?php echo __(count($sections))?>><?php echo __('Sections'); ?></th>
        </tr>
        <tr>
            <?php
            foreach (array_keys($sections) as $section):;
            echo __('<th class="hide-boxes">'.str_replace('-', ' ', $section).'</th>');
            ?>
            <?php endforeach; ?>
            <th class="hide-boxes"> </th>
        </tr>
    </thead>
    <tbody>
    <?php
    foreach (array_keys($nav) as $entry):
        if($entry!='Dashboard'):
    ?>
        <tr>
            <td><?php echo __($entry); ?></td>
            <?php
            foreach (array_keys($sections) as $section):;
            ?>
            <td class="hide-boxes">
                <?php echo $view->formCheckbox(
                    $section."[{$entry}]",
                    '1', array(
                        'disableHidden' => true,
                        'checked' => isset($sections[$section][$entry])
                    )
                ); ?>
            </td>
           <?php endforeach; ?>
        </tr>
 	<?php 
 	    endif; 
	endforeach; 
	?>
    </tbody>
</table>
