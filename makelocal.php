<?php

// make local : pull remote data and posts it as output so that it does not look like remote data that would be barred from use inside of Javascript

$url = $_GET['u'];
filter_var($url, FILTER_VALIDATE_URL);

if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
    die('Not a valid URL');
}
else {
	print @file_get_contents($url);
}

?>