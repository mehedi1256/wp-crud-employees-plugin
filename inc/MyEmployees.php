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
    public function handleDeleteEmployeeData() {

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
}
