<?php require_once('../../../private/initialize.php'); 
require_login();
if(!isset($_GET['page'])){
    redirect_to(url_for('/staff/pages/index.php'));
}

$page = $_GET['page'];

if(is_post_request()) {

  // Handle form values sent by new.php
  $pages = [];
  $pages['page'] = $page;
  $pages['menu_name'] = $_POST['menu_name'] ?? '';
  $pages['position'] = $_POST['position'] ?? '';
  $pages['visible'] = $_POST['visible'] ?? '';
  $pages['subject_id'] = $_POST['subject_id'] ?? '';
  $pages['content'] = $_POST['content'] ?? '';

  $result = update_page($pages);
  if($result === true){
    redirect_to(url_for('/staff/pages/show.php?page='.$page));
  } else {
    $errors = $result;
    // display_errors($errors);
  }
} else {
  $pages = find_pages_by_page($page);
  
}

$subjects_set = find_all_subjects();
$subject_count = mysqli_num_rows($subjects_set);
mysqli_free_result($subjects_set);  
$page_count = count_pages_by_subject_id($pages['subject_id']);
?>


<?php $page_title = 'Edit Page'; ?>
<?php include(SHARED_PATH . '/staff_header.php'); ?>

<div page="content">

  <a href="<?php echo url_for('/staff/subjects/show.php?id='.h(u($pages['subject_id'])));?>"> &laquo; Back to Subject page</a></br>

  <div class="page new">
    <h1>Edit Page</h1>
    <?php echo display_errors($errors);?>
    <form action="<?php echo url_for('/staff/pages/edit.php?page='.h(u($page))); ?>" method="post">
      <dl>
        <dt>Menu Name</dt>
        <dd><input type="text" name="menu_name" value="<?php echo $pages['menu_name']; ?>" /></dd>
      </dl>
      <dl>
        <dt>Position</dt>
        <dd>
          <select name="position">
            <?php 
             for($i=1; $i <= $page_count; $i++){
                 echo "<option value=\"{$i}\"";
                 if($pages['position']== $i) {
                     echo " selected";
                 }
                 echo ">{$i}</options>";
             }
            ?>
          </select>
        </dd>
      </dl>
      <dl>
        <dt>Subject Id</dt>
        <dd>
          <select name="subject_id">
          <?php 
             for($i=1; $i <= $subject_count; $i++){
                 echo "<option value=\"{$i}\"";
                 if($pages['subject_id']== $i) {
                     echo " selected";
                 }
                 echo ">".find_subject_by_id($i)['menu_name']."</options>";
             }
            ?>
          </select>
        </dd>
      </dl>
      <dl>
        <dt>Visible</dt>
        <dd>
          <input type="hidden" name="visible" value="0" />
          <input type="checkbox" name="visible" value="1"<?php if($pages['visible']=="1"){ echo "checked";}; ?> />
        </dd>
      </dl>
      <dl>
        <dt>Content</dt>
        <dd>
        <textarea cols="60" rows="6" name="content"><?php echo $pages['content']; ?></textarea>
        </dd>
      </dl>
      <div page="operations">
        <input type="submit" value="Edit Page" />
      </div>
    </form>

  </div>

</div>

<?php include(SHARED_PATH . '/staff_footer.php'); ?>
