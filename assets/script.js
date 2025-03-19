jQuery(document).ready(function () {
    console.log("welcome to crud plugin of employees");

    // Initialize form validation
    jQuery("#form_add_employee").validate({
        rules: {
            fullname: {
                required: true,
                minlength: 2
            },
            email: {
                required: true,
                email: true
            },
            designation: {
                required: true
            }
        },
        messages: {
            fullname: "Please enter a valid full name",
            email: "Please enter a valid email address",
            designation: "Please select a designation"
        },
        submitHandler: function (form) {
            var formdata = new FormData(form);

            jQuery.ajax({
                url: wce_object.ajax_url,
                data: formdata,
                method: "POST",
                dataType: "json",
                contentType: false,
                processData: false,
                success: function (response) {
                    console.log(response); // Debug response
                    if (response.status == 1) {
                        alert(response.message);
                        loadEmployeesData();
                        jQuery("#fullname,#email,#designation,#file").val("");
                    } else {
                        alert(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.log("AJAX Error: ", xhr.responseText); // Log error details
                }
            });
        }
    });

    // Render Employees Data
    loadEmployeesData();
    // Delete employee
    jQuery(document).on("click", ".btn-delete", function() {
        var employeeId = jQuery(this).data("id");
        if(confirm("Are you sure want to delete this employee?")) {
            jQuery.ajax({
                url: wce_object.ajax_url,
                data: {
                    action: "wce_delete_employee",
                    empId: employeeId
                },
                method: "GET",
                dataType: "json",
                success: function(response) {
                    if(response) {
                        alert(response.message);
                        loadEmployeesData();
                    }
                }
            });
        }
    });
});

// Load all employees from employee table

function loadEmployeesData() {
    jQuery.ajax({
        url: wce_object.ajax_url,
        data: {
            action: "wce_load_employees_data"
        },
        method: "GET",
        dataType: "json",
        success: function (response) {
            console.log(response);
            var employeesDataHTML = "";
            jQuery.each(response.employees, function (index, employee) {
                var emp_profile_image = "--";
                if(employee.profile_image) {
                    emp_profile_image = `<img src="${employee.profile_image}" height="80px" weight="80px"/>`;
                }
                employeesDataHTML += `
                    <tr>
                        <td>${employee.id}</td>
                        <td>${employee.fullname}</td>
                        <td>${employee.email}</td>
                        <td>${employee.designation}</td>
                        <td>${emp_profile_image}</td>
                        <td>
                            <button data-id="${employee.id}" class="btn btn-edit">Edit</button>
                            <button data-id="${employee.id}" class="btn btn-delete">Delete</button>
                        </td>
                    </tr>
                `;
            });

            // bind data with table
            jQuery("#tbody_employees_data").html(employeesDataHTML);
        }
    });
}