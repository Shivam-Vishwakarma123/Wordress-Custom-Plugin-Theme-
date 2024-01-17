<?php

/*
Plugin Name: My Customm Form Plugin
Description: Added a custom form
Version: 1.0
Author: Shivam Vishwakarma
*/



// Activate the plugin
register_activation_hook(__FILE__, 'create_custom_contact_form_table');
function create_custom_contact_form_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'job_applications';

    $sql = "CREATE TABLE $table_name (
        id int(9) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        phone varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        highest_qualification varchar(255) NOT NULL,
        file_path varchar(255) NOT NULL,
        date_of_submission datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    );";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}


// For Short Code
add_shortcode('contact_form', 'custom_form_shortcode');
function custom_form_shortcode()
{
    ob_start();

    echo '<form id="custom-form" method="post" enctype="multipart/form-data">
        <label for="name"> Name:</label>
        <input type="text" name="name" id="name" required><br>

        <label for="phone"> Phone:</label>
        <input type="phone" name="phone" id="phone" required><br>

        <label for="email"> Email:</label>
        <input type="text" name="email" id="email" required><br>

        <label for="highest_qualification"> Highest Qualification:</label>
        <input type="text" name="highest_qualification" id="highest_qualification" required><br>

        <label for="file">File Upload:</label>
        <input type="file" name="file_upload" required><br>

        <input type="submit" name="submit_form" value="Submit">
    </form>';

    return ob_get_clean();
}




// Handle Form to store data in db and send mail
add_action('init', 'handle_form_submission');
function handle_form_submission()
{
    if (isset($_POST['submit_form'])) {
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $highest_qualification = $_POST['highest_qualification'];

        // Upload file
        global $wpdb;
        $upload_dir = wp_upload_dir();
        $file_upload = $_FILES['file_upload'];

        $file_name = $file_upload['name'];
        $file_path = $upload_dir['path'] . '/' . $file_name;

        if (move_uploaded_file($file_upload['tmp_name'], $file_path)) {

            echo 'File uploaded.';
            // Add data into the db
            $wpdb->insert(
                $wpdb->prefix . 'job_applications',
                array(
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'highest_qualification' => $highest_qualification,
                    'file_path' => $file_path,
                )
            );


            // Send email
            $to      = get_option('email_address');
            $subject = 'Tesing mail send';
            $message = 'Email send from : ' . $name . 'This mail is sending just for testing kind of things';
            $headers = array('Content-Type: text/html; charset=UTF-8');
            wp_mail($to, $subject, $message, $headers);

            echo 'Mail send.';
            unset($_POST);
            wp_redirect(get_permalink());
            exit;
        } else {
            echo 'File upload failed.';
        }
    }
}




// Add email settings page 
add_action('admin_menu', 'contact_form_menu');
function contact_form_menu()
{
    add_menu_page(
        'Set Email Address',
        'Set Email Address',
        'manage_options',
        'email_address_setting',
        'email_address_setting_page'
    );
}
function email_address_setting_page()
{
?>
    <div class="class wrapper">

        <h2>Email Adress Setting</h2>
        <form method="post">
            <label for="email_address"> Set Email Address:</label>
            <input type="text" name="email_address" id="email_address" value="<?php if ($_SERVER['REQUEST_METHOD'] != 'POST') {
                                                                                    echo get_option('email_address');
                                                                                } else {
                                                                                    echo $_POST['email_address'];
                                                                                } ?>" required><br>
            <input type="submit" value="Submit">
        </form>

    </div>
<?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email_address = $_POST['email_address'];

        // Add data into options table
        add_option('email_address', $email_address);
        update_option('email_address', $email_address);
    }
}



// Add page to show the listing of the submitted job applications
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

// Extending class
class Job_applications_List_Table extends WP_List_Table
{
    function get_columns()
    {
        $columns = array(
            'cb'            => '<input type="checkbox" />',
            'name'          => __('Name', 'job_applications-cookie-consent'),
            'phone'         => __('Phone', 'job_applications-cookie-consent'),
            'email'   => __('E-Mail', 'job_applications-cookie-consent'),
            'highest_qualification'        => __('HIghest Qualification', 'job_applications-cookie-consent'),
            'date_of_submission'   => __('Date of Submission', 'job_applications-cookie-consent'),
            'file_path'   => __('View || Download', 'job_applications-cookie-consent')
        );
        return $columns;
    }

    // Bind table with columns, data and all
    function prepare_items()
    {
        //data
        if (isset($_POST['s'])) {
            $this->table_data = $this->get_table_data($_POST['s']);
        } else {
            $this->table_data = $this->get_table_data();
        }
        $this->process_bulk_action();

        $columns = $this->get_columns();
        $hidden = (is_array(get_user_meta(get_current_user_id(), 'managetoplevel_page_job_applications_list_tablecolumnshidden', true))) ? get_user_meta(get_current_user_id(), 'managetoplevel_page_job_applications_list_tablecolumnshidden', true) : array();
        $sortable = $this->get_sortable_columns();
        $primary  = 'name';
        $this->_column_headers = array($columns, $hidden, $sortable, $primary);

        usort($this->table_data, array(&$this, 'usort_reorder'));

        /* pagination */
        $per_page = $this->get_items_per_page('elements_per_page', 10);
        $current_page = $this->get_pagenum();
        $total_items = count($this->table_data);

        // Edit
        if (isset($_GET['action']) && $_GET['page'] == "job_applications_list_table" && $_GET['action'] == "edit") {

            // Get the selected items id
            $element_id = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();

            global $wpdb;
            $table_name = $wpdb->prefix . 'job_applications';
            $result = $wpdb->get_results("SELECT * FROM $table_name where id=$element_id");
            $upload_dir = wp_upload_dir();
            $file_name = basename($result[0]->file_path);
            $file_url = esc_url($upload_dir['url'] . '/' . $file_name);

            echo '<form id="custom-form" method="post" enctype="multipart/form-data">
        <label for="name"> Name:</label>
        <input type="text" name="name" id="job_name" value="' . $result[0]->name . '" required><br><br>

        <label for="phone"> Phone:</label>
        <input type="phone" name="phone" id="job_phone" value="' . $result[0]->phone . '" required><br><br>

        <label for="email"> Email:</label>
        <input type="text" name="email" id="job_email" value="' . $result[0]->email . '" required><br><br>

        <label for="highest_qualification"> Highest Qualification:</label>
        <input type="text" name="highest_qualification" id="job_highest_qualification" value="' . $result[0]->highest_qualification . '" required><br><br>

        <label>Existing File:</label>
        <a href="' . $file_url . '" target="_blank">View Existing File</a><br><br>

        <label for="file">New File Upload:</label>
        <input type="file" name="file_upload"><br><br>
        
        <input type="submit" name="submit_admin_form" value="Update">
    </form>';



            if (isset($_POST['submit_admin_form'])) {

                $name = $_POST['name'];
                $phone = $_POST['phone'];
                $email = $_POST['email'];
                $highest_qualification = $_POST['highest_qualification'];

                $file_upload = $_FILES['file_upload'];
                $file_name = $file_upload['name'];
                $file_path = $upload_dir['path'] . '/' . $file_name;

                if (move_uploaded_file($file_upload['tmp_name'], $file_path)) {

                    echo 'File uploaded.';
                    // Add data into the db
                    $wpdb->update(
                        $table_name,
                        array(
                            'name' => $name,
                            'phone' => $phone,
                            'email' => $email,
                            'highest_qualification' => $highest_qualification,
                            'file_path' => $file_path
                        ),
                        array('id' => $element_id),
                        array('%s'),
                        array('%d')
                    );
                }

                

                $message =  '<div id="message" class="notice is-dismissible updated"><p>Deleted Successfully.</p></div>';
                echo $message;
                echo '<script>window.location.href = "' . admin_url('admin.php?page=job_applications_list_table') . '";</script>';
                // exit;
            }
            exit;
        }


        // Delete
        if (isset($_GET['action']) && $_GET['page'] == "job_applications_list_table" && $_GET['action'] == "delete") {
            global $wpdb;
            // Get the selected items
            $element_id = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            $wpdb->delete(
                $wpdb->prefix . 'job_applications',
                array('id' => $element_id),
                array('%d')
            );
            $message =  '<div id="message" class="notice is-dismissible updated"><p>Deleted Successfully.</p></div>';
            echo $message;
            echo '<script>window.location.href = "' . admin_url('admin.php?page=job_applications_list_table') . '";</script>';
            // exit;
        }




        $this->table_data = array_slice($this->table_data, (($current_page - 1) * $per_page), $per_page);

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ));

        $this->items = $this->table_data;
    }

    // Get table data
    private function get_table_data($search = '')
    {
        global $wpdb;

        $table = $wpdb->prefix . 'job_applications';

        if (!empty($search)) {
            return $wpdb->get_results(
                "SELECT * from {$table} WHERE name Like '%{$search}%' OR phone Like '%{$search}%' OR email Like '%{$search}%'",
                ARRAY_A
            );
        } else {
            return $wpdb->get_results(
                "SELECT * from {$table}",
                ARRAY_A
            );
        }
    }


    // define $table_data property
    private $table_data;

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'id':
            case 'name':
            case 'phone':
            case 'email':
            case 'highest_qualification':
            case 'date_of_submission':
            case 'file_path':
            default:
                return $item[$column_name];
        }
    }

    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="element[]" value="%s" />',
            $item['id']
        );
    }

    function column_file_path($item)
    {
        $upload_dir = wp_upload_dir();
        $file_name = basename($item['file_path']);
        $file_url = esc_url($upload_dir['url'] . '/' . $file_name);
        return sprintf(
            '<a href="%s" target="_blank">View</a> || <a href="%s" download>Download</a>',
            $file_url,
            $file_url
        );
    }

    protected function get_sortable_columns()
    {
        $sortable_columns = array(
            'name'  => array('name', false),
            'phone'  => array('phone', false),
            'date_of_submission' => array('date_of_submission', true)
        );
        return $sortable_columns;
    }

    // Sorting function
    function usort_reorder($a, $b)
    {
        // If no sort, default to user_login
        $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'name';

        // If no order, default to asc
        $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';

        // Determine sort order
        $result = strcmp($a[$orderby], $b[$orderby]);

        // Send final sort direction to usort
        return ($order === 'asc') ? $result : -$result;
    }

    // Adding action links to row
    function column_name($item)
    {
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&id=%s">Edit</a>', $_REQUEST['page'], 'edit', $item['id']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&id=%s">Delete</a>', $_REQUEST['page'], 'delete', $item['id']),
        );

        return sprintf('%1$s %2$s', $item['name'], $this->row_actions($actions));
    }

    // To show bulk action delete and draft
    function get_bulk_actions()
    {
        $actions = array(
            'delete_all'    => __('Delete', 'job_applications-admin-table')
        );
        return $actions;
    }

    function process_bulk_action()
    {
        global $wpdb;
        if ('delete_all' === $this->current_action()) {
            $selected_elements = isset($_REQUEST['element']) ? $_REQUEST['element'] : array();
            foreach ($selected_elements as $element_id) {
                $wpdb->delete(
                    $wpdb->prefix . 'job_applications',
                    array('id' => $element_id),
                    array('%d')
                );
            }
            echo '<script>window.location.href = "' . admin_url('admin.php?page=job_applications_list_table') . '";</script>';
            // exit;
        }
    }
}



// Adding menu
function my_add_menu_items()
{

    global $job_applications_sample_page;

    // add settings page
    $job_applications_sample_page = add_menu_page(__('My Job Applications Listing', 'job_applications-admin-table'), __('My Job Applications Listing', 'job_applications-admin-table'), 'manage_options', 'job_applications_list_table', 'job_applications_list_init');

    add_action("load-$job_applications_sample_page", "job_applications_sample_screen_options");
}
add_action('admin_menu', 'my_add_menu_items');

// add screen options
function job_applications_sample_screen_options()
{
    global $job_applications_sample_page;
    global $table;

    $screen = get_current_screen();

    // get out of here if we are not on our settings page
    if (!is_object($screen) || $screen->id != $job_applications_sample_page)
        return;

    $args = array(
        'label' => __('Elements per page', 'job_applications-admin-table'),
        'default' => 2,
        'option' => 'elements_per_page'
    );
    add_screen_option('per_page', $args);

    $table = new Job_applications_List_Table();
}

add_filter('set-screen-option', 'test_table_set_option', 10, 3);
function test_table_set_option($status, $option, $value)
{
    return $value;
}


// Plugin menu callback function
function job_applications_list_init()
{
    // Creating an instance
    $table = new Job_applications_List_Table();

    echo '<div class="wrap"><h2>My Job Applications Listing</h2>';
    echo '<form method="post">';

    $table->prepare_items();

    $table->search_box('search', 'search_id');

    $table->display();
    echo '</div></form>';
}
