<?php
/* 
 * Plugin Name: Projects Plugin
 * Description: Projects Plugin to upload and show projects
 * Version: 1.0
 * Author: Suprodip Sorkar
 */


// step 1: create db table
register_activation_hook(__FILE__, 'table_creator');

function table_creator()
{
    global $wpdb;
    $charset_collate = $wpdb -> get_charset_collate();
    $table_name = $wpdb -> prefix.'projects';

    // title, description, catagory, image(thumbnail), url(external)
    $sql = "DROP TABLE IF EXISTS $table_name;
            CREATE TABLE $table_name(
            id mediumint(11) NOT NULL AUTO_INCREMENT,
            title varchar(50) NOT NULL,
            description varchar(250) NOT NULL,
            catagory varchar(50) NOT NULL,
            image varchar(500) NOT NULL,
            url varchar(500) NOT NULL,
            PRIMARY KEY id(id)
            )$charset_collate;
            ";
    require_once(ABSPATH.'wp-admin/includes/upgrade.php');

    dbDelta($sql);
}







// step 2: insert data into the table
add_action('admin_menu', 'displayAdminMenu');

function displayAdminMenu()
{
    // Dashboard, Posts, Pages, Commnets, Appearance, Tools er moto Projects name er menu create hobe
    add_menu_page('Projects', 'Projects', 'manage_options', 'project-list', 'project_list_callback');
    // create submenu
    add_submenu_page('project-list', 'Project List', 'Project List', 'manage_options', 'project-list', 'project_list_callback');
    add_submenu_page('project-list', 'Add Project', 'Add Project', 'manage_options', 'add-project', 'project_add_callback');
    add_submenu_page(null, 'View Project', 'View Project', 'manage_options', 'view-project', 'project_view_callback');
}
function project_add_callback()
{
    echo "<h2> Add Project </h2>";

    global $wpdb;
    $table_name = $wpdb -> prefix.'projects';
    $msg = '';

    if(isset($_REQUEST['submit'])){
        echo "<pre>";
        echo "Table name is : " . $table_name;
        echo "</pre>";


        $wpdb -> insert("$table_name", [
            "title" => $_REQUEST['project_title'],
            "description" => $_REQUEST['project_desc'],
            "catagory" => $_REQUEST['project_category'],
            "image" => $_REQUEST['project_img'],
            "url" => $_REQUEST['project_link']
        ]);
        // echo "hello";
        if($wpdb -> insert_id > 0){
            $msg = "Saved Successfully";
        }
        else{
            $msg = "Failed to save data";
        }
        
    }
    echo "<h4>".$msg."</h4>";
    echo $wpdb->last_error;
    echo $wpdb->show_errors(); // Unknown column 'category' in 'field list'
    // echo $wpdb->last_query();

    echo " 
    
    <form method = 'post' action = ''>
            <p>
                <label> Project Title </label>
                <input type = 'text' name = 'project_title' placeholder = 'Enter Project title' required>
            </p>
            <p>
                <label> Project Name </label>
                <input type = 'text' name = 'project_desc' placeholder = 'Enter Description' required>    
            </p>
            <p>
                <label> Project Category </label>
                <input type = 'text' name = 'project_category' placeholder = 'Enter Category' required>    
            </p>
            <p>
                <label> Project Image </label>
                <input type = 'text' name = 'project_img' placeholder = 'Upload Image' required>    
            </p>
            <p>
                <label> Project External link </label>
                <input type = 'text' name = 'project_link' placeholder = 'Enter External link' required>    
            </p>
            <p>
                <button type = 'submit' name = 'submit'> Submit </button>
            </p>
        </from>
        ";
}
function project_list_callback()
{
    echo "<h2> Project list </h2>";

    global $wpdb;
    $table_name = $wpdb -> prefix.'projects';

    $project_list = $wpdb -> get_results($wpdb -> prepare("select * from $table_name", ""), ARRAY_A);

                $i = 1;
                foreach($project_list as $index => $project):
                    echo "
                    <div>
                        <a href = 'admin.php?page=view-project&id=".$project['id']." '>
                            <img src = ".$project['image']." width='200' height='200'>
                            <div>
                                <h3>".$project['title']."</h3>
                                <button> <a href = ''> View </a> </button>
                            </div>
                        </a>
                        <br><br>
                    </div>
                        ";
                    $i++;
                endforeach;

                echo "
            </table>
        </div>
    ";
    
}

function project_view_callback()
{
    echo "<h2> View </h2>";

    global $wpdb;
    $table_name = $wpdb -> prefix . 'projects';
    $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : "";

    $project_details = $wpdb -> get_row($wpdb -> prepare("select * from $table_name where id = %d", $id), ARRAY_A);

    echo "
        <div>
           
                <img src = ".$project_details['image']." width='200' height='200'>
                <div>
                    <h3> Title: ".$project_details['title']."</h3>
                    <h3> Short Description: ".$project_details['description']."</h3>
                    <h3> Category: ".$project_details['catagory']."</h3> <br><br>
                    <h3> External Link: </h3>
                    <a href = ' ".$project_details['url']." '>".$project_details['url']."</a>
                </div>
    
            <br><br>
        </div>
            ";
}

?>

