<?= head(array(
    'title' => __('ARIADNEplus Tracking'),
    ));
?>
<?php if(!empty($ticket)): ?>
<input id="ticket-type" type="hidden" value="<?= $level; ?>">
<div id="primary">
    <?= flash(); ?>
    <h2><?= __('Step 2: Complete all phases.'); ?></h2>
    <div class="phases">
    <ul class="phases-container">
        <li id="phase-1" style="width:100/6%;" <?php if($level >= 0): ?> class='activated' <?php endif;?> >
            <div class="phase">
                <div id="phase-image-1" class="phase-image"><span class="span-phases" id="phase-span-1"></span></div>
                <div class="phase-current"><?= __('Phase 1'); ?></div>
                <div class="phase-description"><?= __('Metadata'); ?></div>
            </div>
        </li>
        <li id="phase-2" style="width:100/6%;" <?php if($level >= 2): ?> class='activated' <?php endif;?> >
            <div class="phase">
                <div id="phase-image-2" class="phase-image"><span class="span-phases" id="phase-span-2"></span></div>
                <div class="phase-current"><?= __('Phase 2'); ?></div>
                <div class="phase-description"><?= __('Map'); ?></div>
            </div>
        </li>
        <li id="phase-3" style="width:100/6%;" <?php if($level >= 3): ?> class='activated' <?php endif;?> >
            <div class="phase">
                <div id="phase-image-3" class="phase-image"><span class="span-phases" id="phase-span-3"></span></div>
                <div class="phase-current"><?= __('Phase 3'); ?></div>
                <div class="phase-description"><?= __('Enrich'); ?></div>
            </div>
        </li>
        <li id="phase-4" style="width:100/6%;" <?php if($level >= 4): ?> class='activated' <?php endif;?> >
            <div class="phase">
                <div id="phase-image-4" class="phase-image"><span class="span-phases" id="phase-span-4"></span></div>
                <div class="phase-current"><?= __('Phase 4'); ?></div>
                <div class="phase-description"><?= __('Communicate'); ?></div>
            </div>
        </li>
        <li id="phase-5" style="width:100/6%;" <?php if($level >= 5): ?> class='activated' <?php endif;?> >
            <div class="phase">
                <div id="phase-image-5" class="phase-image"><span class="span-phases" id="phase-span-5"></span></div>
                <div class="phase-current"><?= __('Phase 5'); ?></div>
                <div class="phase-description"><?= __('Publish'); ?></div>
            </div>
        </li>
        <li id="phase-6" style="width:100/6%;" <?php if($level >= 6): ?> class='activated' <?php endif;?> >
            <div class="phase">
                <div id="phase-image-5" class="phase-image"><span class="span-phases" id="phase-span-6"></span></div>
                <div class="phase-current"><?= __('Phase 6'); ?></div>
                <div class="phase-description"><?= __('Done'); ?></div>
            </div>
        </li>
    </ul>
    <div class="phase-bar" style="width: <?= (($level > 0) ? $level*(100/6) : 100/6).'%' ?>;"></div>
    </div>
    <div id="phase-content">
      <?php if($level != -1): ?>
    <?= $this->Tracking()->showPhase(array(
        'phase' => $level > 0 ? $level : 1, 
        'record' => $record, 
        'results' => isset($total_results) ? $total_results : 0, 
        'hide' => $hide)); ?>
      <?php else: ?>
      <div id="load">
        <h1>LOADING...</h1>
      </div>
      <?php endif; ?>
    </div>
        <?php if($level < 6 && $level >= 0): ?>
    <div class="next">
        <a id="next-btn" href="<?= html_escape(url('ariadn-eplus-tracking/index/stage', array('url' => WEB_ROOT,'record_type' => get_class($record), 'element' => $elementId, 
                                                            'record_id' => $record->id , 'term' => $ticket->status)));?> " class="btn btn-1">
            <span class="txt"><?= __('Next Phase'); ?></span>
            <span class="round"><i class="fa fa-chevron-right"></i></span>
        </a>
    </div>
        <?php elseif($level == 6): ?>
    <div class="renew">
        <a id="renew-btn" href="#" class="renew-btn btn-1">
            <img class="icon" src="<?= html_escape(img('reload-icon.png')); ?>">
            <span class="renew-txt"><?= __('Renew'); ?></span>
        </a>
      <form method="post" action="<?= html_escape(url('ariadn-eplus-tracking/index/renew'))?>"  id="renew-form" name="renew-form" > 
          <input type="hidden" name="record_type" value="<?= html_escape($ticket->record_type); ?>" />
          <input type="hidden" name="record_id" value="<?= html_escape($ticket->record_id); ?>" />
      </form>
    </div>
        <?php endif; ?>
    
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
        Omeka.Tickets.notifications();
        Omeka.Tickets.stageNotification(<?= isset($total_results) ? $total_results : 0;?>);
    });
</script>
<?php else: ?>
<h2><?= __('ERROR: Ticket not found');?></h2>
<p><?= __('Go back to %sARIADNEplus Tracking%s.', '<a href="' . 
                html_escape(url('ariadn-eplus-tracking')) . '">', '</a>'); ?></p>
<?php endif; ?>
