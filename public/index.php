<?php require_once('../private/initialize.php'); 
$preview = $_GET['preview']??false;

?>

<?php
    if(isset($_GET['page'])) {
        $page_id = $_GET['page'];
        $page = find_pages_by_page($page_id,['visible'=>true,'preview'=>($preview && is_logged_in())]);
        if(!$page) {
            redirect_to(url_for('/index.php'));
        }
        $subject_id = $page['subject_id'];
        $subject = find_pages_by_subject_id($subject_id,['visible'=>true,'preview'=>$preview]);
        if(!$subject) {
            redirect_to(url_for('/index.php'));
        }
    } elseif(isset($_GET['subject_id'])) {
        $subject_id = $_GET['subject_id'];
        $subject = find_subject_by_id($subject_id,['visible'=>true,'preview'=>$preview]);
        if(!$subject) {
            redirect_to(url_for('/index.php'));
        }
        $page_set = find_pages_by_subject_id($subject_id,['visible'=>true,'preview'=>$preview]);
        $page = mysqli_fetch_assoc($page_set); // first page
        if(!$page) {
        redirect_to(url_for('/index.php'));
        }
        mysqli_free_result($page_set);
        $page_id = $page['page'];
    } else {
        // nothing selected; show the homepage
    }

?>

<?php include(SHARED_PATH . '/public_header.php'); ?>

<div id="main">

	<?php include(SHARED_PATH . '/public_navigation.php'); ?>

	<div id="page">

		<?php
      if(isset($page)) {
        // show the page from the database
        // TODO add html escaping back in
        $allowed_tags = '<div><img><h1><h2><p><br><string><em><ul><li>';
        echo strip_tags($page['content'],$allowed_tags);

      } else {
        // Show the homepage
        // The homepage content could:
        // * be static content (here or in a shared file)
        // * show the first page from the nav
        // * be in the database but add code to hide in the nav
        include(SHARED_PATH . '/static_homepage.php');
      }
    ?>

	</div>

</div>

<?php include(SHARED_PATH . '/public_footer.php'); 