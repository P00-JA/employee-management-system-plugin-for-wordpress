<?php

if (!defined("ABSPATH")) {
    exit();
}
global $message, $status;
$action = '';
$empId = 0;

//find type request from the query string (view/edit)
if (isset($_GET['action']) && isset($_GET['empId']) && ('edit' === $_GET['action'] || 'view' === $_GET['action']) ) {
    
    global $wpdb;
    
    $action = ('edit' === $_GET['action']) ? 'edit' : 'view';
    $empId = absint($_GET['empId']);

    //single employee information
    $employee = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM {$wpdb->prefix}ems_form_data WHERE id = %d", $empId),
        ARRAY_A
    );    
}

?>
<div class="header" style="margin: 10px 15px 10px 0px; padding: 25px 20px; background-color: #000; border-radius: 10px;">
    <h1 style="text-align: center; color: white; font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;">Employee Management System</h1>
</div>
<br>
<h4 class="first-sub-heading">
    <?php
    if ("view" === $action ) {
        _e("EMPLOYEE DETAILS", "ems-plugin");
    } else if ("edit" === $action ) {
        _e("EDIT EMPLOYEE", "ems-plugin");
    } else {
        _e("ADD EMPLOYEE", "ems-plugin");
    }
    ?>
</h4>
<hr>
<div class="container">
    <div class="panel panel-info">
        <div class="panel-heading"><?php _e("Employee Form", "ems-plugin"); ?></div>
        <div class="panel-body" style="padding: 20px;">
            <?php
            if (!empty($message)) {
                if (1 === $status) {
                    ?>
                    <div class="alert alert-success">
                        <?php echo " " . esc_html($message) . " "; ?>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="alert alert-danger">
                        <?php echo " " . esc_html($message) . " "; ?>
                    </div>
                    <?php
                }
            }
            ?>
            <form action="<?php 
                  echo esc_url('edit'=== $action   ? esc_url(admin_url("admin.php?page=wp-ems-plugin&action=edit&empId=" . $empId)) : esc_url(admin_url("admin.php?page=wp-ems-plugin"))) ; ?>" method="post" id="ems-frm-add-employee">
                <?php wp_nonce_field('ems_form_action', 'ems_nonce'); ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="fname" style="margin-bottom: 5px;"><?php _e("First Name:", "ems-plugin"); ?></label>
                            <input 
                            type="text" 
                            class="form-control" 
                            id="fname" 
                            placeholder="<?php _e("Enter First Name", "ems-plugin"); ?>" 
                            name="firstName" 
                            value="<?php if ( 'view' === $action || 'edit' === $action) { echo esc_attr__($employee['first_name']) ;} ?>" 
                            <?php if ('view' === $action) { echo "readonly='readonly'";} ?>
                            >
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="lname" style="margin-bottom: 5px;"><?php _e("Last Name:", "ems-plugin"); ?></label>
                            <input 
                            type="text" 
                            class="form-control" 
                            id="lname" 
                            placeholder="<?php _e("Enter Last Name", "ems-plugin"); ?>" 
                            name="lastName" 
                            value="<?php if ('view' === $action || 'edit' === $action) { echo esc_attr__($employee['last_name']) ;} ?>" 
                            <?php if ('view' === $action) { echo "readonly='readonly'";} ?>          
                            >
                        </div>
                    </div>
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="email" style="margin-bottom: 5px;"><?php _e("Email :", "ems-plugin"); ?></label>
                    <input 
                    type="email" 
                    class="form-control" 
                    id="email" 
                    placeholder="<?php _e("Enter Email", "ems-plugin"); ?>" 
                    name="email" 
                    value="<?php if ('view' === $action || 'edit' === $action) { echo esc_attr__($employee['email']) ;} ?>"
                    <?php if ('view' === $action) { echo "readonly='readonly'";} ?>                                                                                              
                    >                                                                                                                                                                                                              
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="phone" style="margin-bottom: 5px;"><?php _e("Phone:", "ems-plugin"); ?></label>
                    <input 
                    type="text" 
                    class="form-control" 
                    id="phone" 
                    placeholder="<?php _e("Enter Phone Number", "ems-plugin"); ?>" 
                    name="phone" 
                    value="<?php if ('view' === $action ||'edit' === $action) { echo esc_attr__($employee['phone_number']) ; } ?>"
                    <?php if ('view' === $action) { echo "readonly='readonly'"; } ?>                                                                                                         
                    >                                                                                                                                                                                                                           
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="gender" style="margin-bottom: 5px;"><?php _e("Gender:", "ems-plugin"); ?></label>
                    <select <?php if ('view' === $action) {
                                echo "disabled";
                            } ?> name="gender" id="gender" class="form-control">
                        <option value=""><?php _e("Select gender", "ems-plugin"); ?></option>
                        <option <?php if (('view' === $action ||'edit' === $action) && 'male' === esc_attr__($employee['gender'])) {
                                    echo "Selected";
                                }  ?> value="male"><?php _e("Male", "ems-plugin"); ?></option>
                        <option <?php if (('view'=== $action || 'edit' === $action) && 'female' === esc_attr__($employee['gender'])) {
                                    echo "Selected";
                                }  ?> value="female"><?php _e("Female", "ems-plugin"); ?></option>
                        <option <?php if (('view' === $action ||'edit' === $action) && 'other' === esc_attr__($employee['gender'])) {
                                    echo "Selected";
                                }  ?> value="other"><?php _e("Other", "ems-plugin"); ?></option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="designation" style="margin-bottom: 5px;"><?php _e("Designation:", "ems-plugin"); ?></label>
                    <input 
                    type="text" 
                    class="form-control" 
                    id="designation" 
                    placeholder="<?php _e("Designation", "ems-plugin"); ?>" 
                    name="designation" 
                    value="<?php if ('view' === $action || 'edit' === $action) { echo esc_attr__($employee['designation']);} ?>"
                    <?php if ('view' === $action) { echo "readonly='readonly'"; } ?>                                                                                                                       
                    >                                                                                                                                                                                                                                                 
                </div>
                <?php
                 if ("edit" === $action) {
                ?>
                    <button type="submit" class="btn btn-primary" name="ems_btn_edit"><?php _e("Edit", "ems-plugin"); ?></button>
                <?php
                } else if("" === $action) {
                ?>
                    <button type="submit" class="btn btn-success" name="ems_btn_submit"><?php _e("Submit", "ems-plugin"); ?></button>
                <?php
                }
                ?>
            </form>
        </div>
    </div>
</div>
