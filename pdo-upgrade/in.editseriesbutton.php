<?php

// Edit series button
echo '<form id="edit-series-form">
      <input type="hidden" name="u_id" value="'.$user_id.'">
      <button type="button" class="postform link-button inline blue" onclick="seriesEditor(); seriesEditorShowHide();"><small>Edit all series</small></button>
      </form>';
echo '</p>';
