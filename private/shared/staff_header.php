<!doctype html>

<html lang="en">
    <head>
        <title>Blogs-<?php echo $page_title??'Staff'?></title>
        <meta charset="utf-8">
    </head>
    <link rel="stylesheet" media="all" href="<?php echo url_for('/stylesheets/staff.css');?>" />
    <body>
        <header>
            <h1> Blog - Staff Area </h1>
        </header>
        <navigation>
            <ul>
                <li>User: <?php echo $_SESSION['username']??''?></li>
                <li><a href="<?php echo url_for('/staff/index.php'); ?>">Menu</a></li>
                <li><a href="<?php echo url_for('/staff/logout.php'); ?>">Logout</a></li>
            </ul>
        </navigation>
        <?php if(isset($_SESSION['message'])){
            echo display_session_message();
            unset($_SESSION['message']);
        }?>