<?php global $wpdb; ?>
    

<!-- START OF ICPRESS METABOX -------------------------------------------------------------------------->

<div id="icp-ICPressMetaBox">
    
    <ul>
        <li><a href="#icp-ICPressMetaBox-Bibliography">Bibliography</a></li>
        <li><a href="#icp-ICPressMetaBox-InTextCreator">In-Text</a></li>
    </ul>
    
    
    
   
    <!-- START OF ICPRESS BIBLIOGRAPHY ------------------------------------------------------------------>
    <!-- NEXT: datatype [items, tags, collections], SEARCH items, tags, collections LIMIT -------------- -->
    
    <div id="icp-ICPressMetaBox-Bibliography">
        
        <?php
        
        if ($wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."icpress;") > 1)
        {
            // See if default exists
            $icp_default_account = false;
            if (get_option("ICPress_DefaultAccount")) $icp_default_account = get_option("ICPress_DefaultAccount");
            
            if ($icp_default_account !== false)
            {
                $icp_account = $wpdb->get_results(
                    $wpdb->prepare(
                        "
                        SELECT api_user_id, nickname FROM ".$wpdb->prefix."icpress
                        WHERE api_user_id = %s
                        ",
                        $icp_default_account
                    )
                );
            }
            else
            {
                $icp_account = $wpdb->get_results(
					"
					SELECT api_user_id, nickname FROM ".$wpdb->prefix."icpress LIMIT 1;
					"
				);
            }
            
            if (is_null($icp_account[0]->nickname) === false && $icp_account[0]->nickname != "")
                $icp_default_account = $icp_account[0]->nickname . " (" . $icp_account[0]->api_user_id . ")";
        ?>
        <!-- START OF ACCOUNT -->
        <div id="icp-ICPressMetaBox-Biblio-Account" rel="<?php echo $icp_account[0]->api_user_id; ?>">
            Searching <?php echo $icp_default_account; ?>. <a href="<?php echo admin_url( 'admin.php?page=ICPress&options=true'); ?>">Change account?</a>
        </div>
        <!-- END OF ACCOUNT -->
        <?php } ?>
        
        
        <!-- START OF SEARCH -->
        <div id="icp-ICPressMetaBox-Biblio-Citations">
            <input id="icp-ICPressMetaBox-Biblio-Citations-Search" class="help" type="text" value="Type to search" />
            <input type="hidden" id="ICPRESS_PLUGIN_URL" name="ICPRESS_PLUGIN_URL" value="<?php echo ICPRESS_PLUGIN_URL; ?>" />
            
        </div><div id="icp-ICPressMetaBox-Biblio-Citations-List"><div id="icp-ICPressMetaBox-Biblio-Citations-List-Inner"></div><hr class="clear" /></div>
        <!-- END OF SEARCH -->
        
        
        <!-- START OF OPTIONS -->
        <div id="icp-ICPressMetaBox-Biblio-Options">
            
            <h4>Options <span class='toggle'></span></h4>
            
            <div id="icp-ICPressMetaBox-Biblio-Options-Inner">
                
                <label for="icp-ICPressMetaBox-Biblio-Options-Author">Filter by Author:</label>
                <input type="text" id="icp-ICPressMetaBox-Biblio-Options-Author" value="" />
                
                <hr />
                
                <label for="icp-ICPressMetaBox-Biblio-Options-Year">Filter by Year:</label>
                <input type="text" id="icp-ICPressMetaBox-Biblio-Options-Year" value="" />
                
                <hr />
                
                <label for="icp-ICPressMetaBox-Biblio-Options-Style">Style:</label>
                <select id="icp-ICPressMetaBox-Biblio-Options-Style">
                    <?php
                    
                    if (!get_option("ICPress_StyleList"))
                        add_option( "ICPress_StyleList", "apa, apsa, asa, chicago-author-date, chicago-fullnote-bibliography, harvard1, modern-language-association, nature, vancouver");
                    
                    $icp_styles = explode(", ", get_option("ICPress_StyleList"));
                    sort($icp_styles);
                    
                    // See if default exists
                    $icp_default_style = "apa";
                    if (get_option("ICPress_DefaultStyle"))
                        $icp_default_style = get_option("ICPress_DefaultStyle");
                    
                    foreach($icp_styles as $icp_style)
                        if ($icp_style == $icp_default_style)
                            echo "<option id=\"".$icp_style."\" value=\"".$icp_style."\" rel='default' selected='selected'>".$icp_style."</option>\n";
                        else
                            echo "<option id=\"".$icp_style."\" value=\"".$icp_style."\">".$icp_style."</option>\n";
                    
                    ?>
                </select>
                <p class="note">Add more styles <a href="<?php echo admin_url( 'admin.php?page=ICPress&options=true'); ?>">here</a>. Note: Requires re-import.</p>
                
                <hr />
                
                <!--Sort by:-->
                <label for="icp-ICPressMetaBox-Biblio-Options-SortBy">Sort by:</label>
                <select id="icp-ICPressMetaBox-Biblio-Options-SortBy">
                    <option id="icp-bib-default" value="default" rel="default" selected="selected">Default</option>
                    <option id="icp-bib-author" value="author">Author</option>
                    <option id="icp-bib-date" value="date">Date</option>
                    <option id="icp-bib-title" value="title">Title</option>
                </select>
                
                <hr />
                
                <div class="icp-ICPressMetaBox-Field">
                    Sort order:
                    <div class="icp-ICPressMetaBox-Field-Radio">
                        <label for="icp-ICPressMetaBox-Biblio-Options-Sort-ASC">Ascending</label>
                        <input type="radio" id="icp-ICPressMetaBox-Biblio-Options-Sort-ASC" name="sort" value="ASC" checked="checked" />
                        
                        <label for="icp-ICPressMetaBox-Biblio-Options-Sort-DESC">Descending</label>
                        <input type="radio" id="icp-ICPressMetaBox-Biblio-Options-Sort-No" name="sort" value="DESC" />
                    </div>
                </div>
                
                <hr />
                
                <div class="icp-ICPressMetaBox-Field">
                    Show images?
                    <div class="icp-ICPressMetaBox-Field-Radio">
                        <label for="icp-ICPressMetaBox-Biblio-Options-Image-Yes">Yes</label>
                        <input type="radio" id="icp-ICPressMetaBox-Biblio-Options-Image-Yes" name="images" value="yes" />
                        
                        <label for="icp-ICPressMetaBox-Biblio-Options-Image-No">No</label>
                        <input type="radio" id="icp-ICPressMetaBox-Biblio-Options-Image-No" name="images" value="no" checked="checked" />
                        </div>
                </div>
                    
                <hr />
                
                <div class="icp-ICPressMetaBox-Field">
                    Show title by year?
                    <div class="icp-ICPressMetaBox-Field-Radio">
                        <label for="icp-ICPressMetaBox-Biblio-Options-Title-Yes">Yes</label>
                        <input type="radio" id="icp-ICPressMetaBox-Biblio-Options-Title-Yes" name="title" value="yes" />
                        
                        <label for="icp-ICPressMetaBox-Biblio-Options-Title-No">No</label>
                        <input type="radio" id="icp-ICPressMetaBox-Biblio-Options-Title-No" name="title" value="no" checked="checked" />
                    </div>
                </div>
                
                <hr />
                
                <div class="icp-ICPressMetaBox-Field">
                    Downloadable?
                    <div class="icp-ICPressMetaBox-Field-Radio">
                        <label for="icp-ICPressMetaBox-Biblio-Options-Download-Yes">Yes</label>
                        <input type="radio" id="icp-ICPressMetaBox-Biblio-Options-Download-Yes" name="download" value="yes" />
                        
                        <label for="icp-ICPressMetaBox-Biblio-Options-Download-No">No</label>
                        <input type="radio" id="icp-ICPressMetaBox-Biblio-Options-Download-No" name="download" value="no" checked="checked" />
                    </div>
                </div>
                
                <hr />
                
                <div class="icp-ICPressMetaBox-Field">
                    Abstract?
                    <div class="icp-ICPressMetaBox-Field-Radio">
                        <label for="icp-ICPressMetaBox-Biblio-Options-Abstract-Yes">Yes</label>
                        <input type="radio" id="icp-ICPressMetaBox-Biblio-Options-Abstract-Yes" name="abstract" value="yes" />
                        
                        <label for="icp-ICPressMetaBox-Biblio-Options-Abstract-No">No</label>
                        <input type="radio" id="icp-ICPressMetaBox-Biblio-Options-Abstract-No" name="abstract" value="no" checked="checked" />
                    </div>
                </div>
                
                <hr />
                
                <div class="icp-ICPressMetaBox-Field">
                    Notes?
                    <div class="icp-ICPressMetaBox-Field-Radio">
                        <label for="icp-ICPressMetaBox-Biblio-Options-Notes-Yes">Yes</label>
                        <input type="radio" id="icp-ICPressMetaBox-Biblio-Options-Notes-Yes" name="notes" value="yes" />
                        
                        <label for="icp-ICPressMetaBox-Biblio-Options-Notes-No">No</label>
                        <input type="radio" id="icp-ICPressMetaBox-Biblio-Options-Notes-No" name="notes" value="no" checked="checked" />
                    </div>
                </div>
                
                <hr />
                
                <div class="icp-ICPressMetaBox-Field">
                    Citable (in RIS format)?
                    <div class="icp-ICPressMetaBox-Field-Radio">
                        <label for="icp-ICPressMetaBox-Biblio-Options-Cite-Yes">Yes</label>
                        <input type="radio" id="icp-ICPressMetaBox-Biblio-Options-Cite-Yes" name="cite" value="yes" />
                        
                        <label for="icp-ICPressMetaBox-Biblio-Options-Cite-No">No</label>
                        <input type="radio" id="icp-ICPressMetaBox-Biblio-Options-Cite-No" name="cite" value="no" checked="checked" />
                    </div>
                </div>
                
                <hr />
                
                <label for="icp-ICPressMetaBox-Biblio-Options-Limit">Limit by:</label>
                <input type="text" id="icp-ICPressMetaBox-Biblio-Options-Limit" value="" />
                
            </div>
        </div>
        <!-- END OF OPTIONS -->
        
        <!-- START OF SHORTCODE -->
        <div id="icp-ICPressMetaBox-Biblio-Shortcode">
            
            <a id="icp-ICPressMetaBox-Biblio-Generate-Button" class="button-primary" href="javascript:void(0);">Generate Shortcode</a>
            <a id="icp-ICPressMetaBox-Biblio-Clear-Button" class="button" href="javascript:void(0);">Clear</a>
            
            <hr class="clear" />
            
            <div id="icp-ICPressMetaBox-Biblio-Shortcode-Inner">
                <label for="icp-ICPressMetaBox-Biblio-Shortcode-Text">Shortcode:</span></label>
                <textarea id="icp-ICPressMetaBox-Biblio-Shortcode-Text">[icpress]</textarea>
            </div>
        </div>
        <!-- END OF SHORTCODE -->
        
    </div><!-- #icp-ICPressMetaBox-Bibliography -->
    
    <!-- END OF ICPRESS BIBLIOGRAPHY --------------------------------------------------------------------->
    
    
    
    <!-- START OF ICPRESS IN-TEXT ------------------------------------------------------------------------->
    
    <div id="icp-ICPressMetaBox-InTextCreator">
        
        <?php if ($wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."icpress;") > 1) { ?>
        <!-- START OF ACCOUNT -->
        <div id="icp-ICPressMetaBox-Account">
            <?php
            
            // See if default exists
            $icp_default_account = false;
            if (get_option("ICPress_DefaultAccount"))
                $icp_default_account = get_option("ICPress_DefaultAccount");
            
            if ($icp_default_account !== false)
            {
                $icp_account = $wpdb->get_results(
                    $wpdb->prepare(
                        "
                        SELECT api_user_id, nickname FROM ".$wpdb->prefix."icpress
                        WHERE api_user_id = %s",
                        $icp_default_account
                    )
                );
            }
            else
            {
                $icp_account = $wpdb->get_results(
					"
					SELECT api_user_id, nickname FROM ".$wpdb->prefix."icpress LIMIT 1;
					"
				);
            }
            
            if (is_null($icp_account[0]->nickname) === false && $icp_account[0]->nickname != "")
                $icp_default_account = $icp_account[0]->nickname . " (" . $icp_account[0]->api_user_id . ")";
            
            ?>
            Searching <?php echo $icp_default_account; ?>. <a href="<?php echo admin_url( 'admin.php?page=ICPress&options=true'); ?>">Change account?</a>
        </div>
        <!-- END OF ACCOUNT -->
        <?php } ?>
        
        <!-- START OF SEARCH -->
        <div id="icp-ICPressMetaBox-Citations">
            <input id="icp-ICPressMetaBox-Citations-Search" class="help" type="text" value="Type to search" />
            <input type="hidden" id="ICPRESS_PLUGIN_URL" name="ICPRESS_PLUGIN_URL" value="<?php echo ICPRESS_PLUGIN_URL; ?>" />
            
        </div><div id="icp-ICPressMetaBox-Citations-List"><div id="icp-ICPressMetaBox-Citations-List-Inner"></div><hr class="clear" /></div>
        <!-- END OF SEARCH -->
        
        <!-- START OF OPTIONS -->
        <div id="icp-ICPressMetaBox-InTextCreator-Options">
            
            <h4>Options <span class='toggle'></span></h4>
            
            <div id="icp-ICPressMetaBox-InTextCreator-Options-Inner">
                
                <h5 class="first">In-Text Options</h3>
                
                <label for="icp-ICPressMetaBox-InTextCreator-Options-Format">Format:</label>
                <input type="text" id="icp-ICPressMetaBox-InTextCreator-Options-Format" value="(%a%, %d%, %p%)" />
                <p class="note">Use these placeholders: %a% for author, %d% for date, %p% for page, %num% for list number.</p>
                
                <hr />
                
                <label for="icp-ICPressMetaBox-InTextCreator-Options-Etal">Et al:</label>
                <select id="icp-ICPressMetaBox-InTextCreator-Options-Etal">
                    <option id="default" value="default" selected="selected">Default</option>
                    <option id="yes" value="yes">Yes</option>
                    <option id="no" value="no">No</option>
                </select>
                
                <hr />
                
                <label for="icp-ICPressMetaBox-InTextCreator-Options-Separator">Separator:</label>
                <select id="icp-ICPressMetaBox-InTextCreator-Options-Separator">
                    <option id="semicolon" value="default" selected="selected">Semicolon</option>
                    <option id="default" value="comma">Comma</option>
                </select>
                
                <hr />
                
                <label for="icp-ICPressMetaBox-InTextCreator-Options-And">And:</label>
                <select id="icp-ICPressMetaBox-InTextCreator-Options-And">
                    <option id="default" value="default" selected="selected">No</option>
                    <option id="and" value="and">and</option>
                    <option id="comma-and" value="comma-and">, and</option>
                </select>
                
                <h5>Bibliography Options</h3>
                
                <label for="icp-ICPressMetaBox-InTextCreator-Options-Style">Style:</label>
                <select id="icp-ICPressMetaBox-InTextCreator-Options-Style">
                    <?php
                    
                    if (!get_option("ICPress_StyleList"))
                        add_option( "ICPress_StyleList", "apa, apsa, asa, chicago-author-date, chicago-fullnote-bibliography, harvard1, modern-language-association, nlm, nature, vancouver");
                    
                    $icp_styles = explode(", ", get_option("ICPress_StyleList"));
                    sort($icp_styles);
                    
                    // See if default exists
                    $icp_default_style = "apa";
                    if (get_option("ICPress_DefaultStyle")) $icp_default_style = get_option("ICPress_DefaultStyle");
                    
                    foreach($icp_styles as $icp_style)
                        if ($icp_style == $icp_default_style)
                            echo "<option id=\"".$icp_style."\" value=\"".$icp_style."\" rel='default' selected='selected'>".$icp_style."</option>\n";
                        else
                            echo "<option id=\"".$icp_style."\" value=\"".$icp_style."\">".$icp_style."</option>\n";
                    
                    ?>
                </select>
                <p class="note">Add more styles <a href="<?php echo admin_url( 'admin.php?page=ICPress&options=true'); ?>">here</a>. Note: Requires re-import.</p>
                
                <hr />
                
                <!--Sort by:-->
                <label for="icp-ICPressMetaBox-InTextCreator-Options-SortBy">Sort by:</label>
                <select id="icp-ICPressMetaBox-InTextCreator-Options-SortBy">
                    <option id="default" value="default" rel="default" selected="selected">Default</option>
                    <option id="author" value="author">Author</option>
                    <option id="date" value="date">Date</option>
                    <option id="title" value="title">Title</option>
                </select>
                
                <hr />
                
                <div class="icp-ICPressMetaBox-Field">
                    Sort order:
                    <div class="icp-ICPressMetaBox-Field-Radio">
                        <label for="icp-ICPressMetaBox-InTextCreator-Options-Sort-ASC">Ascending</label>
                        <input type="radio" id="icp-ICPressMetaBox-InTextCreator-Options-Sort-ASC" name="sort" value="ASC" checked="checked" />
                        
                        <label for="icp-ICPressMetaBox-InTextCreator-Options-Sort-DESC">Descending</label>
                        <input type="radio" id="icp-ICPressMetaBox-InTextCreator-Options-Sort-No" name="sort" value="DESC" />
                    </div>
                </div>
                
                <hr />
                
                <div class="icp-ICPressMetaBox-Field">
                    Show images?
                    <div class="icp-ICPressMetaBox-Field-Radio">
                        <label for="icp-ICPressMetaBox-InTextCreator-Options-Image-Yes">Yes</label>
                        <input type="radio" id="icp-ICPressMetaBox-InTextCreator-Options-Image-Yes" name="images" value="yes" />
                        
                        <label for="icp-ICPressMetaBox-InTextCreator-Options-Image-No">No</label>
                        <input type="radio" id="icp-ICPressMetaBox-InTextCreator-Options-Image-No" name="images" value="no" checked="checked" />
                    </div>
                </div>
                
                <hr />
                
                <div class="icp-ICPressMetaBox-Field">
                    Show title by year?
                    <div class="icp-ICPressMetaBox-Field-Radio">
                        <label for="icp-ICPressMetaBox-InTextCreator-Options-Title-Yes">Yes</label>
                        <input type="radio" id="icp-ICPressMetaBox-InTextCreator-Options-Title-Yes" name="title" value="yes" />
                        
                        <label for="icp-ICPressMetaBox-InTextCreator-Options-Title-No">No</label>
                        <input type="radio" id="icp-ICPressMetaBox-InTextCreator-Options-Title-No" name="title" value="no" checked="checked" />
                    </div>
                </div>
                
                <hr />
                
                <div class="icp-ICPressMetaBox-Field">
                    Downloadable?
                    <div class="icp-ICPressMetaBox-Field-Radio">
                        <label for="icp-ICPressMetaBox-InTextCreator-Options-Download-Yes">Yes</label>
                        <input type="radio" id="icp-ICPressMetaBox-InTextCreator-Options-Download-Yes" name="download" value="yes" />
                        
                        <label for="icp-ICPressMetaBox-InTextCreator-Options-Download-No">No</label>
                        <input type="radio" id="icp-ICPressMetaBox-InTextCreator-Options-Download-No" name="download" value="no" checked="checked" />
                    </div>
                </div>
                
                <hr />
                
                <div class="icp-ICPressMetaBox-Field">
                    Abstract?
                    <div class="icp-ICPressMetaBox-Field-Radio">
                        <label for="icp-ICPressMetaBox-InTextCreator-Options-Abstract-Yes">Yes</label>
                        <input type="radio" id="icp-ICPressMetaBox-InTextCreator-Options-Abstract-Yes" name="abstract" value="yes" />
                        
                        <label for="icp-ICPressMetaBox-InTextCreator-Options-Abstract-No">No</label>
                        <input type="radio" id="icp-ICPressMetaBox-InTextCreator-Options-Abstract-No" name="abstract" value="no" checked="checked" />
                    </div>
                </div>
                
                <hr />
                
                <div class="icp-ICPressMetaBox-Field">
                    Notes?
                    <div class="icp-ICPressMetaBox-Field-Radio">
                        <label for="icp-ICPressMetaBox-InTextCreator-Options-Notes-Yes">Yes</label>
                        <input type="radio" id="icp-ICPressMetaBox-InTextCreator-Options-Notes-Yes" name="notes" value="yes" />
                        
                        <label for="icp-ICPressMetaBox-InTextCreator-Options-Notes-No">No</label>
                        <input type="radio" id="icp-ICPressMetaBox-InTextCreator-Options-Notes-No" name="notes" value="no" checked="checked" />
                    </div>
                </div>
                
                <hr />
                
                <div class="icp-ICPressMetaBox-Field">
                    Citable (in RIS format)?
                    <div class="icp-ICPressMetaBox-Field-Radio">
                        <label for="icp-ICPressMetaBox-InTextCreator-Options-Cite-Yes">Yes</label>
                        <input type="radio" id="icp-ICPressMetaBox-InTextCreator-Options-Cite-Yes" name="cite" value="yes" />
                        
                        <label for="icp-ICPressMetaBox-InTextCreator-Options-Cite-No">No</label>
                        <input type="radio" id="icp-ICPressMetaBox-InTextCreator-Options-Cite-No" name="cite" value="no" checked="checked" />
                    </div>
                </div>
                
            </div>
        </div>
        <!-- END OF OPTIONS -->
        
        <!-- START OF SHORTCODE -->
        <div id="icp-ICPressMetaBox-InTextCreator-Shortcode">
            
            <a id="icp-ICPressMetaBox-InTextCreator-Generate-Button" class="button-primary" href="javascript:void(0);">Generate Shortcode</a>
            <a id="icp-ICPressMetaBox-InTextCreator-Clear-Button" class="button" href="javascript:void(0);">Clear</a>
            
            <hr class="clear" />
            
            <div id="icp-ICPressMetaBox-InTextCreator-Shortcode-Inner">
                <label for="icp-ICPressMetaBox-InTextCreator-InText">Shortcode:</span></label>
                <textarea id="icp-ICPressMetaBox-InTextCreator-InText">[icpressInText]</textarea>
                
                <div id="icp-ICPressMetaBox-InTextCreator-Text-Bib-Container" class="inTextOnly">
                    <label for="icp-ICPressMetaBox-InTextCreator-Text-Bib">Bibliography: <span>(Paste somewhere in the post)</span></label>
                    <input id="icp-ICPressMetaBox-InTextCreator-Text-Bib" type="text" value="[icpressInTextBib]" />
                </div>
            </div>
        </div>
        <!-- END OF SHORTCODE -->
        
    </div><!-- #icp-ICPressMetaBox-InTextCreator -->
    
    <!-- END OF ICPRESS IN-TEXT ---------------------------------------------------------------------------->
    

    
</div><!-- #icp-ICPressMetaBox -->
    
<!-- END OF ICPRESS METABOX ------------------------------------------------------------------------------->


