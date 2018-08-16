<?php require_once('../../../private/initialize.php'); ?>
<?php include_once(SHARED_PATH.'/staff_header.php');?>
<?php require_login();?>
<?php 
$id = h($_GET['id'] ?? '1');

$subject = find_subject_by_id($id);
$pages_set = find_pages_by_subject_id($subject['id']);
?>

<a href="<?php echo url_for('/staff/subjects/index.php')?>"> &laquo; Back to Staff Subject List</a></br>
<div class="attributes">
<h1>Subject: <?php echo h($subject['menu_name']); ?>  </h1>
    <dl>
        <dt>Menu Name</dt>
        <dd><?php echo h($subject['menu_name']);?></dd>
    </dl>
    <dl>
        <dt>Position</dt>
        <dd><?php echo h($subject['position']);?></dd>
    </dl>
    <dl>
        <dt>Visible</dt>
        <dd><?php echo $subject['visible']==1?'true':'false';?></dd>
    </dl>
</div>
<hr>

<div class="pages listing">
    <h2>Pages</h2>

    <div class="actions">
      <a class="action" href="<?php echo url_for('/staff/pages/new.php?subject_id='.h(u($subject['id']))); ?>">Create New Page</a>
    </div>

  	<table class="list">
  	  <tr>
        <th>Page</th>
        <th>Position</th>
        <th>Visible</th>
  	    <th>Name</th>
  	    <th>&nbsp;</th>
  	    <th>&nbsp;</th>
        <th>&nbsp;</th>
  	  </tr>

      <?php while($pages = mysqli_fetch_assoc($pages_set)) { ?>
        <tr>
          <td><?php echo $pages['page']; ?></td>
          <td><?php echo $pages['position']; ?></td>
          <td><?php echo $pages['visible'] == 1 ? 'true' : 'false'; ?></td>
    	    <td><?php echo $pages['menu_name']; ?></td>
          <td><a class="action" href="<?php echo url_for('/staff/pages/show.php?page='.h(u($pages['page']))); ?>">View</a></td>
          <td><a class="action" href="<?php echo url_for('/staff/pages/edit.php?page='.h(u($pages['page']))); ?>">Edit</a></td>
          <td><a class="action" href="<?php echo url_for('/staff/pages/delete.php?page='.h(u($pages['page']))); ?>">Delete</a></td>
    	  </tr>
      <?php } ?>
  	</table>
        <?php mysqli_free_result($pages_set); ?>
  </div>
<?php include_once(SHARED_PATH.'/staff_footer.php');?>