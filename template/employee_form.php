<div id="wp_employee_crud_plugin">
    <!-- Add Employee Card -->
    <div class="card add-employee-form hide-element">
        <button id="close-add-emp-form">Close form</button>
        <h3 class="section-title">Add Employee</h3>
        <form action="javascript:void(0);" id="form_add_employee" class="employee-form" enctype="multipart/form-data">
            <input type="hidden" name="action" value="wce_add_employee" />
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('wce_add_employee_nonce'); ?>" />
            <div class="form-group">
                <label for="fullname">Full Name:</label>
                <input type="text" name="fullname" placeholder="Employee Name" id="fullname" required />
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" placeholder="Employee Email" id="email" required />
            </div>
            <div class="form-group">
                <label for="designation">Designation:</label>
                <select name="designation" id="designation" required>
                    <option value="">-- Choose Designation --</option>
                    <option value="php">PHP Developer</option>
                    <option value="full">Full Stack Developer</option>
                    <option value="wordpress">WordPress Developer</option>
                    <option value="java">Java Developer</option>
                </select>
            </div>
            <div class="form-group">
                <label for="file">Profile Image:</label>
                <input type="file" name="file" id="file" />
            </div>
            <div class="form-group">
                <button id="btn_save_data" type="submit" class="btn btn-primary">Save Data</button>
            </div>
        </form>
    </div>

    <!-- Edit Employee Card -->
    <div class="card edit-employee-form hide-element">
        <button id="close-edit-emp-form">Close Form</button>
        <h3 class="section-title">Edit Employee</h3>
        <form action="javascript:void(0);" id="form_edit_employee" class="employee-form" enctype="multipart/form-data">
            <input type="hidden" name="action" value="wce_edit_employee" />
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('wce_edit_employee_nonce'); ?>" />
            <input type="hidden" name="emp_id" id="emp_id" />
            <div class="form-group">
                <label for="emp_fullname">Full Name:</label>
                <input type="text" name="emp_fullname" placeholder="Employee Name" id="emp_fullname" required />
            </div>
            <div class="form-group">
                <label for="emp_email">Email:</label>
                <input type="email" name="emp_email" placeholder="Employee Email" id="emp_email" required />
            </div>
            <div class="form-group">
                <label for="emp_designation">Designation:</label>
                <select name="emp_designation" id="emp_designation" required>
                    <option value="">-- Choose Designation --</option>
                    <option value="php">PHP Developer</option>
                    <option value="full">Full Stack Developer</option>
                    <option value="wordpress">WordPress Developer</option>
                    <option value="java">Java Developer</option>
                </select>
            </div>
            <div class="form-group">
                <label for="emp_file">Profile Image:</label>
                <input type="file" name="emp_file" id="emp_file" />
                <br/>
                <img id="emp_profile_image" style="width: 100px;height: 100px;" />
            </div>
            <div class="form-group">
                <button id="btn_edit_data" type="submit" class="btn btn-primary">Update Employee Data</button>
            </div>
        </form>
    </div>

    <!-- List of All Employees Card -->
    <div class="card">
        <button id="add-employee">Add Employee</button>
        <h3 class="section-title">List of All Employees</h3>
        <table class="employee-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Designation</th>
                    <th>Profile Image</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="tbody_employees_data"></tbody>
        </table>
    </div>
</div>