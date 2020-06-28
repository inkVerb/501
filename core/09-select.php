<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our pieces functions
include ('./in.piecesfunctions.php');

// Include our login cluster
$head_title = "AJAX form select example"; // Set a <title> name used next
$edit_page_yn = false; // Include JavaScript for TinyMCE?
include ('./in.login_check.php');



// Set a default Series, probably from settings table
$de_series = (isset($_SESSION['de_series'])) ? $_SESSION['de_series'] : 1;

// Accept any set value
$p_series = (isset($p_series)) ? $p_series : $de_series;
include ('./in.select.php');

// Footer
include ('./in.footer.php');
