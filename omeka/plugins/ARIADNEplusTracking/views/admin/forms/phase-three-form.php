<div id="div-phase-3">
    <div class="form-style-10">
        <form method="post" id="form-phase-3" action="#"> 
          <input type="hidden" id="json" value="<?= htmlspecialchars(metadata($record, array('Monitor','GettyAAT mapping')));?>">
        <h1 id="h1-phase" >Metadata enrichment<span>To enrich your metadata, follow these steps.</span></h1>
        <div class="section"><span>1</span>Create Period0 period definitions</div>
        <div class="inner-wrap">
           <label>Your period0 collection url<input id="periodo" value="<?= htmlspecialchars(metadata($record,array('Monitor','URL of your PeriodO collection')));?>" type="text" name="periodo" /></label>
        </div>
        <div class="section"><span>2</span>Create AAT subject mappings</div>
        <div class="inner-wrap">
            <p> You have created the matching between your local terms and Getty AAT with the <a href="https://ariadne.d4science.org/group/ariadneplus_mappings/vocabulary-matching-tool" > Vocabulary Matching Tool</a>​. </p>
            <p> Save the output on your folder <a href="https://data.d4science.net/smpr">​here on the d4science workspace</a>​ (Workspace > VRE Folders > ARIADNEplus_Mappings > Matched Vocabularies - login required). </p>
            <p> You should save the output <?= link_to($record,'edit','here'); ?> too (Monitor Tab >  JSON file of your matchings to Getty AAT ). </p>
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
                                <h4> <a href="http://perio.do/guide/"> Online User Guide </a> </h4>
                            </div>
                        </div>

                        <div class="acc-btn"><h4>How to use Vocabulary Matching Tool​</h4></div>
                        <div class="acc-content">
                          <div class="acc-content-inner">
                              <h4> <a href="https://vmt.ariadne.d4science.org/vmt/vmt-help.html"> Online User Guide </a> </h4>
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
        jQuery("#periodo").change(function(){
            var value = jQuery(this).val();
            var regex = /https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)$/;
            if(value.match(regex)){
                jQuery(this).notify("Good.", { 
                        className: 'info' ,
                        position: 'top left'
                });
            } else{
                jQuery(this).notify("That's not an url.", { 
                        className: 'error' ,
                        position: 'top left'
                });
            };
        });
    });
</script>