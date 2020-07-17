<div id="div-phase-3">
    <div class="form-style">
        <form method="post" id="form-phase-3" action="#">
            <input type="hidden" id="json" value="<?= html_escape(metadata($record, array('Monitor','GettyAAT mapping')));?>">
            <h1 class="h1-phase" >Metadata enrichment<span class="span-form">To enrich your metadata, follow these steps.</span></h1>
            <div class="section"><span class="span-form-step">1</span>Create Period0 period definitions</div>
            <div class="inner-wrap">
                <a href="https://client.perio.do/?page=backend-home&backendID=web-https%3A%2F%2Fdata.perio.do%2F" target="_blank" class="grid-item">
                    <div class="img-tool">
                        <div>
                            <figure><img title="PeriodO" src="<?= html_escape(img('periodo-logo.svg'));?>" /></figure>
                            <span class="periodo-client">CLIENT</span>
                        </div>
                    </div>
                </a>
                <label><?= __('Your period0 collection url'); ?><input id="periodo" value="<?= html_escape(metadata($record,array('Monitor','URL of your PeriodO collection')));?>" 
                                                          placeholder="e.g. http://n2t.net/ark:/99152/p07h9k6" type="text" name="periodo" /></label>
                <span class="validity"></span>
            </div>
            <div class="section"><span class="span-form-step">2</span>Create AAT subject mappings</div>
            <div class="inner-wrap">
                <a href="https://ariadne.d4science.org/group/ariadneplus_mappings/vocabulary-matching-tool" target="_blank" class="grid-item">
                    <div class="img-tool">
                        <div>
                            <figure><img title="ARIADNEplus Gateway" src="<?= html_escape(img('ariadne-gateway-logo.png'));?>" /></figure>
                            <span class="vmtool">Vocabulary Matching Tool</span>
                        </div>
                    </div>
                </a>
                <p> You should save the output (.json) <?= link_to($record,'edit','here', array('target' => "_blank")); ?>.</p>
            </div>
        </form>
        <div class="button-section">
            <button class="form-button" id="btn-help-modal">ARIADNEplus Helper</button>
            <div id="help-modal" class="modal">
                <!-- Modal content -->
                <div class="modal-content">
                    <div id="help-button-popup" class="help-popup">
                        <div class="acc-container">
                            <span id="help" class="close">&times;</span>
                            <div class="acc-btn"><h4>How to create a Period0 Collection</h4></div>
                            <div class="acc-content">
                                <div class="acc-content-inner">
                                    <h4> <a href="http://perio.do/guide/" target="_blank"> Online User Guide </a> </h4>
                                </div>
                            </div>
                            <div class="acc-btn"><h4>How to use Vocabulary Matching Tool​</h4></div>
                            <div class="acc-content">
                                <div class="acc-content-inner">
                                    <h4> <a href="https://vmt.ariadne.d4science.org/vmt/vmt-help.html" target="_blank"> Online User Guide </a> </h4>
                                </div>
                            </div>
                            <div class="acc-btn"><h4>Export and Save your mapping file​</h4></div>
                            <div class="acc-content">
                                <div class="acc-content-inner">
                                    <h4>Steps:</h4>
                                    <ol>
                                        <li>Access to <a href="https://vmt.ariadne.d4science.org/vmt/" target="_blank"> Vocabulary Matching Tool </a>​​. When you log in, you will see your mappings: </li>
                                        <img src="<?= html_escape(img('vmt-1.png')); ?>" class="center"/>
                                        <li>In the lower buttons, click on the "Export JSON" button.</li>
                                        <img src="<?= html_escape(img('vmt-2.png')); ?>" class="center"/>
                                        <li>Save the output file.</li>
                                        <li>Then click <?= link_to($record,'edit','here', array('target' => "_blank")); ?>.</li>
                                        <li>After that you will be redirected to the edit page. Go to the "Monitor" tab (1), attach the saved file (2) and save the changes (3).</li>
                                        <img src="<?= html_escape(img('vmt-3.png')); ?>" class="center"/>
                                        <li>You got it.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>                  
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
        Omeka.Tickets.validateUrl(jQuery("#periodo"), 'periodo');
        Omeka.Tickets.helperModal();
    });
</script>