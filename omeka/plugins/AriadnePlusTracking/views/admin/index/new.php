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
                            <h5>
                              <?= __('Selected record : '); ?><span class="selected-record"></span>
                              <img id="cat-sites" class="ariadne-category" src="<?= html_escape(img('sites-icon.png')) ?>"/>
                              <img id="cat-event" class="ariadne-category" src="<?= html_escape(img('event-icon.png')) ?>"/>
                              <img id="cat-burial" class="ariadne-category" src="<?= html_escape(img('burial-icon.png')) ?>"/>
                              <img id="cat-artefact" class="ariadne-category"src="<?= html_escape(img('artefact-icon.png')) ?>"/>
                              <img id="cat-fieldwork" class="ariadne-category" src="<?= html_escape(img('fieldwork-icon.png')) ?>"/>
                              <img id="cat-scientific" class="ariadne-category" src="<?= html_escape(img('scientific-icon.png')) ?>"/>
                            </h5>
                            
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
        Omeka.Tickets.newForm();
        Omeka.Tickets.categorySelect();
    });   
</script>