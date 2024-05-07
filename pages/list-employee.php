<?php
if (!defined("ABSPATH")) {
    exit();
}

global $wpdb;


// Handle delete request
if (isset($_GET['action']) && 'delete' === $_GET['action'] && isset($_GET['empId'])) {
    // Check if the current user is an administrator
    if (!current_user_can('administrator')) {
        echo esc_html__("You do not have permission to access this action.", "ems-plugin");
        exit;
    }
    if (!isset($_GET['ems_nonce']) || !wp_verify_nonce( sanitize_text_field($_GET['ems_nonce']) , 'ems_delete_action')) {
        // Nonce verification failed, handle error or redirect
        echo esc_html__("Nonce verification failed!", "ems-plugin");
        exit;
    }

    $empId = intval($_GET["empId"]);

    // Delete employee
    $wpdb->delete("{$wpdb->prefix}ems_form_data", array(
        "id" => $empId
    ));

    $message = __("Employee Deleted Successfully", "ems-plugin");
}

$employees = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ems_form_data", ARRAY_A);
?>
<div style="margin: 10px 15px 10px 0px; padding: 25px 20px; background-color: #012e42; border-radius: 10px;">
    <h1 style="text-align: center; color: white; font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;"><?php _e("Employees List", "ems-plugin"); ?></h1>
</div>

<hr>
<div class="container">
    <div class="panel panel-primary">
        <div class="panel-heading"><?php _e("Employee-List", "ems-plugin"); ?></div>
        <div class="panel-body">
            <?php
            if (!empty($message)) {
            ?>
                <div class="alert alert-success">
                    <?php echo esc_html($message); ?>
                </div>
            <?php
            }
            ?>
            <div class="table-responsive">
                <table class="table" id="tbl-employee">
                    <thead>
                        <tr>
                            <th>#<?php _e("ID", "ems-plugin"); ?></th>
                            <th>#<?php _e("First Name", "ems-plugin"); ?></th>
                            <th>#<?php _e("Last Name", "ems-plugin"); ?></th>
                            <th>#<?php _e("Email", "ems-plugin"); ?></th>
                            <th>#<?php _e("Phone", "ems-plugin"); ?></th>
                            <th>#<?php _e("Gender", "ems-plugin"); ?></th>
                            <th>#<?php _e("Designation", "ems-plugin"); ?></th>
                            <?php 
                            if (!is_page('employee-management-system-list-page')){
                               ?>
                                    <th>#<?php _e("Action", "ems-plugin"); ?></th>
                               <?php
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (count($employees) > 0) {
                            foreach ($employees as $employee) {
                        ?>
                                <tr>
                                    <td><?php echo esc_html__($employee['id']); ?></td>
                                    <td><?php echo esc_html__($employee['first_name']); ?></td>
                                    <td><?php echo esc_html__($employee['last_name']); ?></td>
                                    <td><?php echo esc_html__($employee['email']); ?></td>
                                    <td><?php echo esc_html__($employee['phone_number']); ?></td>
                                    <td><?php echo esc_html__(ucfirst($employee['gender'])); ?></td>
                                    <td><?php echo esc_html__($employee['designation']); ?></td>
                                    <?php 
                                    if (!is_page('employee-management-system-list-page')){
                                        ?>
                                            <td style="display: flex; flex-direction: row; gap: 5px;">
                                                <a href="admin.php?page=wp-ems-plugin&action=edit&empId=<?php echo esc_attr($employee['id']); ?>" style="margin-right: 5px;" class="btn btn-warning" role="button"><?php _e("Edit", "ems-plugin"); ?></a>
                                                <a 
                                                href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=list-employee&action=delete&empId=' . $employee['id']), 'ems_delete_action', 'ems_nonce')); ?>" 
                                                style="margin-right: 5px;" class="btn btn-danger" role="button" onclick="if(!confirm('<?php _e("Are you sure you want to delete?", "ems-plugin"); ?>')){ return false; }">
                                                <?php _e("Delete", "ems-plugin"); ?>
                                                </a>
                                                <a href="admin.php?page=wp-ems-plugin&action=view&empId=<?php echo esc_attr__($employee['id']); ?>" class="btn btn-info" role="button"><?php _e("View", "ems-plugin"); ?></a>
                                            </td>
                                        <?php
                                        }
                                    ?>

                                </tr>
                        <?php
                            }
                        } else {
                            esc_html_e("No Employee Data", "ems-plugin");
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
