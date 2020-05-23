<?= head(array(
    'title' => __('ARIADNEplus Tracking'),
    ));
?>
<div id="primary">
    <?= flash(); ?>
    <div id="explanation">
        <img alt="ARIADNE+ Logo" id="ariadnelogo" src="<?= html_escape(img('ariadne-tracking-logo.png')); ?>" />
        <p><?= __('In this window you can manage the tracking tickets for imports to'); ?> <br> ARIADNEplus.</p>
    </div>
  <h2><?= __('Step 1: Select an existing ticket...'); ?></h2>
    <section class="ten columns alpha omega">
        <?= $this->Tracking()->showTickets(); ?>
    </section>
    <h2><?= __(' or '); ?><a href="<?= html_escape(url('ariadn-eplus-tracking/index/new')); ?>"> <?= __(' create a new one '); ?> </a></h2>
    
</div>	