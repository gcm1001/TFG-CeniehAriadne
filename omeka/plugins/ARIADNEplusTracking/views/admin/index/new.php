<?= head(array(
    'title' => __('ARIADNEplus Tracking'),
    ));
?>
<div id="primary">
    <?= flash(); ?>
    <h2><?= __('Step 1: Create a new ticket'); ?></h2>
<div id="selectoptions">
    <div class="center-div-step">
        <div id="questionsdiv">
            <div class="container-step initial-active-area">
                <div class="line">
                  <div class="step first"><img alt="Step 1" id="step-1" src="<?= html_escape(img('step-1.png')); ?>" /></i></div>
                  <div class="step second"> <img alt="Step 2" id="step-2" src="<?= html_escape(img('step-2.png')); ?>" /></div>
                  <div class="step third"> <img alt="Step 3" id="step-3" src="<?= html_escape(img('step-3.png')); ?>" /></div>
                </div>
                <div class="steps">
                    <form id="new-form" class="option-submission" method="post">
                        <div class="submission first-step">
                            <p><?= __('Select the type of record you would like manage.'); ?></p>
                            <label class="input">
                                <?= $this->formSelect('record_type', null, array('id' => 'record-type','style' => 'max-width: 300px;'), $options_for_select_type); ?> <br/><br/>
                            </label>
                            <button class="first next"><?= __('Continue'); ?></button>
                        </div>
                        <div class="submission second-step">
                            <p><?= __('Choose the specific record.'); ?> </p>
                            <label class="input">              
                                <?= $this->formSelect('collection', null, array('id' => 'record-id-col','style' => 'max-width: 300px;'), $options_for_select_collection); ?> 
                                <?= $this->formSelect('item', null, array('id' => 'record-id-item','style' => 'max-width: 300px;'), $options_for_select_item); ?> <br/><br/>
                            </label>
                            <button class="second back"><?= __('Go back'); ?></button>
                            <button class="second next"><?= __('Continue'); ?></button>
                        </div>
                        <div id="last-form" class="submission third-step" method="post">
                            <h5><?= __('Selected record : '); ?><span class="selected-record"></span></h5>
                            <p> <?= __('Now select one of the fundamental ARIADNE categories which belongs to. '); ?></p>
                            <label class="input"> 
                                <?= $this->formSelect('ariadne_category', null, array('id' => 'ariadne-category','style' => 'max-width: 300px;'), $options_for_select_category); ?> <br/><br/>
                            </label>
                            <button class="third back"><?= __('Go back'); ?></button>
                            <button type="submit" id="new-button" class="third next"><?= __('Continue'); ?></button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
        var type = jQuery("#record-type");
        var typeval;
        var record;
        var recordval;
        var selectedrecord = jQuery(".selected-record");

        jQuery(".first").click(function (event) {
            typeval = type.val();
            if (typeval === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Please, select the type of record!',
                    showConfirmButton:false,
                });
                return false;
            }   
            if(typeval === "Collection"){
                jQuery("#record-id-item").hide();
                record = jQuery("#record-id-col");
            } else {
                jQuery("#record-id-col").hide();
                record = jQuery("#record-id-item");
            }
            jQuery(".container-step").removeClass("first initial-active-area");
            jQuery(".container-step").addClass("second second-active-area");
            event.preventDefault();
        });
        
        jQuery('.second.back').click(function (event){
            if(typeval === "Collection"){
                jQuery("#record-id-item").show();
            } else {
                jQuery("#record-id-col").show();
            }
            jQuery(".container-step").removeClass("second second-active-area");
            jQuery(".container-step").addClass("first initial-active-area");
            event.preventDefault();
        });
        
        jQuery('.third.back').click(function (event){
            jQuery(".container-step").removeClass("third third-active-area");
            jQuery(".container-step").addClass("second second-active-area");
            event.preventDefault();
        });
        
        jQuery(".second.next").click(function (event) {
            recordval = record.val();
            if (recordval === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Please, select a record!',
                    showConfirmButton:false,
                });
                return false;
            } else {
                selectedrecord.html(typeval + " " + recordval);
            }
            jQuery(".container-step").removeClass("second second-active-area initial-active-area");
            jQuery(".container-step").addClass("third third-active-area");
            event.preventDefault();
        });
        
        jQuery("#new-button").click(function (e) {
            e.preventDefault();
            var category = jQuery('#ariadne-category').val();
            if (category === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Please, select a category!',
                    showConfirmButton:false,
                });
                return false;
            } else {
                selectedrecord.html(typeval + " " + recordval);
                Swal.fire({
                    icon: 'success',
                    title: 'The ticket has been created successfully!',
                    text: 'Wait a moment, please...',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                });
                setTimeout(function(){
                    jQuery('#new-form').submit()
                }, 2500);
            }
            
        });
    });   
</script>