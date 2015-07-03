<p class="c3"><span>This is a copy of the WordPress core and associated plugins the Iter Community Commons site uses to serve the Iter Community when set up in this established configuration. As much of the functionality comes from the configuration. Those settings are preserved in an export of the Iter Commons database.</span></p><p class="c3 c2"><span></span></p><p class="c3"><span>To use this repository:</span></p><ul class="c9 lst-kix_89g5v5b209ak-0 start"><li class="c3 c5"><span>Get the current version of the repository: </span><span class="c10"><a class="c0" href="https://www.google.com/url?q=https%3A%2F%2Fgithub.com%2Fitercommunity%2FIterCommunity%2Fcommits%2Fmaster&amp;sa=D&amp;sntz=1&amp;usg=AFQjCNGnkRGn-6Ff3cHbJDRAJz1qcxy1sw">https://github.com/itercommunity/IterCommunity/commits/master</a></span></li><li class="c3 c5"><span>Prepare a MySQL database. Retain the host, database name, username and database password as credentials you will use later to connect your site to your data source.</span></li><li class="c3 c5"><span>Prepare a web directory to house the repository.</span></li><li class="c3 c5"><span>Post the repository files to the public web directory. </span></li><li class="c3 c5"><span>Edit the wp-config.php.</span></li></ul><ul class="c9 lst-kix_89g5v5b209ak-1 start"><li class="c1 c7"><span>Change these lines with the credentials you have for your data source.</span></li></ul>
<p class="c1">
<span class="c6">
<pre>
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define(&#39;DB_NAME&#39;, &#39;cleaned&#39;);
/** MySQL database username */
define(&#39;DB_USER&#39;, &#39;cleaned&#39;);
/** MySQL database password */
define(&#39;DB_PASSWORD&#39;, &#39;gittwpi1&#39;);
/** MySQL hostname */
define(&#39;DB_HOST&#39;, &#39;localhost&#39;
... (ignore) ...
define(&#39;WP_HOME&#39;,&#39;http://iter-dev.utsc.utoronto.ca/cleaned&#39;);
define(&#39;WP_SITEURL&#39;,&#39;http://iter-dev.utsc.utoronto.ca/cleaned&#39;);
</pre>
<ul class="c9 lst-kix_89g5v5b209ak-0"><li class="c3 c5"><span>Import the MySQL database, itercommons.sql, into your new MySQL database.</span></li><li class="c3 c5"><span>Replace references to our settings with your own references:</span></li></ul><ul class="c9 lst-kix_89g5v5b209ak-1 start"><li class="c1 c7"><span>Implant a new email address for the main admin and for the secondary admin on the site. These two SQL statements (below) &nbsp;when run against the database will update your install. 
When you attempt to login, [your site]/wp-login.php, use the &ldquo;Request New Password&rdquo; and your preferred email address to spawn the WordPress install to give you a means to insert a new password into your site.<br></span>
<span class="c6">
<pre>
UPDATE `icc_users` SET `user_email` = &lsquo;new email&rsquo; WHERE `ID` = 1;
UPDATE `icc_users` SET `user_email` = &lsquo;other new email&rsquo; WHERE `ID` = 3;
</pre>
</span></li><li class="c1 c7"><span>Update the site settings that are stored in the database. If you are comfortable with how SQL statement reads, it will make sense how to update our default values for your new values.<br></span>
<span class="c6">
<pre>
UPDATE `icc_options` SET `option_value` = &#39;your site url&#39; WHERE `option_name` = &#39;siteurl&#39;;
UPDATE `icc_options` SET `option_value` = &#39;text name&#39; WHERE `option_name` = &#39;blogname&#39;;
UPDATE `icc_options` SET `option_value` = &#39;text description&#39; WHERE `option_name` = &#39;blogdescription&#39;;
UPDATE `icc_options` SET `option_value` = &#39;your primary email address&#39; WHERE `option_name` = &#39;admin_email&#39;;
UPDATE `icc_options` SET `option_value` = &#39;your site url&#39; WHERE `option_name` = &#39;home&#39;;
UPDATE `icc_bp_user_blogs_blogmeta` SET `meta_value` = &#39;your site url&#39; WHERE `meta_key` = &#39;url&#39;;
UPDATE `icc_bp_user_blogs_blogmeta` SET `meta_value` = &#39;text name&#39; WHERE `meta_key` = &#39;name&#39;;
UPDATE `icc_bp_user_blogs_blogmeta` SET `meta_value` = &#39;text description&#39; WHERE `meta_key` = &#39;description&#39;;
UPDATE `icc_posts` SET `guid` = REPLACE(`guid`, &#39;http://iter-dev.utsc.utoronto.ca/&#39;, &#39;your new site url&#39;);
</pre>
</span></li><li class="c1 c7"><span>There are two premium elements to this site: the Gravity Forms plugin and the Stat Fort theme. To use these elements, you will need to purchase your own support: </span><span class="c10"><a class="c0" href="http://www.google.com/url?q=http%3A%2F%2Fwww.gravityforms.com%2Fpurchase-gravity-forms%2F&amp;sa=D&amp;sntz=1&amp;usg=AFQjCNEz55-WSS7LD4kPeHmb-NClsHbj5Q">http://www.gravityforms.com/purchase-gravity-forms/</a></span><span>&nbsp;and </span><span class="c10"><a class="c0" href="http://www.google.com/url?q=http%3A%2F%2Fthemeforest.net%2Fitem%2Fstatfort-educational-wordpress-theme%2F6839697&amp;sa=D&amp;sntz=1&amp;usg=AFQjCNEIT3TPoPB9wZ10aQsmayoIyL2WXw">http://themeforest.net/item/statfort-educational-wordpress-theme/6839697</a></span><span>&nbsp;respectively.</span></li><li class="c1 c7"><span>After logging into the site as an administrator, the next task will be to assess what elements you want to keep. Some of the elements only exist to serve as demonstrations of the utility of the site and its function. Some content to consider removing. </span></li></ul><ul class="c9 lst-kix_89g5v5b209ak-2 start"><li class="c3 c4"><span>Page content</span></li><li class="c3 c4"><span>Menu elements</span></li><li class="c3 c4"><span>Plugins</span></li><li class="c3 c4"><span>Groups</span></li><li class="c3 c4"><span>Forums</span></li><li class="c3 c4"><span>Media (ie. images)</span></li></ul><ul class="c9 lst-kix_89g5v5b209ak-1"><li class="c1 c7"><span>After you install the site and make the updates, update the plugins to current version.</span></li><li class="c1 c7"><span>Delete the SQL file, itercommons.sql, from your install root, if you have not done so already. </span></li><li class="c1 c7"><span>After you have done that, make sure to secure your WordPress directories: </span>
<span class="c6">
<pre>
chown www-data:www-data -R * &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;# Apache ownership
find . -type d -exec chmod 755 {} \; &nbsp;# Change directories 
find . -type f -exec chmod 644 {} \; &nbsp;# Change files
</pre>
</span></li></ul>
