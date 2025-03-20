<?php

class MyEmployees
{
    private $wpdb;
    private $table_prefix;
    private $table_name;
    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_prefix = $this->wpdb->prefix;
        $this->table_name = $this->table_prefix . 'employees';
    }

    // create db table + wordpress page
    public function callPluginActivationFunction()
    {
        $collate  = $this->wpdb->get_charset_collate();
        $createCommand = "
        CREATE TABLE IF NOT EXISTS {$this->table_name} (
            id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            fullname VARCHAR(50) NOT NULL,
            email VARCHAR(50) DEFAULT NULL,
            designation VARCHAR(50) DEFAULT NULL,
            profile_image VARCHAR(220) DEFAULT NULL
            ) {$collate}
        ";

        require_once(ABSPATH . "/wp-admin/includes/upgrade.php");
        dbDelta($createCommand);

        // Wp page

        $page_title = "Employee CRUD System 2";
        $page_content = "[wp-employee-form]";

        if (!get_page_by_title($page_title)) {
            wp_insert_post(array(
                "post_title" => $page_title,
                "post_content" => $page_content,
                "post_type" => "page",
                "post_status" => "publish"
            ));
        }
    }

    // drop db table
    public function dropEmployeesTable()
    {
        $dropCommand = "DROP TABLE IF EXISTS {$this->table_name}";
        $this->wpdb->query($dropCommand);
    }

    // render employee form layout

    public function createEmployeeForm()
    {

        ob_start();

        include_once WCE_DIR_PATH . "template/employee_form.php";

        $template = ob_get_contents();

        ob_end_clean();

        return $template;
    }

    // add CSS / JS
    public function addAssetsToPlugin()
    {
        // add style
        wp_enqueue_style("employee-crud-css", WCE_DIR_URL . "assets/style.css");
        // add validation
        wp_enqueue_script("wce-validation", WCE_DIR_URL . "assets/jquery.validate.min.js", array("jquery"));
        // add js
        wp_enqueue_script("employee-crud-js", WCE_DIR_URL . "assets/script.js", array("jquery"), "3.0");
        // add ajax url
        wp_localize_script('employee-crud-js', 'wce_object', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wce_add_employee_nonce')
        ]);
    }

    // process ajax request: add employee form
    public function handleAddEmployeeFormData()
    {
        // Debugging: Log the received POST data
        error_log(print_r($_POST, true));
        error_log(print_r($_FILES, true));

        // Check if action is set and correct
        if (!isset($_POST['action']) || $_POST['action'] !== 'wce_add_employee') {
            echo json_encode([
                "status" => 0,
                "message" => "Invalid action"
            ]);
            wp_die();
        }

        // Check nonce for security
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wce_add_employee_nonce')) {
            echo json_encode([
                "status" => 0,
                "message" => "Security check failed"
            ]);
            wp_die();
        }

        $full_name = sanitize_text_field($_POST['fullname']);
        $email = sanitize_text_field($_POST['email']);
        $designation = sanitize_text_field($_POST['designation']);
        $file_path = '';

        // Handle file upload
        if (!empty($_FILES['file']['name'])) {
            $upload = wp_upload_bits($_FILES['file']['name'], null, file_get_contents($_FILES['file']['tmp_name']));
            if (!$upload['error']) {
                $file_path = $upload['url'];
            } else {
                echo json_encode([
                    "status" => 0,
                    "message" => "File upload failed: " . $upload['error']
                ]);
                wp_die();
            }
        }

        $this->wpdb->insert($this->table_name, [
            "fullname" => $full_name,
            "email" => $email,
            "designation" => $designation,
            "profile_image" => $file_path
        ]);

        $employee_id = $this->wpdb->insert_id;

        if ($employee_id > 0) {
            echo json_encode([
                "status" => 1,
                "message" => "Employee created successfully"
            ]);
        } else {
            echo json_encode([
                "status" => 0,
                "message" => "Failed to save Employee"
            ]);
        }
        wp_die();
    }

    // load employee data from employee table
    public function handleLoadEmployeeData()
    {
        $employees = $this->wpdb->get_results(
            "SELECT * FROM {$this->table_name}",
            ARRAY_A
        );

        return wp_send_json([
            "status" => true,
            "message" => "Employees Data",
            "employees" => $employees
        ]);

        wp_die();
    }

    // delete employee data
    public function handleDeleteEmployeeData()
    {

        $employee_id = $_GET["empId"];

        $this->wpdb->delete($this->table_name, [
            "id" => $employee_id
        ]);

        return wp_send_json([
            "status" => true,
            "message" => "Employee Deleted Successfully"
        ]);

        wp_die();
    }

    // get single employee data by id
    public function handleToGetSingleEmployeeData()
    {
        $employee_id = $_GET["empId"];

        if ($employee_id > 0) {
            $employee_data = $this->wpdb->get_row(
                "SELECT * FROM {$this->table_name} WHERE id = {$employee_id}",
                ARRAY_A
            );

            return wp_send_json([
                "status" => true,
                "message" => "Employee Data foudn",
                "data" => $employee_data
            ]);
        } else {
            return wp_send_json([
                "status" => false,
                "message" => "Please pass employee id"
            ]);
        }
    }


    // update employee data
    public function handleUpdateEmployeeData()
    {
        // 1. Verify nonce for security
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wce_edit_employee_nonce')) {
            wp_send_json_error([
                'message' => 'Security check failed'
            ]);
        }

        // 2. Check user permissions (optional, adjust capability as needed)
        // if (!current_user_can('edit_posts')) {
        //     wp_send_json_error([
        //         'message' => 'Permission denied'
        //     ]);
        // }

        // 3. Validate and sanitize input data
        if (!isset($_POST['emp_id']) || !is_numeric($_POST['emp_id'])) {
            wp_send_json_error([
                'message' => 'Invalid employee ID'
            ]);
        }

        $employee_id = intval($_POST['emp_id']);
        $fullname = isset($_POST['emp_fullname']) ? sanitize_text_field($_POST['emp_fullname']) : '';
        $email = isset($_POST['emp_email']) ? sanitize_email($_POST['emp_email']) : '';
        $designation = isset($_POST['emp_designation']) ? sanitize_text_field($_POST['emp_designation']) : '';

        // Validate required fields
        if (empty($fullname) || empty($email) || empty($designation)) {
            wp_send_json_error([
                'message' => 'All fields (Full Name, Email, Designation) are required'
            ]);
        }

        // Validate email format
        if (!is_email($email)) {
            wp_send_json_error([
                'message' => 'Invalid email address'
            ]);
        }

        // Fetch the current employee data to get the existing profile image
        $employee = $this->fetchExistingEmployeeData($employee_id);

        if (!$employee) {
            wp_send_json_error([
                'message' => 'Employee not found'
            ]);
        }

        $old_file_url = !empty($employee['profile_image']) ? $employee['profile_image'] : '';
        $old_file_path = '';

        // Convert the file URL to a server path if there’s an existing file
        if ($old_file_url) {
            // Get the uploads directory
            $upload_dir = wp_upload_dir();
            $base_url = $upload_dir['baseurl'];
            $base_dir = $upload_dir['basedir'];

            // Convert the URL to a file path
            $old_file_path = str_replace($base_url, $base_dir, $old_file_url);

            // Ensure the path uses the correct directory separator for the server
            $old_file_path = wp_normalize_path($old_file_path);
        }

        // 4. Handle file upload (if a new profile image is provided)
        $file_path = '';
        if (!empty($_FILES['emp_file']['name'])) {
            $upload = wp_upload_bits($_FILES['emp_file']['name'], null, file_get_contents($_FILES['emp_file']['tmp_name']));
            if ($upload['error']) {
                wp_send_json_error([
                    'message' => 'File upload failed: ' . $upload['error']
                ]);
            }
            $file_path = $upload['url'];

            // Delete the old file if it exists
            if ($old_file_path && file_exists($old_file_path)) {
                // if (!unlink($old_file_path)) {
                //     error_log('Failed to delete old profile image: ' . $old_file_path);
                //     // Optionally, you can decide whether to fail the request here
                //     // For now, we'll log the error and continue
                // }
                // wp_die($old_file_path);
                unlink($old_file_path);
            }
        }

        // 5. Prepare data for update
        $data = [
            'fullname' => $fullname, // Match the database column name (was "name")
            'email' => $email,
            'designation' => $designation
        ];

        // Include profile image in the update if a new file was uploaded
        if (!empty($file_path)) {
            $data['profile_image'] = $file_path;
        }

        $where = [
            'id' => $employee_id
        ];

        // 6. Perform the update
        $updated = $this->wpdb->update(
            $this->table_name,
            $data,
            $where,
            ['%s', '%s', '%s', '%s'], // Format for data (all strings)
            ['%d'] // Format for where (integer)
        );

        // 7. Check if the update was successful
        if ($updated === false) {
            // Log the error for debugging
            error_log('WPDB Update Error: ' . $this->wpdb->last_error);
            wp_send_json_error([
                'message' => 'Failed to update employee data due to a database error'
            ]);
        } elseif ($updated === 0) {
            wp_send_json_error([
                'message' => 'No employee found with the given ID or no changes were made'
            ]);
        }

        // 8. Success response
        wp_send_json_success([
            'message' => 'Employee data updated successfully'
        ]);
    }

    // Helper function to fetch existing employee data
    private function fetchExistingEmployeeData($employee_id) {
        return $this->wpdb->get_row(
            $this->wpdb->prepare("SELECT profile_image FROM {$this->table_name} WHERE id = %d", $employee_id),
            ARRAY_A
        );
    }
}
