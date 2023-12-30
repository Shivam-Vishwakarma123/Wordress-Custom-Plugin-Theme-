<?php
/*
Plugin Name: Custom Contact Form Plugin
Description: A custom contact form for your website.
Version: 1.0
Author: Your Name
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
        email varchar(255) NOT NULL,
        file_path varchar(255) NOT NULL,
        submission_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    );";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}




// Short code
add_shortcode('custom_contact_form', 'custom_contact_form');
function custom_contact_form()
{
    ob_start();
?>
    <form id="custom-contact-form" method="post" action="" enctype="multipart/form-data">

        <label for="name">Name:</label>
        <input type="text" name="name" required><br>

        <label for="email">Email:</label>
        <input type="email" name="email" required><br>

        <label for="file">File Upload:</label>
        <input type="file" name="file_upload" required><br>

        <input type="submit" name="submit" value="Submit">
    </form>
<?php
    return ob_get_clean();
}





// Handle db and file upload
add_action('init', 'handle_contact_form_submission');
function handle_contact_form_submission()
{
    global $wpdb;

    if (isset($_POST['submit'])) {
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);


        // Handle file upload
        $upload_dir = wp_upload_dir();
        $file_upload = $_FILES['file_upload'];

        $file_name = sanitize_file_name($file_upload['name']);
        $file_path = $upload_dir['path'] . '/' . $file_name;
        echo $file_path;

        if (move_uploaded_file($file_upload['tmp_name'], $file_path)) {

            // Insert data into the database
            $wpdb->insert(
                $wpdb->prefix . 'job_applications',
                array(
                    'name' => $name,
                    'email' => $email,
                    'file_path' => $file_path,
                    'submission_date' => current_time('mysql'),
                )
            );
        } else {
            // File upload failed
            echo 'File upload failed.';
        }
    }
}




// Create backend page
add_action('admin_menu', 'custom_contact_form_menu_page');
function custom_contact_form_menu_page()
{
    add_menu_page(
        'Form Submissions',
        'Form Submissions',
        'manage_options',
        'custom_contact_form_submissions',
        'display_custom_contact_form_submissions',
        'dashicons-list-view'
    );
}




// Display data from job submission table
function display_custom_contact_form_submissions()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'job_applications';

    $submissions = $wpdb->get_results("SELECT * FROM $table_name");
    // print_r($submissions);
?>
    <div class="wrap">
        <h1>Contact Form Submissions</h1>

        <?php
        if (empty($submissions)) {
            echo '<p>No submissions found.</p>';
        } else {
        ?>

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Link</th>
                        <th>Submission Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $upload_dir = wp_upload_dir();
                    foreach ($submissions as $submission) {
                        $file_name = basename($submission->file_path);
                        $file_url = esc_url($upload_dir['baseurl'] . '/' . date('Y/m') . '/' . $file_name);
                    ?>
                        <tr>
                            <td><?php echo $submission->id; ?></td>
                            <td><?php echo esc_html($submission->name); ?></td>
                            <td><?php echo esc_html($submission->email); ?></td>
                            <td><?php echo '<a href="' . $file_url . '" target="_blank">View</a>';
                                echo ' | <a href="' . $file_url . '" download>Download</a>'; ?></td>
                            <td><?php echo esc_html($submission->submission_date); ?></td>
                        </tr>
                    <?php
                    }
                    ?>

                </tbody>
            </table>
        <?php
        }
        ?>

    </div>
<?php
}
?>