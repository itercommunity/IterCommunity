<?php

    if ( (isset($_GET['accounts']) && $_GET['accounts'] == "true")
			|| (isset($_GET['selective']) && $_GET['selective'] == "true")
			|| (isset($_GET['import']) && $_GET['import'] == "true")
		)
        $tagpage = "accounts";
    else if ( isset($_GET['options']) && $_GET['options'] == "true" )
        $tagpage = "options";
    else if ( isset($_GET['help']) && $_GET['help'] == "true" )
        $tagpage = "help";
    else
        $tagpage = "default";

?>

<div id="icp-ICPress-Navigation">

    <div id="icp-Icon" title="Zotero + WordPress = ICPress"><br /></div>

    <div class="nav">
        <a class="nav-item <?php if ($tagpage == "default") echo "nav-tab-active"; ?>" href="admin.php?page=ICPress">Browse</a>
        <?php if ( current_user_can('edit_others_posts') ) { ?><a class="nav-item <?php if ($tagpage == "accounts") echo "nav-tab-active"; ?>" href="admin.php?page=ICPress&amp;accounts=true">Accounts</a><?php } ?>
        <?php if ( current_user_can('edit_others_posts') ) { ?><a class="nav-item <?php if ($tagpage == "options") echo "nav-tab-active"; ?>" href="admin.php?page=ICPress&amp;options=true">Options</a><?php } ?>
        <a class="nav-item <?php if ($tagpage == "help") echo "nav-tab-active"; ?>" href="admin.php?page=ICPress&amp;help=true">Help</a>
    </div>

</div><!-- #icp-ICPress-Navigation -->