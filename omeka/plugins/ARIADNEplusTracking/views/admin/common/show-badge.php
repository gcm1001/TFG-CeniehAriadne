<h4> Ariadne+ Status</h4>
<?php if (!empty($extdiv)): ?> <div class="<?= html_escape($extdiv); ?>"> <?php endif; ?>
<div class="badge">
<?php if($status): ?>
          <a href="<?= html_escape(url('ariadn-eplus-tracking/index/ticket', 
                            array('record_type'=> ucfirst($type) , 
                                   $type => $record->id))); ?>">
          <div class="name"><span><img alt="A+" id="logo-badge" src="<?= img('ariadne-logo-badge.png'); ?>" /></span></div>
          <div class="status <?= trim(strtolower($status)) ?>"> <span><?= $status; ?></span> </div>
          </a>
<?php else: ?>
          <a href="<?= html_escape(url('ariadn-eplus-tracking/index/new')); ?>">
          <div class="name"><span><img alt="A+" id="logo-badge" src="<?= img('ariadne-logo-badge.png') ?>" /></span></div>
          <div class="status noprogress"><span>Propose the <?= $type; ?></span></div>
          </a>
<?php endif; ?>   
</div>
<?php if (!empty($extdiv)): ?> </div> <?php endif; ?>