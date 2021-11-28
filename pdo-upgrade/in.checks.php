<?php

  if (!empty($_POST['website'])) {
    $website = checkPost('website',$_POST['website']);
  }

  if (!empty($_POST['email'])) {
    $email = checkPost('email',$_POST['email']);
  }

  if (!empty($_POST['favnumber'])) {
    $favnumber = checkPost('favnumber',$_POST['favnumber']);
  }

  if (!empty($_POST['fullname'])) {
    $fullname = checkPost('fullname',$_POST['fullname']);
  }

  if (!empty($_POST['blog_public'])) {
    $new_blog_public = checkPost('blog_public',$_POST['blog_public']);
  }

  if (!empty($_POST['blog_title'])) {
    $new_blog_title = checkPost('blog_title',$_POST['blog_title']);
  }

  if (!empty($_POST['blog_tagline'])) {
    $new_blog_tagline = checkPost('blog_tagline',$_POST['blog_tagline']);
  }

  if (!empty($_POST['blog_description'])) {
    $new_blog_description = checkPost('blog_description',$_POST['blog_description']);
  }

  if (!empty($_POST['blog_keywords'])) {
    $new_blog_keywords = checkPost('blog_keywords',$_POST['blog_keywords']);
  }

  if (!empty($_POST['blog_summary_words'])) {
    $new_blog_summary_words = checkPost('blog_summary_words',$_POST['blog_summary_words']);
  }

  if (!empty($_POST['blog_piece_items'])) {
    $new_blog_piece_items = checkPost('blog_piece_items',$_POST['blog_piece_items']);
  }

  if (!empty($_POST['blog_feed_items'])) {
    $new_blog_feed_items = checkPost('blog_feed_items',$_POST['blog_feed_items']);
  }

  if (!empty($_POST['blog_crawler_index'])) {
    $new_blog_crawler_index = checkPost('blog_crawler_index',$_POST['blog_crawler_index']);
  }

  if (!empty($_POST['username'])) {
    $username = checkPost('username',$_POST['username']);
  }

  if (!empty($_POST['password'])) {
    $password = checkPost('password',$_POST['password']);
  }

  // Test only to see if passwords match
  if ((!empty($_POST['password2'])) && (!empty($_POST['password']))) {
    if ($_POST['password'] != $_POST['password2']) {
      $check_err['password2'] = 'Passwords much match!';
    }
  }

  // Set a variable if $check_err is empty
  if (empty($check_err)) {
    $no_form_errors = true;
  }
