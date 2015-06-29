jQuery(document).ready(function() {


    /*
    
        CKEDITOR TABS
    
    */
    
    jQuery("div#icp-ICPress-CkEditor").tabs();
    
    
    
    /*
    
        CKEDITOR CONTEXT MENU
    
    */
    
    if (jQuery("#wp-content-editor-container").length > 0)
    {
        
        var iframeWindow = null;
        
        CKEDITOR.plugins.add('icpress',
        {
            requires: [ 'iframedialog' ],
            
            init: function(editor)
            {
                var pluginName = 'icpress';
                var icpressPath = this.path + '../../../../icpress';
                var icpressCurrentShortcode = "";
                var icpressCurrentCitation = "";
                var icpressCurrentPages = "";
                var icpressCurrentPlaceholder = "";
                var icpressTotalShortcodes = 0;
                
                
                // Add ICPress CKEDITOR CSS
                CKEDITOR.config.contentsCss = [CKEDITOR.config.contentsCss, icpressPath + '/icpress.metabox.css'];
                
                // Set up ICPress commands
                editor.addCommand( 'icpress_AddCitation', new CKEDITOR.dialogCommand('icpress_AddCitation_Dialog') );
                editor.addCommand( 'icpress_AddBibliography', new CKEDITOR.dialogCommand('icpress_AddBibliography_Dialog') );
                
                
                // ICPress GUI menu item -- NOT WORKING?! Can't overwrite
                //editor.ui.addButton(pluginName, {
                //    label: 'Add Citation',
                //    group: 'icpressgroup',
                //    icon: icpressPath + '/icpress/images/icon.png',
                //    command: 'icpresscommand'
                //});
                
                
                // ICPress context menu items
                if (editor.addMenuItem)
                {
                    editor.addMenuGroup('icpress_MenuGroup');
                    
                    // Add or Edit Citation
                    editor.addMenuItem('icpress_AddCiteItem', {
                        label: 'Add Citation',
                        group: 'icpress_MenuGroup',
                        icon: icpressPath + '/images/icon-add.png',
                        command: 'icpress_AddCitation'
                    });
                    editor.addMenuItem('icpress_EditCiteItem', {
                        label: 'Edit Citation',
                        group: 'icpress_MenuGroup',
                        icon: icpressPath + '/images/icon-edit.png',
                        command: 'icpress_AddCitation'
                    });
                    
                    // Add or Edit Bibliography
                    editor.addMenuItem('icpress_AddBibItem', {
                        label: 'Add Bibliography',
                        group: 'icpress_MenuGroup',
                        icon: icpressPath + '/images/icon-add.png',
                        command: 'icpress_AddBibliography'
                    });
                    editor.addMenuItem('icpress_EditBibItem', {
                        label: 'Edit Bibliography',
                        group: 'icpress_MenuGroup',
                        icon: icpressPath + '/images/icon-edit.png',
                        command: 'icpress_AddBibliography'
                    });
                    
                } // editor.addMenuItem
                
                
                // Context menu
                if (editor.contextMenu)
                {
                    editor.contextMenu.addListener(function(element)
                    {
                        var icp_parents = element.getParents("span");
                        
                        if (icp_parents[1].getName() == "span")
                        {
                            if (icp_parents[1].getAttribute("class") == "icp-ICPress-Citation")
                            {
                                // Set current vars
                                icpressCurrentPlaceholder = icp_parents[1].getAttribute("id");
                                icpressCurrentShortcode = jQuery.trim(icp_parents[1].getChild(0).getText());
                                icpressCurrentCitation = jQuery.trim(icp_parents[1].getChild(1).getText());
                                if (jQuery.trim(icp_parents[1].getChild(0).getAttribute("rel")).length > 0)
                                    icpressCurrentPages = jQuery.trim(icp_parents[1].getChild(0).getAttribute("rel"));
                                
                                return { icpress_EditCiteItem: CKEDITOR.TRISTATE_ON };
                            }
                            else if (parents[1].getAttribute("class") == "icp-ICPress-Bibliography")
                            {
                                // Set current vars
                                icpressCurrentPlaceholder = icp_parents[1].getAttribute("id");
                                icpressCurrentShortcode = jQuery.trim(icp_parents[1].getChild(0).getText());
                                
                                return { icpress_EditBibItem: CKEDITOR.TRISTATE_ON };
                            }
                        }
                        
                        return { icpress_AddCiteItem: CKEDITOR.TRISTATE_ON, icpress_AddBibItem: CKEDITOR.TRISTATE_ON };
                    }); 
                } // editor.contextMenu
                
                
                // Grab Account data from select
                var icp_accounts = new Array();
                icp_accounts[0] = [ "Select an account", "" ];
                jQuery("select#icp-ICPressMetaBox-Collection-Accounts option").each(function(e) {
                    icp_accounts[e+1] = [ jQuery(this).text() + " (" + jQuery(this).attr("class") + ")", jQuery(this).val()];
                });
                
                
                // ICPress Citation dialog
                CKEDITOR.dialog.add( 'icpress_AddCitation_Dialog', function( api )
                {
                    return {
                        title : 'ICPress Citation',
                        minWidth : 600,
                        minHeight : 400,
                        contents :
                        [
                           {
                                id : 'iframe',
                                label : 'ICPress Citation',
                                expand : true,
                                elements :
                                [
                                   {
                                        type : 'iframe',
                                        src : icpressPath + '/icpress.widget.ckeditor.php?iframe=true',
                                        width : '100%',
                                        height : '100%',
                                        onContentLoad : function()
                                        {
                                            var iframe = document.getElementById( this._.frameId );
                                            iframeWindow = iframe.contentWindow;
                                        }
                                   }
                                ]
                           }
                        ],
                        onShow : function()
                        {
                            // Remove placeholder temporarily
                            if (icpressCurrentPlaceholder.length > 0)
                            {
                                jQuery(".cke_editor_content iframe").contents().find("#"+icpressCurrentPlaceholder).remove();
                                icpressCurrentPlaceholder = "";
                            }
                            
                            function icpressSetContentOnEdit()
                            {
                                if (jQuery("iframe.cke_dialog_ui_iframe").length > 0
                                        && jQuery("iframe.cke_dialog_ui_iframe").contents().find("#icp-ICPress-Output-Citation").length > 0)
                                {
                                    // For editing citation ... move from spans back to dialog ... check if spans exist!
                                    if (icpressCurrentShortcode.length > 0)
                                        jQuery("iframe.cke_dialog_ui_iframe").contents().find("#icp-ICPress-Output-Shortcode").val(icpressCurrentShortcode);
                                    
                                    if (icpressCurrentCitation.length > 0)
                                        jQuery("iframe.cke_dialog_ui_iframe").contents().find("#icp-ICPress-Output-Citation").val(icpressCurrentCitation);
                                    
                                    if (icpressCurrentPages.length > 0)
                                        jQuery("iframe.cke_dialog_ui_iframe").contents().find("#icp-ICPressMetaBox-Pages-Input").val(icpressCurrentPages);
                                    
                                    clearInterval(icpressIframeCheck);
                                }
                            }
                            
                            var icpressIframeCheck = setInterval(icpressSetContentOnEdit, 500);
                        },
                        onOk : function(event)
                        {
                            // Grab from [hidden] inputs
                            
                            var icpressPagesOutput = "";
                            icpressTotalShortcodes++;
                            
                            var icpressShortcodeOutput = '<span id="icp-Shortcode-' + icpressTotalShortcodes + '" class="icp-ICPress-Citation"><span class="icp-ICPress-Citation-Shortcode"'
                            
                            if (jQuery("iframe.cke_dialog_ui_iframe").contents().find("#icp-ICPressMetaBox-Pages-Input").val().length > 0)
                                icpressPagesOutput = jQuery("iframe.cke_dialog_ui_iframe").contents().find("#icp-ICPressMetaBox-Pages-Input").val();
                            
                            if (icpressPagesOutput.length > 0)
                                icpressShortcodeOutput += ' rel="' + icpressPagesOutput + '"';
                            
                            icpressShortcodeOutput += '>[icpressInText item="' + jQuery("iframe.cke_dialog_ui_iframe").contents().find("#icp-ICPress-Output-Shortcode").val() + '"';
                            
                            if (icpressPagesOutput.length > 0)
                                icpressShortcodeOutput += ' pages="' + icpressPagesOutput + '"';
                            
                            var icpressCitationOutput = jQuery("iframe.cke_dialog_ui_iframe").contents().find("#icp-ICPress-Output-Citation").val();
                            
                            if (icpressPagesOutput.length > 0)   // There's page(s) to add/update
                            {
                                if (icpressCitationOutput.indexOf("p. ") > 0)   // Already in citation
                                {
                                    icpressCitationOutput = icpressCitationOutput.substring(0, icpressCitationOutput.indexOf("p. ")) + "p. " + icpressPagesOutput  + ")";
                                }
                                else // Not in citation yet
                                {
                                    if (icpressPagesOutput.indexOf("-") > 0)   // Multiple
                                        icpressCitationOutput = icpressCitationOutput.replace(")", ", pp. " + icpressPagesOutput + ")");
                                    else   // Single
                                        icpressCitationOutput = icpressCitationOutput.replace(")", ", p. " + icpressPagesOutput + ")");
                                }
                            }
                            
                            icpressShortcodeOutput += ']</span><span class="icp-ICPress-Citation-Info">' + icpressCitationOutput + '</span></span>';
                            
                            // HAVE TO CHANGE so when editing, don't ADD but REPLACE
                            CKEDITOR.instances.content.insertHtml( icpressShortcodeOutput );
                        }
                    };
                } ); // icpress_AddCitation_Dialog
                
                
                
                // Add Bibliography dialog
               CKEDITOR.dialog.add( 'icpress_AddBibliography_Dialog', function ()
               {
                    return {
                        title : 'ICPress Bibliography',
                        minWidth : 600,
                        minHeight : 400,
                        contents :
                        [
                           {
                                id : 'iframe',
                                label : 'ICPress Bibliography',
                                expand : true,
                                elements :
                                [
                                   {
                                        type : 'iframe',
                                        src : icpressPath + '/icpress.widget.ckeditor.php?iframe=true&bib=true',
                                        width : '100%',
                                        height : '100%',
                                        onContentLoad : function() {
                                            var iframe = document.getElementById( this._.frameId );
                                            iframeWindow = iframe.contentWindow;
                                        }
                                   }
                                ]
                           }
                        ],
                        onOk : function()
                        {
                            //this._.editor.insertHtml(iframeWindow.getElementById('icp-ICPressMetaBox-ShortcodeCreator-Text-InTextBib').value);
                            CKEDITOR.instances.content.insertHtml( '<span class="icp-ICPress-Citation"><span class="icp-ICPress-Citation-Shortcode">' + iframeWindow.getElementById('icp-ICPressMetaBox-Output-Shortcode').value + '</span></span>' );
                        }
                    };
                } ); // icpress_AddBibliography_Dialog
                
            }
        });
        
        
        // Add the ICPress plugin to the CKEditor extra plugin list
        CKEDITOR.config.extraPlugins = 'icpress';
        
    }


});