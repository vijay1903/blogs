<?php 
    require_once('../../private/initialize.php'); 
    require_login();
?>
<?php include(SHARED_PATH . '/staff_header.php'); ?>

<div id="content">
    <div id="main-menu">
        <ul>
            <li>
            <a href="<?php echo url_for('/staff/subjects/index.php'); ?>">Subjects</a>
            </li>
            <li>
            <a href="<?php echo url_for('/staff/admins/index.php'); ?>">Admins</a>
            </li>
        </ul>
    </div>
</div>

<?php include(SHARED_PATH . '/staff_footer.php'); ?>