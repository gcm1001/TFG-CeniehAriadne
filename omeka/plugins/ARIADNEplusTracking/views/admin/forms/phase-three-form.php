<div id="div-phase-3">
    <div class="form-style-10">
        <form method="post" id="form-phase-3" action="#">
          <input type="hidden" id="json" value="<?= html_escape(metadata($record, array('Monitor','GettyAAT mapping')));?>">
        <h1 class="h1-phase" >Metadata enrichment<span class="span-form">To enrich your metadata, follow these steps.</span></h1>
        <div class="section"><span class="span-form-step">1</span>Create Period0 period definitions</div>
        <div class="inner-wrap">
            <a href="https://client.perio.do/?page=backend-home&backendID=web-https%3A%2F%2Fdata.perio.do%2F" target="_blank" class="grid-item">
              <div class="img-tool">
                <div>
                  <figure><img title="hover text" src="<?= html_escape(img('periodo-logo.svg'));?>" /></figure>
                  <span class="periodo-client">CLIENT</span>
                </div>
              </div>
            </a>
             <label>Your period0 collection url<input id="periodo" value="<?= html_escape(metadata($record,array('Monitor','URL of your PeriodO collection')));?>" 
                                                      placeholder="e.g. http://n2t.net/ark:/99152/p07h9k6" type="text" name="periodo" /></label>
        </div>
        <div class="section"><span class="span-form-step">2</span>Create AAT subject mappings</div>
        <div class="inner-wrap">
          <a href="https://ariadne.d4science.org/group/ariadneplus_mappings/vocabulary-matching-tool" target="_blank" class="grid-item">
            <div class="img-tool">
              <div>
                <figure><img title="hover text" src="<?= html_escape(img('ariadne-gateway-logo.png'));?>" /></figure>
                <span class="vmtool">Vocabulary Matching Tool</span>
              </div>
            </div>
          </a>
          <p> You should save the output (.json) <?= link_to($record,'edit','here'); ?>.</p>
        </div>
        </form>
        <div class="button-section">
            <button class="form-button" id="btn-help-modal">ARIADNEplus Helper</button>
            <div id="help-modal" class="modal">
              <!-- Modal content -->
                <div class="modal-content">
                <span id="help" class="close">&times;</span>
                <div id="help-button-popup" class="help-popup">
                        <h2>ARIADNEplus Helper</h2>
                        <h3>Topics</h3>
                        <div class="acc-container">
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
                        </div>
                    </div>                  
              </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function(){
        Omeka.Tickets.validateUrl(jQuery("#periodo"));
        Omeka.Tickets.helperModal();
    });
</script>