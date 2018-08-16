<?php 
    //Subject functions
    function find_all_subjects($options = []) {
        global $db;

        $visible = $options['visible'] ?? false;
        $preview = $options['preview'] ?? false;
        $sql = "SELECT * FROM subjects ";
        if($visible&&!$preview){
            $sql .= "WHERE visible = true ";
        }
        $sql .= "ORDER BY position ASC";
        $result = mysqli_query($db, $sql);
        confirm_result_set($result);
        return $result;
    }

    function find_subject_by_id($id,$options=[]){
        global $db;

        $visible = $options['visible'] ?? false;
        $preview = $options['preview'] ?? false;
        $sql = "SELECT * FROM subjects ";
        $sql .= "WHERE id='" . db_escape($db, $id) . "'";
        if($visible&&!$preview){
            $sql .= "AND visible = true ";
        }
        $result = mysqli_query($db,$sql);
        confirm_result_set($result);

        $subject = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        return $subject;
    }

    function validate_subject($subject) {
        $errors = [];
        
        // menu_name
        if(is_blank($subject['menu_name'])) {
            $errors[] = "Name cannot be blank.";
        } elseif(!has_length($subject['menu_name'], ['min' => 2, 'max' => 255])) {
            $errors[] = "Name must be between 2 and 255 characters.";
        }

        // position
        // Make sure we are working with an integer
        $postion_int = (int) $subject['position'];
        if($postion_int <= 0) {
            $errors[] = "Position must be greater than zero.";
        }
        if($postion_int > 999) {
            $errors[] = "Position must be less than 999.";
        }

        // visible
        // Make sure we are working with a string
        $visible_str = (string) $subject['visible'];
        if(!has_inclusion_of($visible_str, ["0","1"])) {
            $errors[] = "Visible must be true or false.";
        }

        return $errors;
    }

    function insert_subject($subject){
        global $db;

        $errors = validate_subject($subject);
        $shift = shift_subject_positions(0, $subject['position']);
        if(!$shift){
            echo mysqli_error($db);
            db_disconnect($db);
        }
        if(!empty($errors)){
            return $errors;
        }
        
        $sql = "INSERT INTO subjects ";
        $sql .= "(menu_name, position, visible) ";
        $sql .= "VALUES ('";
        $sql .= db_escape($db, $subject['menu_name']) . "', '";
        $sql .= db_escape($db, $subject['position']) . "', '";
        $sql .= db_escape($db, $subject['visible']) . "');";
        $result = mysqli_query($db,$sql);
        if($result){
            $_SESSION['message'] = "Subject added.";
            return true;
            
        } else {
            echo mysqli_error($db);
            db_disconnect($db);
        exit;
        }
    }

    function update_subject($subject){
        global $db;
        $errors = validate_subject($subject);

        if(!empty($errors)){
            return $errors;
        }
        $sub = find_subject_by_id($subject['id']);
        // $sub = mysqli_fetch_assoc($sub_set);
        $init_pos = $sub['position'];
        $shift = shift_subject_positions($init_pos, $subject['position'], $subject['id']);
        if(!$shift){
            echo mysqli_error($db);
            db_disconnect($db);
        }
        $sql = "UPDATE subjects SET ";
        $sql .= "menu_name='". db_escape($db, $subject['menu_name'])."',";
        $sql .= "position='". db_escape($db, $subject['position'])."',";
        $sql .= "visible='". db_escape($db, $subject['visible'])."' ";
        $sql .= "WHERE id='". db_escape($db, $subject['id'])."' ";
        $sql .= "LIMIT 1";
        echo $sql;
        $result = mysqli_query($db, $sql);
        if($result){
            $_SESSION['message'] = "Subject updated.";
            redirect_to(url_for('/staff/subjects/show.php?id=' . $subject['id']));
        } else {
            echo mysqli_error($db);
            db_disconnect($db);
        exit;
        }
    }

    function delete_subject($id){
        global $db;
        
        $sub = find_subject_by_id($id);
        // $sub = mysqli_fetch_assoc($sub_set);
        $init_pos = $sub['position'];
        $shift = shift_subject_positions($init_pos, 0, $id);
        if(!$shift){
            echo mysqli_error($db);
            db_disconnect($db);
        }
        $sql = "DELETE from subjects ";
        $sql .= "WHERE id='".db_escape($db, $id)."'";
        $sql .= "LIMIT 1";

        $result = mysqli_query($db,$sql);
        
        if($result){
            $_SESSION['message'] = "Subject deleted";
            return true;
        } else {
            echo mysqli_error($db);
            db_disconnect($db);
            exit;
        }
    }

    function shift_subject_positions($start_pos, $end_pos, $current_id=0){
        global $db;

        if($start_pos == $end_pos){ return;}
        $sql = "UPDATE subjects SET ";
        if($start_pos == 0){ // adding new item
            
            $sql .= "position = position + 1 ";
            $sql .= "WHERE position >= '".db_escape($db,$end_pos);
            
        } elseif($end_pos == 0){ // deleting an item
            
            $sql .= "position = position - 1 ";
            $sql .= "WHERE position > '".db_escape($db,$start_pos);
            
        } elseif($start_pos < $end_pos){ // move down
            $sql .= "position = position - 1 ";
            $sql .= "WHERE position > '".db_escape($db,$start_pos);
            $sql .= "' AND position <= '".db_escape($db,$end_pos);

        } elseif($start_pos > $end_pos){ // move up
            
            $sql .= "position = position + 1 ";
            $sql .= "WHERE position < '".db_escape($db,$start_pos);
            $sql .= "' AND position >= '".db_escape($db,$end_pos);
            
        }
        $sql .= "' AND id!='".db_escape($db,$current_id)."' ";
        $result = mysqli_query($db, $sql);

        if($result){
            return true;
        } else {
            echo "Subject position updating failed. ".mysqli_error;
            db_disconnect();
            exit;
        }
    }

    //Page functions

    function find_all_pages($options = []) {
        global $db;

        $visible = $options['visible'] ?? false;
        $preview = $options['preview'] ?? false;
        
        $sql = "SELECT * FROM pages ";
        if($visible&&!$preview){
            $sql .= "WHERE visible = true ";
        }
        $sql .= "ORDER BY subject_id ASC, position ASC";
        $result = mysqli_query($db, $sql);
        confirm_result_set($result);
        return $result;
    }

    function find_pages_by_page($page, $options=[]){
        global $db;
        $visible = $options['visible'] ?? false;
        $preview = $options['preview'] ?? false;
        
        $sql = "SELECT * FROM pages ";
        
        $sql .= "WHERE page='" . db_escape($db, $page) . "'";
        if($visible&&!$preview){
            $sql .= "AND visible = true ";
        }
        $result = mysqli_query($db,$sql);
        confirm_result_set($result);

        $subject = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        return $subject;
        
    }

    function find_pages_by_subject_id($subject_id, $options=[]){
        global $db;
        $visible = $options['visible'] ?? false;
        $preview = $options['preview'] ?? false;
        

        $sql = "SELECT * FROM pages ";
        
        $sql .= "WHERE subject_id='" . db_escape($db, $subject_id) . "' ";
        if($visible&&!$preview){
            $sql .= "AND visible = true ";
        }
        $sql .= "ORDER BY position ASC";
        $result = mysqli_query($db,$sql);
        confirm_result_set($result);
        return $result;
        
    }

    function count_pages_by_subject_id($subject_id, $options=[]){
        global $db;
        $visible = $options['visible'] ?? false;
        $preview = $options['preview'] ?? false;
        

        $sql = "SELECT count(page) FROM pages ";
        
        $sql .= "WHERE subject_id='" . db_escape($db, $subject_id) . "' ";
        if($visible&&!$preview){
            $sql .= "AND visible = true ";
        }
        $sql .= "ORDER BY position ASC";
        $result = mysqli_query($db,$sql);
        confirm_result_set($result);
        $row = mysqli_fetch_row($result);
        mysqli_free_result($result);
        $count = $row[0];
        return $count;
        
    }

    function has_unique_page_name($pages){
        global $db;
        $sql = "SELECT * FROM pages ";
        $sql .= "WHERE menu_name ='";
        $sql .= db_escape($db, $pages['menu_name'])."'";
        $result = mysqli_query($db, $sql);
        $temp = mysqli_fetch_assoc($result);
        if(empty($temp)){
            return true;
        } else {
            return false;
        }
    }

    function validate_page($pages) {
        global $db;
        $errors = [];

        
        
        // menu_name
        if(is_blank($pages['menu_name'])) {
            $errors[] = "Name cannot be blank.";
        } elseif(!has_length($pages['menu_name'], ['min' => 2, 'max' => 255])) {
            $errors[] = "Name must be between 2 and 255 characters.";
        }
        // //subject_id
        // if(is_blank($pages['subject_id'])) {
        //     $errors[] = "Subject cannot be blank.";
        // }
        // position
        // Make sure we are working with an integer
        $postion_int = (int) $pages['position'];
        if($postion_int <= 0) {
            $errors[] = "Position must be greater than zero.";
        }
        if($postion_int > 999) {
            $errors[] = "Position must be less than 999.";
        }

        // visible
        // Make sure we are working with a string
        $visible_str = (string) $pages['visible'];
        if(!has_inclusion_of($visible_str, ["0","1"])) {
            $errors[] = "Visible must be true or false.";
        }

        //content
        if(is_blank($pages['content'])) {
            $errors[] = "Content cannot be blank.";
        }

        return $errors;
    }

    function insert_page($pages){
        global $db;

        $errors = validate_page($pages);
        $shift = shift_page_positions(0, $pages['position'],$pages['subject_id']);
        if(!$shift){
            echo mysqli_error($db);
            db_disconnect($db);
        }
        if(!empty($errors)){
            return $errors;
        }

        $sql = "INSERT INTO pages ";
        $sql .= "(menu_name, subject_id, position, visible, content) ";
        $sql .= "VALUES ('";
        $sql .= db_escape($db, $pages['menu_name']) . "', '";
        $sql .= db_escape($db, $pages['subject_id']) . "', '";
        $sql .= db_escape($db, $pages['position']) . "', '";
        $sql .= db_escape($db, $pages['visible']) . "', '";
        $sql .= db_escape($db, $pages['content']) . "');";
        $result = mysqli_query($db,$sql);
        if($result){
            $_SESSION['message'] = "Page added.";
            return true;
        } else {
            echo mysqli_error($db);
            db_disconnect($db);
            exit;
        }
    }

    function update_page($pages){
        global $db;

        $errors = validate_page($pages);
        $old_page = find_pages_by_page($pages['page']);
        $init_pos = $old_page['position'];
        $shift = shift_page_positions($init_pos, $pages['position'], $pages['subject_id'], $pages['page']);
        if(!$shift){
            echo mysqli_error($db);
            db_disconnect($db);
        }
        if(!empty($errors)){
            return $errors;
        }

        $sql = "UPDATE pages SET ";
        $sql .= "menu_name='". db_escape($db, $pages['menu_name'])."',";
        $sql .= "position='". db_escape($db, $pages['position'])."',";
        $sql .= "visible='". db_escape($db, $pages['visible'])."' ,";
        $sql .= "content='". db_escape($db, $pages['content'])."' ";
        $sql .= "WHERE page='". db_escape($db, $pages['page'])."' ";
        $sql .= "LIMIT 1";
        echo $sql;
        $result = mysqli_query($db, $sql);
        if($result){
            $_SESSION['message'] = "Page updated";
            redirect_to(url_for('/staff/pages/show.php?page=' . $pages['page']));
        } else {
            echo mysqli_error($db);
            db_disconnect($db);
        exit;
        }
    }

    function delete_page($page){
        global $db;
        $old_page = find_pages_by_page($page);
        $shift = shift_page_positions($old_page['position'] ,0 , $old_page['subject_id'], $old_page['page']);
        if(!$shift){
            echo mysqli_error($db);
            db_disconnect($db);
        }
        $sql = "DELETE from pages ";
        $sql .= "WHERE page='".$page."' ";
        $sql .= "LIMIT 1";

        $result = mysqli_query($db,$sql);

        if($result){
            $_SESSION['message'] = "Page deleted";
            return true;
        } else {
            echo mysqli_error($db);
            db_disconnect($db);
            exit;
        }
    }

    function shift_page_positions($start_pos, $end_pos, $subject_id, $current_id=0){
        global $db;

        if($start_pos == $end_pos){ return;}
        $sql = "UPDATE pages SET ";
        if($start_pos == 0){ // adding new item
            
            $sql .= "position = position + 1 ";
            $sql .= "WHERE position >= '".db_escape($db,$end_pos);
            
        } elseif($end_pos == 0){ // deleting an item
            
            $sql .= "position = position - 1 ";
            $sql .= "WHERE position > '".db_escape($db,$start_pos);
            
        } elseif($start_pos < $end_pos){ // move down
            $sql .= "position = position - 1 ";
            $sql .= "WHERE position > '".db_escape($db,$start_pos);
            $sql .= "' AND position <= '".db_escape($db,$end_pos);

        } elseif($start_pos > $end_pos){ // move up
            
            $sql .= "position = position + 1 ";
            $sql .= "WHERE position < '".db_escape($db,$start_pos);
            $sql .= "' AND position >= '".db_escape($db,$end_pos);
            
        }
        $sql .= "' AND page!='".db_escape($db,$current_id);
        $sql .= "' AND subject_id ='".db_escape($db,$subject_id)."' ";
        $result = mysqli_query($db, $sql);

        if($result){
            return true;
        } else {
            echo "Page Position updating failed. ".mysqli_error;
            db_disconnect();
            exit;
        }
    }
    //Admin functions

    function find_all_admins() {
        global $db;

        $sql = "SELECT * FROM admins ";
        $sql .= "ORDER BY id ASC";
        $result = mysqli_query($db, $sql);
        confirm_result_set($result);
        return $result;
    }

    function find_admin_by_id($id){
        global $db;

        $sql = "SELECT * FROM admins ";
        $sql .= "WHERE id='" . db_escape($db, $id) . "' ";
        $sql .= "LIMIT 1";
        $result = mysqli_query($db,$sql);
        confirm_result_set($result);

        $admin = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        return $admin;
    }

    function find_admin_by_username($username){
        global $db;

        $sql = "SELECT * FROM admins ";
        $sql .= "WHERE username='" . db_escape($db, $username) . "' ";
        $sql .= "LIMIT 1";
        $result = mysqli_query($db,$sql);
        confirm_result_set($result);

        $admin = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        return $admin;
    }

    function has_unique_username($admins){
        global $db;
        $sql = "SELECT * FROM admins ";
        $sql .= "WHERE username ='";
        $sql .= db_escape($db, $admins['username'])."'";
        $result = mysqli_query($db, $sql);
        $temp = mysqli_fetch_assoc($result);
        if(empty($temp)){
            return true;
        } else {
            return false;
        }
    }

    function has_unique_email($admins){
        global $db;
        $sql = "SELECT * FROM admins ";
        $sql .= "WHERE email ='";
        $sql .= db_escape($db, $admins['email'])."'";
        $result = mysqli_query($db, $sql);
        $temp = mysqli_fetch_assoc($result);
        if(empty($temp)){
            return true;
        } else {
            return false;
        }
    }

    function validate_admin($admin, $options = []) {
        $errors = [];
        $password_required = options['password_required'];
        // username
        if(is_blank($admin['username'])) {
            $errors[] = "Name cannot be blank.";
        } elseif(!has_length($admin['username'], ['min' => 8, 'max' => 255])) {
            $errors[] = "Name must be between 8 and 255 characters.";
        }

        // Full name
        if(is_blank($admin['first_name'])) {
            $errors[] = "First name cannot be blank.";
        } elseif(!has_length($admin['first_name'], ['min' => 2, 'max' => 255])) {
            $errors[] = "First name must be between 2 and 255 characters.";
        }

        if(is_blank($admin['last_name'])) {
            $errors[] = "Last name cannot be blank.";
        } elseif(!has_length($admin['last_name'], ['min' => 2, 'max' => 255])) {
            $errors[] = "Last name must be between 2 and 255 characters.";
        }
        
        //Email
        if(is_blank($admin['email'])) {
            $errors[] = "Email cannot be blank.";
        } elseif(!has_length($admin['email'], ['min' => 2, 'max' => 255])) {
            $errors[] = "Email must be between 2 and 255 characters.";
        }

        if(!has_valid_email_format($admin['email'])) {
            $errors[] = "Invalid email format.";
        }

        //Password
        if($password_required){
            if(is_blank($admin['password'])) {
                $errors[] = "Password cannot be blank.";
            } elseif (!has_length($admin['password'], array('min' => 12))) {
                $errors[] = "Password must contain 12 or more characters";
            } elseif (!preg_match('/[A-Z]/', $admin['password'])) {
                $errors[] = "Password must contain at least 1 uppercase letter";
            } elseif (!preg_match('/[a-z]/', $admin['password'])) {
                $errors[] = "Password must contain at least 1 lowercase letter";
            } elseif (!preg_match('/[0-9]/', $admin['password'])) {
                $errors[] = "Password must contain at least 1 number";
            } elseif (!preg_match('/[^A-Za-z0-9\s]/', $admin['password'])) {
                $errors[] = "Password must contain at least 1 symbol";
            }
            if(is_blank($admin['confirm_password'])) {
                $errors[] = "Confirm password cannot be blank.";
            } elseif ($admin['password'] !== $admin['confirm_password']) {
                $errors[] = "Password and confirm password must match.";
            }
        }
        return $errors;
    }

    function insert_admin($admin){
        global $db;

        $errors = validate_admin($admin);
        // if(!has_unique_email($admin)){
        //     $errors[] = 'Email already registered.';
        // }
        if(!has_unique_username($admin)){
            $errors[] = 'Username already registered.';
        }
        if(!empty($errors)){
            return $errors;
        }

        
        $hashed_password = password_hash($admin['password'],PASSWORD_BCRYPT);

        $sql = "INSERT INTO admins ";
        $sql .= "(username, first_name, last_name, email, hashed_password) ";
        $sql .= "VALUES ('";
        $sql .= db_escape($db, $admin['username']) . "', '";
        $sql .= db_escape($db, $admin['first_name']) . "', '";
        $sql .= db_escape($db, $admin['last_name']) . "', '";
        $sql .= db_escape($db, $admin['email']) . "', '";
        $sql .= db_escape($db, $hashed_password) . "');";
        $result = mysqli_query($db,$sql);
        if($result){
            $_SESSION['message'] = "Admin added.";
            return true;
        } else {
            echo mysqli_error($db);
            db_disconnect($db);
        exit;
        }
    }

    function update_admin($admin){
        global $db;
        
        $password_sent = !is_blank($admin['password']);
        $errors = validate_admin($admin,['password_required'=>$password_sent]);
        
        if(!empty($errors)){
            return $errors;
        }
        $hashed_password = password_hash($admin['password'],PASSWORD_BCRYPT);

        $sql = "UPDATE admins SET ";
        $sql .= "username='". db_escape($db, $admin['username'])."',";
        $sql .= "first_name='". db_escape($db, $admin['first_name'])."',";
        $sql .= "last_name='". db_escape($db, $admin['last_name'])."',";
        if($password_sent){
            $sql .= "hashed_password='". db_escape($db, $hashed_password)."',";
        }
        $sql .= "email='". db_escape($db, $admin['email'])."' ";
        $sql .= "WHERE id='". db_escape($db, $admin['id'])."' ";
        $sql .= "LIMIT 1";
        echo $sql;
        $result = mysqli_query($db, $sql);
        if($result){
            $_SESSION['message'] = "admin updated.";
            redirect_to(url_for('/staff/admins/show.php?id=' . $admin['id']));
        } else {
            echo mysqli_error($db);
            db_disconnect($db);
        exit;
        }
    }

    function delete_admin($id){
        global $db;

        $sql = "DELETE from admins ";
        $sql .= "WHERE id='".db_escape($db, $id)."'";
        $sql .= "LIMIT 1";

        $result = mysqli_query($db,$sql);

        if($result){
            $_SESSION['message'] = "admin deleted";
            return true;
        } else {
            echo mysqli_error($db);
            db_disconnect($db);
            exit;
        }
    }
?>