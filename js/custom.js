jQuery(function($) {
    console.log("custom.js loaded");
    //list employee data table
   new DataTable('#tbl-employee');

   console.log($('#ems-frm-add-employee'));
   //Employee form validation
   $('#ems-frm-add-employee').validate({
    rules: {
        firstName: {
            required: true,// First name is required
            minlength: 2, // Minimum length of 2 characters
            maxlength: 30 // Maximum length of 50 characters
        },
        lastName: {
            required: true, // Last name is required
            minlength: 2, // Minimum length of 2 characters
            maxlength: 30 // Maximum length of 50 characters
        },
        email: {
            required: true, // Email is required
            email: true // Must be a valid email address format
        },
        phone: {
            required: true, // Phone number is required
            digits: true, // Must contain only digits
            minlength: 10, // Minimum length of 10 digits
            maxlength: 12 // Maximum length of 15 digits
        },
        gender: {
            required: true // Gender is required
        },
        designation: {
            required: true, // Designation is required
            minlength: 2, // Minimum length of 2 characters
            maxlength: 30 // Maximum length of 50 characters
        }
    },
});

});
