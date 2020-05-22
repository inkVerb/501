<?php
// No <head> yet because we might redirect, which uses header() and might break after the <head> tag

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our functions
include ('./in.functions.php');

// Include our login cluster
$head_title = "Editor"; // Set a <title> name used next
include ('./in.login_check.php');

// Include our POST processor
include ('./in.editpiece.php');

// Title the page so we know where we are

echo '<h1>Editor</h1>';

// Our edit form
echo '<form action="edit.php">';

// Tell in.checks.php that this is a "Piece" form
echo '<input type="hidden" name="piece"><br>';

// Create the fields
echo 'Title: '.formInput('p_title', $p_title, $check_err).'<br><br>';
echo 'Slug: '.formInput('p_slug', $p_slug, $check_err).'<br><br>';
echo 'Type:<br>'.formInput('p_type', $p_type, $check_err).'<br><br>';
echo 'Status: '.formInput('p_status', $p_status, $check_err).'<br><br>';
echo 'Date live: '.formInput('p_live', $p_live, $check_err).'<br><br>';
echo 'Content: '.formInput('p_content', $p_content, $check_err).'<br><br>';
echo 'After: '.formInput('p_after', $p_after, $check_err).'<br><br>';

// Two submit buttons
echo '<input type="submit" value="Save draft">';
echo '<input type="submit" value="Publish">';
echo '</form>';

?>

</body>
</html>
