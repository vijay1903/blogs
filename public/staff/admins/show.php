<?php require_once('../../../private/initialize.php'); ?>
<?php include_once(SHARED_PATH.'/staff_header.php');?>
<?php require_login();?>
<?php 
$id = h($_GET['id'] ?? '1');

$admin = find_admin_by_id($id);

?>

<a href="<?php echo url_for('/staff/admins/index.php')?>"> &laquo; Back to Staff admin List</a></br>
<div class="attributes">
<h1>Admin Name: <?php echo h($admin['first_name']).' '. h($admin['last_name']); ?>  </h1>
    <dl>
        <dt>Id</dt>
        <dd><?php echo h($admin['id']);?></dd>
    </dl>
    <dl>
        <dt>Username</dt>
        <dd><?php echo h($admin['username']);?></dd>
    </dl>
    <dl>
        <dt>Email</dt>
        <dd><?php echo h($admin['email']);?></dd>
    </dl>
</div>

<?php include_once(SHARED_PATH.'/staff_footer.php');?>