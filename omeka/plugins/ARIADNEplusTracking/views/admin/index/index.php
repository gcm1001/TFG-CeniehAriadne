<?php
$pageTitle = __('AriadnePlus Monitor');
echo head(array(
    'title' => $pageTitle,
    'bodyclass' => 'ariadne-plus-tracking index',
));
?>
<div id="primary">
    <?php echo flash(); ?>
    <div id="explanation">
        <img alt="ARIADNE+ Logo" id="ariadnelogo" src="<?php echo img('ariadne-tracking-logo.png'); ?>" />
        <p>In this window you can manage the tracking tickets for imports to <br> ARIADNEplus.</p>
    </div>
    <h2><?php echo __('Step 1: Select an existing ticket...'); ?></h2>
    <section class="ten columns alpha omega">
        <?php echo $this->Tracking()->showTickets(); ?>
    </section>
    <h2><?php echo __(' or <a href="%s">create a new one </a>.', html_escape(url('ariadn-eplus-tracking/index/new'))); ?></h2>
    
</div>	