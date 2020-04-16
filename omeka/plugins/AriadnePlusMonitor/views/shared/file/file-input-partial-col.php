<div class="two columns alpha">
	<label><b><?php echo __('JSON file of your matchings to Getty AAT'); ?></b></label>
</div>
<div class="drawer-contents">
    <p><?php echo __('The maximum file size is %s.', max_file_size()); ?></p>
    
    <div class="field two columns alpha" id="file-inputs">
        <label><?php echo __('Find a File'); ?></label>
    </div>

    <div class="files four columns omega">
        <input name="monitorfile[0]" type="file">
    </div>
</div>

