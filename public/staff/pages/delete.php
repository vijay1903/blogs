<?php

require_once('../../../private/initialize.php');
require_login();
if(!isset($_GET['page'])) {
  redirect_to(url_for('/staff/pages/index.php'));
}
$page = $_GET['page'];
$pages = find_pages_by_page($page);
$pages['subject_id'];
if(is_post_request()) {
    $subject_id = $pages['subject_id'];
    $result = delete_page($page);
    redirect_to(url_for('/staff/subjects/show.php?id='.h(u($pages['subject_id']))));
} 

?>

<?php $page_title = 'Delete Page'; ?>
<?php include(SHARED_PATH . '/staff_header.php'); ?>

<div id="content">

  <a href="<?php echo url_for('/staff/subjects/show.php?id='.h(u($pages['subject_id'])));?>"> &laquo; Back to Subject page</a></br>

  <div class="subject delete">
    <h1>Delete Page</h1>
    <p>Are you sure you want to delete this page?</p>
    <p class="item"><?php echo h($pages['menu_name']); ?></p>

    <form action="<?php echo url_for('/staff/pages/delete.php?page=' . h(u($pages['page']))); ?>" method="post">
      <div id="operations">
        <input type="submit" name="commit" value="Delete Page" />
      </div>
    </form>
  </div>

</div>

<?php include(SHARED_PATH . '/staff_footer.php'); ?>
