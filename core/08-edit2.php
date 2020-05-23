<?php
// No <head> yet because we might redirect, which uses header() and might break after the <head> tag

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our piece functions
include ('./in.piecefunctions.php');

// Include our login cluster
$head_title = "Editor"; // Set a <title> name used next
include ('./in.login_check.php');

// Include our POST processor
include ('./in.editpiece.php');

// Title the page so we know where we are

echo '<h1>Editor</h1>';

// Our edit form
echo '<form action="edit.php" method="post">';

// Tell in.checks.php that this is a "Piece" form
echo '<input type="hidden" name="piece"><br>';

// Create the fields
echo 'Title: '.pieceInput('p_title', $p_title, $check_err).'<br><br>';
echo 'Slug: '.pieceInput('p_slug', $p_slug, $check_err).'<br><br>';
echo 'Type:<br>'.pieceInput('p_type', $p_type, $check_err).'<br><br>';
echo 'Status: '.pieceInput('p_status', $p_status, $check_err).'<br><br>';
echo 'Date live: '.pieceInput('p_live', $p_live, $check_err).'<br><br>';
echo 'Content: '.pieceInput('p_content', $p_content, $check_err).'<br><br>';
echo 'After: '.pieceInput('p_after', $p_after, $check_err).'<br><br>';

// Two submit buttons
echo '<input type="submit" name="p_submit" value="Save draft">';
echo '<input type="submit" name="p_submit" value="Publish">';
echo '</form>';

?>

</body>
</html>
