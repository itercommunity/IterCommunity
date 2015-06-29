<?php
 
class icpressBrowse
{
	/**
	 * Creates a HTML-formatted library for the selected account.
	 *
	 * TO-DO:
	 * - Remove admin-only features, e.g. Set Image, item key input box, etc.
	 * - Style front-end display
	 * - Add shortcode information to Help page
	 * 
	 * @return     string         	the HTML-formatted subcollections
	 */
	
	private $api_user_id = "";
	
	public function __construct()
	{
		// Called automatically when an instance is instantiated
	}
	
	public function setAccount($api_user_id)
	{
		$this->api_user_id = $api_user_id;
	}
	
	public function getAccount()
	{
		return $this->api_user_id;
	}
	
	public function getLib()
	{
		global $wpdb;
		
		
		// Account ID
		
		global $api_user_id;
		
		if ( isset($_GET['account_id']) && preg_match("/^[0-9]+$/", $_GET['account_id']) )
			$api_user_id = $wpdb->get_var("SELECT nickname FROM ".$wpdb->prefix."icpress WHERE id='".$_GET['account_id']."'", OBJECT);
		
		
		// Collection ID
		
		global $collection_id;
		
		if (isset($_GET['collection_id']) && preg_match("/^[0-9a-zA-Z]+$/", $_GET['collection_id']))
			$collection_id = trim($_GET['collection_id']);
		else
			$collection_id = false;
		
		
		// Tag Name and ID
		
		global $tag_id;
		
		if (isset($_GET['tag_id']) && preg_match("/^[0-9]+$/", $_GET['tag_id']))
			$tag_id = trim($_GET['tag_id']);
		else
			$tag_id = false;
		
		
		?>
            <div id="icp-Browse">
                
                <div id="icp-Browse-Bar">
                    
                    <div id="icp-Browse-Collections">
						<?php
						
						// Collection Title
						
						if ( is_admin() ) // Admin Browse Only
						{
							echo '<a class="icp-List-Subcollection toplevel ';
							if (!$collection_id && !$tag_id) echo 'selected';
							echo '" title="Top Level" href="?page=ICPress';
							if ( $api_user_id ) echo "&amp;api_user_id=".$api_user_id;
							echo '"><span>Collections</span></a>';
						}
						
						// Display Collection List
                        
                        if ( $collection_id ) // parent
                        {
                            //$icp_collection = get_term( $collection_id, 'icp_collections', 'OBJECT' );
                            if ( is_admin( ) ) $icp_top_collection = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."icpress_zoteroCollections WHERE api_user_id='".$this->api_user_id."' AND id='".$collection_id."'", OBJECT);
                            if ( ! is_admin( ) ) $icp_top_collection = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."icpress_zoteroCollections WHERE api_user_id='".$this->api_user_id."' AND item_key='".$collection_id."'", OBJECT);
                        }
                        
                        $icp_collections_query = "SELECT * FROM ".$wpdb->prefix."icpress_zoteroCollections WHERE api_user_id='".$this->api_user_id."' ";
                        if ( $collection_id ) $icp_collections_query .= "AND parent='".$icp_top_collection->item_key."' "; else $icp_collections_query .= "AND parent='' ";
                        $icp_collections_query .= "ORDER BY title ASC";
                        //$icp_collections = get_terms( 'icp_collections', array( 'parent' => $collection_id, 'hide_empty' => false ) );
                        $icp_collections = $wpdb->get_results($icp_collections_query, OBJECT);
						
						if ( ! is_admin() )
						{
							echo "<div class='icp-Browse-Select'><select id='icp-Browse-Collections-Select'>\n";
							
							if ( $tag_id ) echo "<option value='blank'>--Nothing Selected--</option>";
							if ( ! $collection_id ) echo "<option value='toplevel'>Top level</option>";
							if ( $collection_id ) echo "<option selected='selected' value='".$icp_top_collection->item_key."'>".$icp_top_collection->title."</option>";
						}
                        
                        foreach ( $icp_collections as $i => $icp_collection )
                        {
                            //if ( get_option( 'icp_collection-'.$icp_collection->term_id.'-api_user_id' ) != $account_id ) continue;
                            
							if ( ! is_admin() )
							{
								echo '<option value="'.$icp_collection->item_key.'">';
								if ( $collection_id ) echo " - ";
								echo $icp_collection->title.' ('.$icp_collection->numCollections.' subcollections, '.$icp_collection->numItems.' items)</option>'; echo "\n";
							}
							else // admin browse
							{
								echo "<a class='icp-List-Subcollection";
								if ( $collection_id && $collection_id == $icp_collection->item_key ) echo " selected";
								if ( $collection_id ) echo " child";
								if ( !$collection_id && $i == (count($icp_collections)-1) ) echo " last";
								echo "' title='".$icp_collection->title."' href='?page=ICPress&amp;collection_id=".$icp_collection->id;
								if ( $collection_id ) echo "&amp;up=".$collection_id;
								if ( $api_user_id ) { echo "&amp;api_user_id=".$api_user_id; }
								echo "'>";
								echo "<span class='name'>".$icp_collection->title."</span>";
								echo "<span class='item_key'>Collection Key: ".$icp_collection->item_key."</span>";
								echo "<span class='meta'>".$icp_collection->numCollections." subcollections, ".$icp_collection->numItems." items</span>";
								echo "</a>\n";
							}
                        }
                        
						// Collection List back button
                        if ( is_admin() && $collection_id )
						{
							echo '<a class="icp-List-Subcollection back last" title="Back to previous collection(s)" href="?page=ICPress';
							if (isset($_GET['up']) && preg_match("/^[0-9]+$/", $_GET['up'])) echo "&amp;collection_id=".$_GET['up'];
							if ( $api_user_id ) echo "&amp;api_user_id=".$api_user_id;
							echo '"><span>Back</span></a>';
						}
						
						if ( ! is_admin() )
						{
							if ( $collection_id ) echo "<option value='toplevel'>Back to Top level</option>";
							echo "</select></div>\n";
						}
						?>
                    </div><!-- #icp-Browse-Collections -->
                    
                    
                    <div id="icp-Browse-Tags">
                        <?php
						
						if ( is_admin() ) echo '<label for="icp-List-Tags"><span>Tags</span></label>';
						
						if ( ! is_admin() ) echo "<div class='icp-Browse-Select'>";
						echo '<select id="icp-List-Tags" name="icp-List-Tags"';
						if ( $tag_id ) echo ' class="active"';
						echo '>';
                        
						if ( !$tag_id ) echo '<option id="icp-List-Tags-Select" name="icp-List-Tags-Select">No tag selected</option>';
                        
                        //$icp_tags = get_terms( 'icp_tags', array( 'hide_empty' => false ) );
                        $icp_tags = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."icpress_zoteroTags WHERE api_user_id='".$this->api_user_id."' ORDER BY title ASC", OBJECT);
                        
                        foreach ( $icp_tags as $icp_tag )
                        {
                            //if ( get_option( 'icp_tag-'.$icp_tag->term_id.'-api_user_id' ) != $account_id ) continue;
                            
                            echo "<option class='icp-List-Tag' rel='".$icp_tag->id."'";
                            if ( $tag_id == $icp_tag->id ) echo " selected='selected'";
                            echo ">".$icp_tag->title." (".$icp_tag->numItems.")";
                            echo "</option>\n";
                        }
						
						echo "</select>\n";
                        
						if ( ! is_admin() ) echo "</div>\n";
                        ?>
                    </div><!-- #icp-Browse-Tags -->
                    
                </div><!-- #icp-Browse-Bar -->
                
				
                <div id="icp-List">
                
                <?php
                
                // Display title if on collection page
                
                if ( $collection_id )
                {
                    echo "<div class='icp-Collection-Title'>";
                        echo "<span class='name'>".$icp_top_collection->title."</span>";
						if ( is_admin() )
						{
							echo "<div class='item_key'>";
								echo "<span class='item_key_title'>Collection key:</span>";
								echo "<div class='item_key_inner'>";
									echo "<span id='icp-Collection-Title-Key'>".$icp_top_collection->item_key."</span>";
									echo "<input id='icp-Collection-Title-Key-Input' type='text' value='".$icp_top_collection->item_key."' />";
								echo "</div>\n";
							echo "</div>\n";
						}
                    echo "</div>\n";
                }
                else if ( $tag_id ) // Top Level
                {
                    $tag_title = $wpdb->get_row("SELECT title FROM ".$wpdb->prefix."icpress_zoteroTags WHERE api_user_id='".$this->api_user_id."' AND id='".$tag_id."'", OBJECT);
                    echo "<div class='icp-Collection-Title'>Viewing items with the \"<strong>".$tag_title->title."</strong>\" tag</div>\n";
                }
                else
                {
                    echo "<div class='icp-Collection-Title'>Top Level Items</div>\n";
                }
                
                ?>
                
                <?php
                    
                    /*$icp_citation_attr =
                        array(
                            'posts_per_page' => -1,
                            'post_type' => 'icp_entry',
                            'orderby' => 'post_date',
                            'order' => 'DESC',
                            'meta_query' => array(
                                'relation' => 'AND',
                                array(
                                    'key' => 'api_user_id',
                                    'value' => $account_id,
                                    'compare' => 'LIKE'
                                ),
                                array(
                                    'key' => 'item_type',
                                    'value' => array( 'attachment', 'note' ),
                                    'compare' => 'NOT IN'
                                )
                            )
                        );
                    
                    // By Collection ID
                    if (isset($_GET['collection_id']) && preg_match("/^[a-zA-Z0-9]+$/", $_GET['collection_id']) == 1)
                    {
                        $icp_citation_attr = array_merge( $icp_citation_attr,
                            array(
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => 'icp_collections',
                                        'field' => 'id',
                                        'terms' => $_GET['collection_id'],
                                        'include_children' => false
                                    )
                                )
                            )
                        );
                    
                    // By Tag ID
                    } else if (isset($_GET['tag_id']) && preg_match("/^[0-9]+$/", $_GET['tag_id']) == 1)
                    {
                        $icp_citation_attr = array_merge( $icp_citation_attr,
                            array(
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => 'icp_tags',
                                        'field' => 'id',
                                        'terms' => $_GET['tag_id']
                                    )
                                )
                            )
                        );
                    }
                    
                    $icp_citations = get_posts( $icp_citation_attr );*/
                    
                    // By Collection ID
                    if (isset($_GET['collection_id']) && preg_match("/^[a-zA-Z0-9]+$/", $_GET['collection_id']) == 1)
                    {
                        $icp_citations_query = 
                            "
                            SELECT ".$wpdb->prefix."icpress_zoteroItems.*,
							".$wpdb->prefix."icpress_zoteroItemImages.image AS itemImage
							FROM
							 ".$wpdb->prefix."icpress_zoteroUserItems INNER JOIN 
							 ".$wpdb->prefix."icpress_zoteroItems ON ".$wpdb->prefix."icpress_zoteroUserItems.id = ".$wpdb->prefix."icpress_zoteroItems.id
                            LEFT JOIN ".$wpdb->prefix."icpress_zoteroRelItemColl
								ON ".$wpdb->prefix."icpress_zoteroItems.item_key=".$wpdb->prefix."icpress_zoteroRelItemColl.item_key 
							LEFT JOIN ".$wpdb->prefix."icpress_zoteroItemImages
								ON ".$wpdb->prefix."icpress_zoteroItems.item_key=".$wpdb->prefix."icpress_zoteroItemImages.item_key
								AND ".$wpdb->prefix."icpress_zoteroUserItems.api_user_id=".$wpdb->prefix."icpress_zoteroItemImages.api_user_id
                            WHERE ".$wpdb->prefix."icpress_zoteroRelItemColl.collection_key = '".$icp_top_collection->item_key."' 
                            AND ".$wpdb->prefix."icpress_zoteroItems.itemType != 'attachment'
                            AND ".$wpdb->prefix."icpress_zoteroItems.itemType != 'note'
                            AND ".$wpdb->prefix."icpress_zoteroUserItems.api_user_id = '".$this->api_user_id."'
                            ORDER BY author ASC
                            ";
						$icp_citations = $wpdb->get_results( $icp_citations_query );
                    }
                    // By Tag ID
                    else if (isset($_GET['tag_id']) && preg_match("/^[0-9]+$/", $_GET['tag_id']) == 1)
                    {
                        $icp_citations_query =
                            "
                            SELECT ".$wpdb->prefix."icpress_zoteroItems.*,
							".$wpdb->prefix."icpress_zoteroItemImages.image AS itemImage
							FROM ".$wpdb->prefix."icpress_zoteroUserItems INNER JOIN ".$wpdb->prefix."icpress_zoteroItems 
							".$wpdb->prefix."icpress_zoteroItems ON ".$wpdb->prefix."icpress_zoteroUserItems.id = ".$wpdb->prefix."icpress_zoteroItems.id
                            LEFT JOIN ".$wpdb->prefix."icpress_zoteroRelItemTags
								ON ".$wpdb->prefix."icpress_zoteroItems.item_key=".$wpdb->prefix."icpress_zoteroRelItemTags.item_key 
							LEFT JOIN ".$wpdb->prefix."icpress_zoteroItemImages
								ON ".$wpdb->prefix."icpress_zoteroItems.item_key=".$wpdb->prefix."icpress_zoteroItemImages.item_key
								AND ".$wpdb->prefix."icpress_zoteroUserItems.api_user_id=".$wpdb->prefix."icpress_zoteroItemImages.api_user_id
                            WHERE ".$wpdb->prefix."icpress_zoteroRelItemTags.tag_title = '".$tag_title->title."' 
                            AND ".$wpdb->prefix."icpress_zoteroItems.itemType != 'attachment'
                            AND ".$wpdb->prefix."icpress_zoteroItems.itemType != 'note'
                            AND ".$wpdb->prefix."icpress_zoteroUserItems.api_user_id = '".$this->api_user_id."'
                            ORDER BY author ASC
                            ";
						$icp_citations = $wpdb->get_results( $icp_citations_query );
                    }
                    // Top-level
                    else
                    {
                        $icp_citations_query =
                            "
                            SELECT ".$wpdb->prefix."icpress_zoteroItems.*,
								".$wpdb->prefix."icpress_zoteroItemImages.image AS itemImage,
								".$wpdb->prefix."icpress_zoteroRelItemColl.collection_key
							FROM ".$wpdb->prefix."icpress_zoteroItems 
							 ".$wpdb->prefix."icpress_zoteroUserItems INNER JOIN 
							".$wpdb->prefix."icpress_zoteroItems ON ".$wpdb->prefix."icpress_zoteroUserItems.id = ".$wpdb->prefix."icpress_zoteroItems.id 
                            LEFT JOIN ".$wpdb->prefix."icpress_zoteroRelItemColl
								ON ".$wpdb->prefix."icpress_zoteroItems.item_key=".$wpdb->prefix."icpress_zoteroRelItemColl.item_key
							LEFT JOIN ".$wpdb->prefix."icpress_zoteroItemImages
								ON ".$wpdb->prefix."icpress_zoteroItems.item_key=".$wpdb->prefix."icpress_zoteroItemImages.item_key
								AND ".$wpdb->prefix."icpress_zoteroUserItems.api_user_id=".$wpdb->prefix."icpress_zoteroItemImages.api_user_id
                            WHERE ".$wpdb->prefix."icpress_zoteroRelItemColl.collection_key IS NULL
                            AND ".$wpdb->prefix."icpress_zoteroItems.itemType != 'attachment'
                            AND ".$wpdb->prefix."icpress_zoteroItems.itemType != 'note'
                            AND ".$wpdb->prefix."icpress_zoteroUserItems.api_user_id = '".$this->api_user_id."'
                            ORDER BY author ASC
                            ";
						$icp_citations = $wpdb->get_results( $icp_citations_query );
                    }
                    
                    // print $icp_citations_query;
                    
                    // DISPLAY EACH ENTRY
                    
                    $entry_zebra = true;
                    
                    if (count($icp_citations) == 0)
                    {
                        echo "<p>There are no citations to display.";
						if ( is_admin() ) echo " If you think you're receiving this message in error, you may need to <a title=\"Import your Zotero items\" href=\"admin.php?page=ICPress&setup=true&setupstep=three&api_user_id=".$this->api_user_id."\" style=\"color: #f00000; text-shadow: none;\">import your Zotero library</a>.";
						echo "</p>";
                    }
                    else // display
                    {
                        foreach ($icp_citations as $entry)
                        {
                            $citation_id = $entry->item_key;
                            $citation_content = htmlentities( $entry->citation, ENT_QUOTES, "UTF-8", true );
                            
                            $icp_thumbnail = false;
                            //if ( has_post_thumbnail( $entry->ID ) ) $icp_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $entry->ID ) );
                            if ( !is_null($entry->itemImage) ) $icp_thumbnail = wp_get_attachment_image_src($entry->itemImage);
                            
                            if ($entry_zebra === true) echo "<div class='icp-Entry'>\n"; else echo "<div class='icp-Entry odd'>\n";
                            
							
                            // START OF DISPLAY IMAGE
							
                            if ( is_admin() || $icp_thumbnail !== false ) echo "<div id='icp-Citation-".$citation_id."' class='icp-Entry-Image";
                            if ( $icp_thumbnail !== false ) echo " hasimage";
                            if ( is_admin() || $icp_thumbnail !== false ) echo "' rel='".$citation_id."'>\n";
                            
                            $citation_image = "";
							
                            if ( is_admin() ) $citation_image .= "<a title='Set Image' class='upload' rel='".$entry->item_key."' href='media-upload.php?post_id=".$entry->id."&type=image&TB_iframe=1'>Set Image</a>\n";
                            
                            if ( $icp_thumbnail !== false )
                            {
                                if ( is_admin() ) $citation_image .= "<a title='Remove Image' class='delete' rel='".$entry->id."' href='".ICPRESS_PLUGIN_URL."lib/actions/actions.php?remove=image&amp;entry_id=".$entry->id."'>&times;</a>\n";
                                $citation_image .= "<img class='thumb' src='".$icp_thumbnail[0]."' alt='image' />\n";
                            }
                            
                            echo $citation_image;
                            if ( is_admin() || $icp_thumbnail !== false ) echo "</div><!-- .icp-Entry-Image -->\n";
							
							// END OF DISPLAY IMAGE
							
                            
                            // DISPLAY CONTENT
                            echo html_entity_decode($citation_content, ENT_QUOTES)."\n";

                            if ( is_admin() ) echo "<div class='icp-Entry-ID'><span class='title'>Item Key:</span> <div class='icp-Entry-ID-Text'><span>".$citation_id."</span><input value='".$citation_id."' /></div><br/><a href=\"admin.php?page=ICPress&citation=true&edit=citation&amp;entry_id=".$entry->id."\">Edit ".$citation_id."</a></div>\n";
                            echo "</div>\n\n";
                            
                            // Zebra striping
                            if ($entry_zebra === true) $entry_zebra = false; else $entry_zebra = true;
                        }

                            if ($entry_zebra === true) echo "<div class='icp-Entry'>\n"; else echo "<div class='icp-Entry odd'>\n";
                        	    if ( is_admin() ) echo "<div class='icp-Entry-ID'><div class='icp-Entry-ID-Text'></div><br/><a href=\"admin.php?page=ICPress&citation=true&add=citation\">Add New Record</a></div>\n";
                        	    	echo "</div>\n\n";
								echo "</div>\n\n";
							echo "</div>\n\n";							
                    }
                    
                    ?>
                
                </div><!-- #icp-List -->
                
                <div id="icp-Pagination">
                    <div id="icp-PaginationInner">
                        <span class="icp-Pagination-Total">
                            Showing <?php echo count($icp_citations); if ( count($icp_citations) == 1 ) echo " entry"; else echo " entries"; unset($icp_citations); ?>
                        </span>
                    </div><!-- #icp-PaginationInner -->
                </div><!-- #icp-Pagination -->
                
            </div><!-- #icp-Browse -->
		<?php
	}
}
 
?>