<?php
require_once '../../../wp-load.php';

foreach ($_REQUEST as $keys=>$values) {
	$$keys = $values;
}
$subject = "(" . $bloginfo . ") Contact Form Received";
$message = '
	<table width="100%" border="1">
	  <tr>
		<td width="100"><strong>Name:</strong></td>
		<td>'.$contact_name.'</td>
	  </tr>
	  <tr>
		<td><strong>Email:</strong></td>
		<td>'.$contact_email.'</td>
	  </tr>
	  <tr>
		<td><strong>Subject:</strong></td>
		<td>'.$subject.'</td>
	  </tr>
	  <tr>
		<td><strong>Message:</strong></td>
		<td>'.$contact_msg.'</td>
	  </tr>
	  <tr>
		<td><strong>IP Address:</strong></td>
		<td>'.$_SERVER["REMOTE_ADDR"].'</td>
	  </tr>
	</table>
	';
$headers = "From: " . $contact_name . "\r\n";
$headers .= "Reply-To: " . $contact_email . "\r\n";
$headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
$headers .= "MIME-Version: 1.0" . "\r\n";
$attachments = '';
wp_mail( $cs_contact_email, $subject, $message, $headers, $attachments );
echo "<p>".cs_textarea_filter($cs_contact_succ_msg)."</p>";
?>