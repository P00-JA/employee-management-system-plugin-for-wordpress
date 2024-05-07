<?php
/*
 * Plugin Name: Employee Management System 
 * Description: This is a Employee management CRUD operation plugin
 * Plugin URI: https://example.com/employee-management-system
 * Author: Pooja M P
 * Author URI:https://example.com 
 * Version: 1.0
 * Requires at least: 6.5.2
 * Requires PHP: 7.4
 * Text Domain: ems-plugin
 * Domain Path: /languages
 */

if (!defined("ABSPATH")) {
    exit();
}
define("EMS_PLUGIN_PATH", plugin_dir_path(__FILE__));
define("EMS_PLUGIN_URL", plugin_dir_url(__FILE__));

//form validation
add_action("admin_init", "ems_validate_form_submission");

//translation loader
add_action( 'init', 'ems_load_textdomain' );

/**
 * Load translation files for the plugin.
 */
function ems_load_textdomain() {
    load_plugin_textdomain( 'ems-plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}

//calling action hook to add menu
add_action("admin_menu", "ems_add_admin_menu");

/**
 * Add menu and submenus to the WordPress admin menu.
 */
function ems_add_admin_menu()
{
    add_menu_page(
        esc_html__("Employee Management System|Employee Management System Menu", "ems-plugin"),
        esc_html__("Employee Management System", "ems-plugin"),
        "manage_options",
        "wp-ems-plugin",
        "ems_crud_system",
        "dashicons-buddicons-buddypress-logo",
        23
    );

    //sub menus
    add_submenu_page(
        "wp-ems-plugin",
        esc_html__("Add Employee", "ems-plugin"),
        esc_html__("Add Employee", "ems-plugin"),
        "manage_options",
        "wp-ems-plugin",
        "ems_crud_system"
    );
    add_submenu_page(
        "wp-ems-plugin",
        esc_html__("List Employee", "ems-plugin"),
        esc_html__("List Employee", "ems-plugin"),
        "manage_options",
        "list-employee",
        "ems_list_employee"
    );
}

/**
 * Callback function to display the employee management system page.
 */
function ems_crud_system()
{
    include_once(EMS_PLUGIN_PATH . "pages/add-employee.php");
}

/**
 * Callback function to display the list of employees.
 */
function ems_list_employee()
{
    include_once(EMS_PLUGIN_PATH . "pages/list-employee.php");
}

//register activation hook
register_activation_hook(__FILE__, "ems_create_table");


/**
 * Create the database table for storing employee data.
 */
function ems_create_table()
{
    global $wpdb;
    $table_prefix = $wpdb->prefix; // wp_

    $sql = "
   CREATE TABLE IF NOT EXISTS {$table_prefix}ems_form_data (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `first_name` varchar(120) NOT NULL,
    `last_name` varchar(120) NOT NULL,
    `email` varchar(80) NOT NULL,
    `phone_number` varchar(50) NOT NULL,
    `gender` enum('male','female','other') NOT NULL,
    `designation` varchar(50) NOT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
   ";
    include_once ABSPATH . "wp-admin/includes/upgrade.php";
    dbDelta($sql);

    //create post
    $page_data = [
        'post_title'    => esc_html__("EMS-List", "ems-plugin"),
        'post_status'   => 'publish',
        'post_type' => 'page',
        'post_content' => '[ems_list_employee]',
        'post_name' => 'employee-management-system-list-page'
    ];
    $id = wp_insert_post($page_data);
}

//register deactivation hook
register_deactivation_hook(__FILE__, "ems_drop_table");

/**
 * Drop the database table and associated WordPress page upon plugin deactivation.
 */
function ems_drop_table()
{
    global $wpdb;
    $table_prefix = $wpdb->prefix;
    $sql = "DROP TABLE {$table_prefix}ems_form_data; ";
    $wpdb->query($sql);

    //drop wordpress page
    $page_slug = "employee-management-system-list-page";
    $page_info = get_page_by_path($page_slug);

    if (!empty($page_info)) {
        $page_id = $page_info->ID;
        wp_delete_post($page_id, true);
    }
}

// Add CSS/JS to plugin admin pages
add_action("admin_enqueue_scripts", "ems_add_plugin_assets");

/**
 * Enqueue CSS and JavaScript files for the plugin's admin pages.
 */
function ems_add_plugin_assets()
{
    // Get the current admin page URL
    $current_page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';

    // Check if the current page is one of the plugin pages
    if ('wp-ems-plugin' === $current_page || 'list-employee' === $current_page) {
        // Enqueue plugin CSS and JS files 
        wp_enqueue_style("ems-bootstrap-css", EMS_PLUGIN_URL . "css/bootstrap.min.css", array(), "1.0.0", "all");
        wp_enqueue_style("ems-bootstrap3-css", EMS_PLUGIN_URL . "css/bootstrap3.min.css", array(), "1.0.0", "all");
        wp_enqueue_style("ems-datatable-css", EMS_PLUGIN_URL . "css/dataTables.dataTables.min.css", array(), "1.0.0", "all");
        wp_enqueue_style("ems-custom-css", EMS_PLUGIN_URL . "css/custom.css", array(), "1.0.0", "all");
        
        wp_enqueue_script("ems-bootstrap-js", EMS_PLUGIN_URL . "js/bootstrap.min.js", array("jquery"), "1.0.0");
        wp_enqueue_script("ems-bootstrap3-js", EMS_PLUGIN_URL . "js/bootstrap3.min.js", array("jquery"), "1.0.0");
        wp_enqueue_script("ems-datatable-js", EMS_PLUGIN_URL . "js/dataTables.min.js", array("jquery"), "1.0.0");
        wp_enqueue_script("ems-validate-js", EMS_PLUGIN_URL . "js/jquery.validate.min.js", array("jquery"), "1.0.0");
        wp_enqueue_script("ems-custom-js", EMS_PLUGIN_URL . "js/custom.js", array("jquery"), "1.0.0");
    }
}

//enqueue CSS and JS to frontened
add_action('wp_enqueue_scripts', 'ems_enqueue_frontend_assets');

/**
 * Enqueue CSS and JavaScript files for the plugin's frontend pages.
 */
function ems_enqueue_frontend_assets() {
    
    if (is_page('employee-management-system-list-page')){
        //css
        wp_enqueue_style("ems-bootstrap-css", EMS_PLUGIN_URL . "css/bootstrap.min.css", array(), "1.0.0", "all");
        wp_enqueue_style("ems-bootstrap3-css", EMS_PLUGIN_URL . "css/bootstrap3.min.css", array(), "1.0.0", "all");
        wp_enqueue_style("ems-datatable-css", EMS_PLUGIN_URL . "css/dataTables.dataTables.min.css", array(), "1.0.0", "all");
        wp_enqueue_style("ems-custom-css", EMS_PLUGIN_URL . "css/custom.css", array(), "1.0.0", "all");

        //js
        wp_enqueue_script("ems-bootstrap-js", EMS_PLUGIN_URL . "js/bootstrap.min.js", array("jquery"), "1.0.0");
        wp_enqueue_script("ems-bootstrap3-js", EMS_PLUGIN_URL . "js/bootstrap3.min.js", array("jquery"), "1.0.0");
        wp_enqueue_script("ems-datatable-js", EMS_PLUGIN_URL . "js/dataTables.min.js", array("jquery"), "1.0.0");
        wp_enqueue_script("ems-validate-js", EMS_PLUGIN_URL . "js/jquery.validate.min.js", array("jquery"), "1.0.0");
        wp_enqueue_script("ems-custom-js", EMS_PLUGIN_URL . "js/custom.js", array("jquery"), "1.0.0");
    }

}

/**
 * Shortcode to render the list of employees.
 *
 * @return string Rendered HTML content of the employee list.
 */
function ems_render_list_employee()
{
    ob_start();
    include(EMS_PLUGIN_PATH . "pages/list-employee.php");
    return ob_get_clean();
}
add_shortcode('ems_list_employee', 'ems_render_list_employee');

/**
 * Validate form submission and process the form data.
 */
function ems_validate_form_submission()
{
    global $message, $status;
    $message = '';
    $status = '';

    if ( 'POST' === $_SERVER['REQUEST_METHOD'] && (isset($_POST['ems_btn_submit']) || isset($_POST['ems_btn_edit']))) {
        // Verify nonce
        if (!isset($_POST['ems_nonce']) || !wp_verify_nonce($_POST['ems_nonce'], 'ems_form_action')) {
            // Nonce verification failed, handle error or redirect
            _e("Nonce verification failed!", "ems-plugin");
            exit;
        }

        //Define validation rules

        $validation_errors = array();
        $required_fields = array("firstName", "lastName", "email", "phone", "gender", "designation");
        $min_lengths = array("firstName" => 2, "lastName" => 2, "phone" => 10);
        $max_lengths = array("firstName" => 30, "lastName" => 30, "phone" => 12);

        //validate required fields
        foreach ($required_fields as $field) {
            if (isset($_POST[$field]) && empty($_POST[$field])) {
                $validation_errors[] = sprintf(__("%s is required", "ems-plugin"), ucfirst($field));
            }
        }

        //check the length

        foreach ($min_lengths as $field => $min_length) {
            if (strlen($_POST[$field]) < $min_length) {
                $validation_errors[] = sprintf(__("%s must be at least %d characters", "ems-plugin"), ucfirst($field), $min_length);
            }
        }

        foreach ($max_lengths as $field => $max_length) {
            if (strlen($_POST[$field]) > $max_length) {
                $validation_errors[] = sprintf(__("%s should not be more than %d characters", "ems-plugin"), ucfirst($field), $max_length);
            }
        }

        //email validation
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $validation_errors[] = __("Invalid Email", "ems-plugin");
        }

        //check if mail address already exists

        global $wpdb;
        $existing_email = $wpdb->get_var(
            $wpdb->prepare('SELECT COUNT(*) FROM {$wpdb->prefix}ems_form_data WHERE email= %s', $_POST['email'])
        );
        if ($existing_email > 0) {
            $validation_errors[] = __("Email already exists", "ems-plugin");
        }

        //display if any errors

        if (!empty($validation_errors)) {
            foreach ($validation_errors as $error) {
                ?>
                <div class="alert alert-danger"><?php echo " " . esc_html($error) . " " ?></div>
               <?php
            }
        } else {
            //if no error form can be submitted
            ems_process_form_submission();
        }
    }
}

/**
 * Process form submission data and perform CRUD operations on employee data.
 */
function ems_process_form_submission()
{
    global $wpdb, $message, $status;

    // Check if the current user is an administrator
    if (!current_user_can('administrator')) {
        $message = esc_html__("You do not have permission to perform this action.", "ems-plugin");
        return;
    }

    $firstName = sanitize_text_field($_POST['firstName']);
    $lastName = sanitize_text_field($_POST['lastName']);
    $email = sanitize_email($_POST['email']);
    $phone = sanitize_text_field($_POST['phone']);
    $gender = sanitize_text_field($_POST['gender']);
    $designation = sanitize_text_field($_POST['designation']);

    // Action Type
    if (isset($_GET['action']) && 'edit' === $_GET['action']) {
        $empId = absint($_GET['empId']);

        // Edit operation
        $wpdb->update(
            "{$wpdb->prefix}ems_form_data",
            array(
                "first_name" => $firstName,
                "last_name" => $lastName,
                "email" => $email,
                "phone_number" => $phone,
                "gender" => $gender,
                "designation" => $designation,
            ),
            array("id" => $empId),
            array(
                '%s', // for first_name (string)
                '%s', // for last_name (string)
                '%s', // for email (string)
                '%s', // for phone_number (string)
                '%s', // for gender (string)
                '%s'  // for designation (string)
            ),
            array('%d') // format for the WHERE clause value (empId)
        );

        $message = __("Employee Details Updated Successfully", "ems-plugin");
        $status = 1;

        ?>
        <script>
            setTimeout(() => {
                window.location.href = '<?php echo esc_url(admin_url('admin.php?page=list-employee')); ?>';
            }, 2000);
        </script>
        <?php

    } else {
        // Insert data

        $wpdb->insert(
            "{$wpdb->prefix}ems_form_data",
            array(
                "first_name" => $firstName,
                "last_name" => $lastName,
                "email" => $email,
                "phone_number" => $phone,
                "gender" => $gender,
                "designation" => $designation
            ),
            array(
                '%s', // for first_name (string)
                '%s', // for last_name (string)
                '%s', // for email (string)
                '%s', // for phone_number (string)
                '%s', // for gender (string)
                '%s'  // for designation (string)
            )
        );

        $last_inserted_id = $wpdb->insert_id;

        if ($last_inserted_id > 0) {
            $message = sprintf(__("Employee with ID %d successfully inserted", "ems-plugin"), $last_inserted_id);
            $status = 1;
        } else {
            $message = __("Failed to save the record", "ems-plugin");
            $status = 0;
        }
    }
}
