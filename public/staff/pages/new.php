<?php require_once('../../../private/initialize.php'); 
    require_login();
    $subject_id = $_GET['subject_id'];
    if(is_post_request()) {
        // Handle form values sent by new.php
        $pages = []; 
        $pages['menu_name'] = $_POST['menu_name'] ?? '';
        $pages['subject_id'] = $_POST['subject_id']?? '';
        $pages['position'] = $_POST['position']??'';
        $pages['visible'] = $_POST['visible'] ?? '';
        $pages['content'] = $_POST['content'] ?? '';
        
        $flag = has_unique_page_name($pages);
        if(!$flag){
            $errors[] = "The menu name is already taken.";
        }
        
        $result = insert_page($pages);

        if($result === true){
            $new_page = mysqli_insert_id($db);
            redirect_to(url_for('/staff/pages/show.php?page='.$new_page));
          } else {
            $errors = $result;
            // display_errors($errors);
        }
    }

    
    $subjects_set = find_all_subjects();
    $subject_count = mysqli_num_rows($subjects_set);
    mysqli_free_result($subjects_set);
    $pages = []; 
    $page_count = count_pages_by_subject_id($subject_id) + 1;
    $pages['position'] = $page_count;
?>


<?php $page_title = 'Create page'; ?>
<?php include(SHARED_PATH . '/staff_header.php'); ?>

<div id="content">

  <a href="<?php echo url_for('/staff/subjects/show.php?id='.$subject_id);?>"> &laquo; Back to Subject page</a></br>

  <div class="page new">
    <h1>Create page</h1>
    <?php echo display_errors($errors);?>
    <form action="<?php echo url_for('/staff/pages/new.php');?>" method="post">
      <dl>
        <dt>Menu Name</dt>
        <dd><input type="text" name="menu_name" value="" /></dd>
      </dl>
      <dl>
        <dt>Subject Id</dt>
        <dd>
          <select name="subject_id">
          <?php 
             for($i=1; $i <= $subject_count; $i++){
                 echo "<option value=\"{$i}\"";
                 if($pages['position']== $i) {
                     echo " selected";
                 }
                 echo ">".find_subject_by_id($i)['menu_name']."</options>";
             }
            ?>
          </select>
        </dd>
      </dl>
      <dl>
        <dt>Position</dt>
        <dd>
          <select name="position">
          <?php 
             for($i=1; $i <= $page_count; $i++){
                 echo "<option value=\"{$i}\"";
                 if($i==$page_count) {
                     echo " selected";
                 }
                 echo ">{$i}</options>";
             }
            ?>
          </select>
        </dd>
      </dl>
      <dl>
        <dt>Visible</dt>
        <dd>
          <input type="hidden" name="visible" value="0" />
          <input type="checkbox" name="visible" value="1" />
        </dd>
      </dl>
      <dl>
        <dt>Content</dt>
        <dd>
          <textarea cols="60" rows="6" name="content"></textarea>
        </dd>
      </dl>
      <div id="operations">
        <input type="submit" value="Create page" />
      </div>
    </form>

  </div>

</div>

<?php include(SHARED_PATH . '/staff_footer.php'); ?>
