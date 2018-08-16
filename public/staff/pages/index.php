<?php require_once('../../../private/initialize.php'); ?>
<?php require_login();?>
<?php
  $pages_set = find_all_pages();
?>

<?php $page_title = 'Pages'; ?>
<?php 
  require_login();
  redirect_to(url_for('/staff/subjects/index.php'))
?>