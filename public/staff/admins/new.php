<?php require_once('../../../private/initialize.php'); 
  require_login();
  $admin = [];
  if(is_post_request()) {

    // Handle form values sent by new.php
    $admin = [];
    $admin['username'] = $_POST['username'] ?? '';
    $admin['first_name'] = $_POST['first_name'] ?? '';
    $admin['last_name'] = $_POST['last_name'] ?? '';
    $admin['email'] = $_POST['email'] ?? '';
    $admin['password'] = $_POST['password'] ?? '';
    $admin['confirm_password'] = $_POST['confirm_password'] ?? '';

    $result = insert_admin($admin);
    if($result === true){
      $new_id = mysqli_insert_id($db);
      redirect_to(url_for('/staff/admins/show.php?id='.$new_id));
    } else {
      $errors = $result;
      // display_errors($errors);
    }
    

    
} 
?>


<?php $page_title = 'Create admin'; ?>
<?php include(SHARED_PATH . '/staff_header.php'); ?>

<div id="content">

  <a class="back-link" href="<?php echo url_for('/staff/admins/index.php'); ?>">&laquo; Back to List</a>

  <div class="admin new">
    <h1>Create admin</h1>
    <?php echo display_errors($errors);?>
    <form action="<?php echo url_for('/staff/admins/new.php');?>" method="post">
      <dl>
        <dt>Username</dt>
        <dd><input type="text" name="username" value="" /></dd>
      </dl>
      <dl>
        <dt>Full name</dt>
        <dd><input type="text" name="first_name" value="" /></dd>
        <dd><input type="text" name="last_name" value="" /></dd>
      </dl>
      <dl>
        <dt>Email</dt>
        <dd><input type="email" name="email" value="" /></dd>
      </dl>
      <dl>
        <dt>Password</dt>
        <dd><input type="password" name="password" value="" /></dd>
      </dl>
      <dl>
        <dt>Confirm password</dt>
        <dd><input type="password" name="confirm_password" value="" /></dd>
      </dl>
      
      
      <div id="operations">
        <input type="submit" value="Create admin" />
      </div>
    </form>

  </div>

</div>

<?php include(SHARED_PATH . '/staff_footer.php'); ?>
