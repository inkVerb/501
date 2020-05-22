<?php
// No <head> yet because we might redirect, which uses header() and might break after the <head> tag

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our functions
include ('./in.functions.php');

// Include our login cluster
$head_title = "Editor"; // Set a <title> name used next
include ('./in.login_check.php');

// Title the page so we know where we are

echo '<h1>Editor</h1>';

// Our edit form
echo '<p><b>[ This is where our post editing form will go. ]</b></p>';

?>

</body>
</html>
