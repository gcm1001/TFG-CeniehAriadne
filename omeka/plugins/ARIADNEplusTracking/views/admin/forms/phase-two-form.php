<div id="div-phase-2">
    <div class="form-style-10">
    <h1 class="h1-phase" >Map your metadata!<span class="span-form">To map your metadata, follow these steps.</span></h1>
        <form method="post" id="form-phase-2" action="#"> 
        <div class="section"><span class="span-form-step">1</span>Download the metadata</div> 
        <div class="inner-wrap">
          <input id="value-mode" type="hidden" value="<?= html_escape($ticket->mode); ?>">
                <a href="#" style="display:none;" id="change-mode"> <?= __('Change mode'); ?> </a> <br>
                <label id='export-label'><?= __('Export mode'); ?> <select id="mode" name="mode">
                                    <option value=''><?= __('Select'); ?>...</option>
                                    <option value='OAI-PMH'>OAI-PMH</option>
                                    <option value='XML'>XML</option>
                                    </select></label>
                <a href="#" class="download-xml-button" style="display:none;" download><span class="span-dbutton"><?= __('Download'); ?></span><span class="span-dbutton" id="format-file"></span></a>
        </div> 
        <div class="section"><span class="span-form-step">2</span><?= __('Access to D4Science'); ?></div>
        <div class="inner-wrap">
            <p><?= __('Login using your d4science credentials'); ?>:<a href="https://ariadne.d4science.org/group/ariadneplus_mappings/mapping-tool"> Mapping Tool </a> </p>
        </div>

        <div class="section"><span class="span-form-step">3</span><?= __('Create the map'); ?></div>
            <div class="inner-wrap">
                <p><?= __('Map the data to the AO-Cat using the'); ?><a href="https://ariadne.d4science.org/group/ariadneplus_mappings/mapping-tool"> X3ML mapping tool</a>   </p>
        </div>
        <div class="section"><span class="span-form-step">4</span><?= __('Set the identifier of the map'); ?></div>
            <div class="inner-wrap">
                <label><?= __('Your mapping identifier'); ?><input type="textarea" id="map-identifier" name="map-identifier" 
                                                     value="<?= html_escape(metadata($record,array('Monitor','ID of your metadata transformation')));?>" placeholder="<?= __('For example:'); ?> Mapping/111" required></label>
        </div>
        </form>
        <div class="button-section">
         <!-- Modal button -->
            <button class="form-button" id="btn-help-modal"><?= __('Helper'); ?> </button>
            <!-- The Modal -->
            <div id="help-modal" class="modal">
              <!-- Modal content -->
                <div class="modal-content">
                <span id="help" class="close">&times;</span>
                <div id="help-button-popup" class="help-popup">
                    <h2>ARIADNEplus Helper </h2>
                    <h3>Topics</h3>
                    <div class="acc-container">
                    <div class="acc-btn"><h4>Create an account on D4Science</h4></div>
                    <div class="acc-content">
                        <div class="acc-content-inner">
                          <h4>If you are a new user you first need to register to the ARIADNEplus Mappings VRE on D4Science:</h4>
                          <ol>
                              <li>Go <a href="https://ariadne.d4science.org/explore"> here </a>​​. You will see: </li>
                                  <img src="<?= html_escape(img('register-1.png')); ?>" class="center"/>
                              <li> Click on “Request Access” for the <b>ARIADNEplus_Mappings</b></li>
                              <li>You’ll be asked to login. If it is your first time using D4Science, you’ll be
                                  prompted with the terms of use that you have to read and accept (Click on the
                                  “I agree” button on the bottom left).</li>
                              <li>Once logged in you’ll have to confirm the request by clicking on the “Confirm
                                  Request” button on the window that will be prompted.</li>
                                  <img src="<?= html_escape(img('register-2.png')); ?>" />
                              <li> You will receive a notification email when the moderators accept your request.
                                  As soon as you receive the email you can access the environment and use
                                  the <a href="https://ariadne.d4science.org/group/ariadneplus_mappings/mapping-tool" >3M mapping tool</a></li>
                          </ol>
                        </div>
                    </div>

                    <div class="acc-btn"><h4>How to use X3ML Mapping Tool</h4></div>
                    <div class="acc-content">
                      <div class="acc-content-inner">
                        <ol id="tuto">
                            <li id="tuto"><b id="title"> Control toolbar and Mapping list</b> <br>
                                <img src="<?= html_escape(img('map-1.png')); ?>" class="center"/>
                                <ol id="tuto">
                                    <li id="tuto"> Control toolbar – More/Copy XML/Rights</li>
                                    <img src="<?= html_escape(img('map-2.png')); ?>" />
                                    <li id="tuto">Search and Filter</li>
                                    <img src="<?= html_escape(img('map-3.png')); ?>" />
                                    <li id="tuto">Manual</li>
                                    <img src="<?= html_escape(img('map-4.png')); ?>" />
                                </ol>
                            </li>
                            <li id="tuto"><b id="title"> Setting up a New Mapping</b>
                                <ol id="tuto">
                                    <li id="tuto">Give Title and choose Target Schemas</li>
                                    <img src="<?= html_escape(img('map-5.png')); ?>" />
                                    <li id="tuto">Edit/view Info Tab</li>
                                    <img src="<?= html_escape(img('map-6.png')); ?>" />
                                    <li id="tuto">Add Generic Mapping Metadata</li>
                                    <img src="<?= html_escape(img('map-7.png')); ?>" />
                                    <li id="tuto">Adding Source Schema</li>
                                    <img src="<?= html_escape(img('map-8.png')); ?>" />
                                    <li id="tuto">Adding Sample Data</li>
                                    <img src="<?= html_escape(img('map-9.png')); ?>" />
                                    <img src="<?= html_escape(img('map-10.png')); ?>" />
                                    <li id="tuto">Adding Target Schemas</li>
                                    <img src="<?= html_escape(img('map-11.png')); ?>" />
                                    <img src="<?= html_escape(img('map-12.png')); ?>" />
                                </ol>
                            </li>
                            <li id="tuto"><b id="title"> Matching Table Operation</b>
                                <ol id="tuto">
                                    <li id="tuto">Accessing the Matching Table</li>
                                    <img src="<?= html_escape(img('map-13.png')); ?>" />
                                    <li id="tuto">Adding a New Map</li>
                                    <img src="<?= html_escape(img('map-14.png')); ?>" />
                                    <p> Adding Map Example</p>
                                    <img src="<?= html_escape(img('map-15.png')); ?>" />
                                    <li id="tuto">Adding a New Link</li>
                                    <img src="<?= html_escape(img('map-16.png')); ?>" /><br>
                                    <p> Example</p>
                                    <img src="<?= html_escape(img('map-17.png')); ?>" />
                                    <li id="tuto">Copy and Deleting Maps and Links</li>
                                    <img src="<?= html_escape(img('map-18.png')); ?>" />
                                    <li id="tuto">How to comment</li>
                                    <img src="<?= html_escape(img('map-19.png')); ?>" />
                                    <li id="tuto">View Records</li>
                                    <img src="<?= html_escape(img('map-20.png')); ?>" />
                                </ol>
                            </li>
                            <li id="tuto"><b id="title"> Mapping Patterns </b>
                                <ol id="tuto">
                                    <li id="tuto"> <b> X3ML Constructs </b> </li>
                                    <p>X3ML supports ​ <b>1:N mappings</b> ​ and uses the following special constructs:</p>
                                    <ul>
                                        <li><b>intermediate nodes</b>​ used to represent the mapping of a simple source path to a complex target path.</li>
                                        <li><b>constant expression nodes</b>​ used to assign constant attributes to an entity.</li>
                                        <li><b>conditional statements</b> ​ within the target node and target relation support checks
                                        for existence and equality of values and can be combined into Boolean
                                        expressions.</li>
                                        <li><b>“Same as” variable</b> ​ used to identify a specific node instance for a given input
                                        record that is generated once but is used in a number of locations in the mapping.</li>
                                        <li><b>Join operator (==)</b> ​ used in the source path to denote relational database joins.</li>
                                        <li><b>info and comment blocks</b> ​ throughout the mapping specification bridge the gap
                                            between human author and machine executor.</li>
                                    </ul>
                                    <li id="tuto"> Mapping Source Root to Target Domain</li>
                                    <img src="<?= html_escape(img('map-21.png')); ?>" />
                                    <p> Example </p>
                                    <img src="<?= html_escape(img('map-22.png')); ?>" />
                                    <p> Simple Field Mapping: Creating and Equivalent Proposition</p>
                                    <img src="<?= html_escape(img('map-23.png')); ?>" />
                                    <li id="tuto"> Simple Field Mapping (One to One) </li>
                                    <img src="<?= html_escape(img('map-24.png')); ?>" />
                                    <p> Example </p>
                                    <img src="<?= html_escape(img('map-25.png')); ?>" />
                                    <li id="tuto"> Mapping to Paths: Introducing intermediate nodes</li>
                                    <img src="<?= html_escape(img('map-26.png')); ?>" />
                                    <img src="<?= html_escape(img('map-27.png')); ?>" />
                                    <p> Example </p>
                                    <img src="<?= html_escape(img('map-28.png')); ?>" />
                                    <li id="tuto"> Mapping to Paths: Introducing constant expressions</li>
                                    <img src="<?= html_escape(img('map-29.png')); ?>" />
                                    <img src="<?= html_escape(img('map-30.png')); ?>" />
                                    <p> Example </p>
                                    <img src="<?= html_escape(img('map-31.png')); ?>" />
                                    <li id="tuto"> Using variables </li>
                                    <img src="<?= html_escape(img('map-32.png')); ?>" />
                                    <li id="tuto"> Joining Maps</li>
                                    <img src="<?= html_escape(img('map-33.png')); ?>" />
                                    <li id="tuto"> Multiple instantiation </li>
                                    <img src="<?= html_escape(img('map-34.png')); ?>" />
                                    <p> Example </p>
                                    <img src="<?= html_escape(img('map-35.png')); ?>" />
                                    <li id="tuto"> Mapping under conditions - If rules</li>
                                    <img src="<?= html_escape(img('map-36.png')); ?>" />
                                </ol>
                            </li>
                            <li id="tuto"><b id="title"> Instance and label Generators</b>
                                <ol id="tuto">
                                    <li id="tuto"> <b> Why Instance and Label Generators, what do they do? </b> </li>
                                    <p>The mapping table allows you to make a translation between a source schema and
                                        an RDFS encoded schema like CIDOC CRM. Each node specified in the target will
                                        become a separate data entity in the semantic graph that is created through the
                                        X3ML transformation engine. This separate data entity will need a unique identifier by
                                        which it can be found in the system (like a unique key in a relational database). The
                                        instance generators allow specifying patterns for building unique identifiers for
                                        instances called ‘URIs’.</p>

                                    <p> Because a URI is often unreadable, it is highly recommended that for each node a
                                        label (usually the actual data value from your source schema) be added to each node
                                        as well. This is also done through the instance generator. </p>

                                    <li id="tuto"> Defining instance generation functions/patterns (offline) </li>
                                    <img src="<?= html_escape(img('map-37.png')); ?>" />
                                    <p> Adding generator file (info tab) </p>
                                    <img src="<?= html_escape(img('map-38.png')); ?>" />
                                    <li id="tuto"> Opening up the generator editor </li>
                                    <img src="<?= html_escape(img('map-39.png')); ?>" />
                                    <li id="tuto"> Specifying Instance Generators </li>
                                    <img src="<?= html_escape(img('map-40.png')); ?>" /> <br/>
                                    <img src="<?= html_escape(img('map-41.png')); ?>" />
                                    <li id="tuto"> Testing Transforms </li>
                                    <img src="<?= html_escape(img('map-42.png')); ?>" />
                                    <li id="tuto"> Visualize transformed records </li>
                                    <img src="<?= html_escape(img('map-43.png')); ?>" />
                                    <li id="tuto"> RDF visualizer </li>
                                    <img src="<?= html_escape(img('map-44.png')); ?>" />
                                </ol>
                            </li>
                        </ol>
                      </div>
                    </div>
                        
                    <div class="acc-btn"><h4 class="selected">How to find the identifier of your mappings</h4></div>
                        <div class="acc-content">
                          <div class="acc-content-inner">
                            <h4>Steps:</h4>
                            <ol>
                                <li>Go <a href="https://ariadne.d4science.org/group/ariadneplus_mappings/mapping-tool"> here </a>​​. When you log in, you will see: </li>
                                <img src="<?= html_escape(img('mapid-1.png')); ?>" class="center"/>
                                <li> Search the map you created in the previous step and copy the "id" field.</li>
                                <img src="<?= html_escape(img('mapid-2.png')); ?>" class="center"/>
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
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
        var linkbutton = jQuery('.download-xml-button');
        var selectlabel = jQuery('#export-label');
        var changemode = jQuery('#change-mode');
        var submitmode = jQuery('#submit-mode');
        
        var changeMode = function(){
            selectlabel.show();
            linkbutton.hide();
            changemode.hide();
            submitmode.hide();
        };
        
        var setMode = function($mode){
            var type = "<?= get_class($record); ?>";
            if($mode === 'OAI-PMH'){
                if(type == 'Collection'){
                  var link = "<?= WEB_ROOT.'/oai-pmh-repository/request?verb=ListRecords&metadataPrefix=oai_qdc&set='.$record->id; ?>";
                  jQuery('#format-file').html($mode);
                } else {
                  jQuery('#mode').val('');
                  jQuery('#mode').notify('Items cannot be exported with this mode.',{ 
                        className: 'error' ,
                        position: 'top left'
                  });
                  return;
                }
            } else if ($mode === 'XML') {
                var link = "<?= WEB_ROOT.'/'.strtolower(get_class($record)).'s/show/'.$record->id.'?output=CIRfull'; ?>" ;
                jQuery('#format-file').html($mode);
            } else {
                return;
            }
            linkbutton.attr("href", link);
            selectlabel.hide();
            linkbutton.show();
            changemode.show();
            submitmode.show();
        };
        
        jQuery('#mode').change(function(){
            setMode(jQuery(this).val());
        });
        
        jQuery('#change-mode').click(function(event){
            event.preventDefault();
            changeMode();
        });
        
        if(jQuery('#value-mode').val()){
            jQuery("#mode").val(jQuery('#value-mode').val());
            setMode(jQuery("#mode").val())
        };
        
        jQuery("#map-identifier").change(function(){
            var value = jQuery(this).val();
            var regex = /^Mapping\/[0-9]{1,}$/i;
            if(value.match(regex)){
                jQuery(this).notify("Good.", { 
                        className: 'info' ,
                        position: 'top left'
                });
            } else{
                jQuery(this).notify("Identifier not valid.", { 
                        className: 'error' ,
                        position: 'top left'
                });
            };
        });
        
        var animTime = 300,clickPolice = false;
  
        jQuery(document).on('touchstart click', '.acc-btn', function(){
          if(!clickPolice){
             clickPolice = true;

            var currIndex = jQuery(this).index('.acc-btn'),
                targetHeight = jQuery('.acc-content-inner').eq(currIndex).outerHeight();

            
            if(jQuery(this).find('h4.selected')[0]) {
                jQuery('.acc-btn h4').removeClass('selected');
                jQuery('.acc-content').stop().animate({ height: 0 }, animTime);
            } else {
                jQuery('.acc-btn h4').removeClass('selected');
                jQuery(this).find('h4').addClass('selected');
                jQuery('.acc-content').stop().animate({ height: 0 }, animTime);
                jQuery('.acc-content').eq(currIndex).stop().animate({ height: targetHeight }, animTime);
            }
            
            setTimeout(function(){ clickPolice = false; }, animTime);
          };

        });
    });
</script>