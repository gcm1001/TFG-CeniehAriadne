<?php $view = get_view(); ?>

<style type="text/css">
.hide-boxes {
    text-align: center;
}
</style>
<h2><?= htmlspecialchars(__("Classify main menu entries into sections."));?></h2>
<table id="hide-elements-table">
    <thead>
        <tr>
            <th class="hide-boxes" rowspan="2"><?= htmlspecialchars(__('Entry')); ?></th>
            <th class="hide-boxes" colspan=<?= htmlspecialchars(__(count($sections)));?>><?= htmlspecialchars(__('Sections')); ?></th>
        </tr>
        <tr>
            <?php
            foreach (array_keys($sections) as $section):;?>
            <?= __('<th class="hide-boxes">'.str_replace('-', ' ', $section).'</th>');?>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
    <?php
    foreach (array_keys($nav) as $entry):
        if($entry!='Dashboard'):
    ?>
        <tr>
            <td><?= htmlspecialchars(__($entry)); ?></td>
            <?php
            foreach (array_keys($sections) as $section):;
            ?>
            <td class="hide-boxes">
                <?= $view->formCheckbox(
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
