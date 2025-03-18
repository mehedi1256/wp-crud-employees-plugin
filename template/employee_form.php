<div id="wp_employee_crud_plugin">
    <h3 class="section-title">Add Employee</h3>
    <form action="javascript:void(0);" id="form_add_employee" enctype="multipart/form-data" class="employee-form">
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
    <h3 class="section-title">List of all Employees</h3>
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
        <tbody>
            <tr>
                <td>1</td>
                <td>Mehedi Hassan Shovo</td>
                <td>mehedi@gmail.com</td>
                <td>SWE</td>
                <td>---</td>
                <td>
                    <button class="btn btn-edit">Edit</button>
                    <button class="btn btn-delete">Delete</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
