<?php require_once('../../../private/initialize.php'); ?>
<?php include_once(SHARED_PATH.'/staff_header.php');?>
<?php require_login();?>
<?php 
$page = h($_GET['page'] ?? '1');

$pages = find_pages_by_page($page);

?>

<a href="<?php echo url_for('/staff/subjects/show.php?id='.h(u($pages['subject_id'])));?>"> &laquo; Back to Subject page</a></br>
<div class="attributes">
<h1>Subject: <?php echo h($pages['menu_name']); ?>  </h1>
    <dl>
        <dt>Menu Name</dt>
        <dd><?php echo h($pages['menu_name']);?></dd>
    </dl>
    <dl>
        <dt>Subject Id</dt>
        <dd><?php echo find_subject_by_id($pages['subject_id'])['menu_name'];?></dd>
    </dl>
    <dl>
        <dt>Position</dt>
        <dd><?php echo h($pages['position']);?></dd>
    </dl>
    <dl>
        <dt>Visible</dt>
        <dd><?php echo $pages['visible']==1?'true':'false';?></dd>
    </dl>
    <dl>
        <dd><a class="action" href="<?php echo url_for('/index.php?page='.h(u($pages['page']))).'&preview=true'; ?>" target="_blank">Preview</a></dd>
    </dl>
    <dl>
        <dt>Content</dt>
        <dd><?php echo h($pages['content']);?></dd>
    </dl>
</div>

<?php include_once(SHARED_PATH.'/staff_footer.php');?>