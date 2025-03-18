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

        if(!get_page_by_title($page_title)) {
            wp_insert_post(array(
                "post_title" => $page_title,
                "post_content" => $page_content,
                "post_type" => "page",
                "post_status" => "publish"
            ));
        }
    }

    // drop db table
    public function dropEmployeesTable() {
        $dropCommand = "DROP TABLE IF EXISTS {$this->table_name}";
        $this->wpdb->query($dropCommand);
    }

    // render employee form layout

    public function createEmployeeForm() {

        ob_start();

        include_once WCE_DIR_PATH . "template/employee_form.php";

        $template = ob_get_contents();

        ob_end_clean();

        return $template;
    }

    // add CSS / JS
    public function addAssetsToPlugin() {
        // add style
        wp_enqueue_style("employee-crud-css", WCE_DIR_URL . "assets/style.css");

        // add js
        wp_enqueue_script("employee-crud-js", WCE_DIR_URL . "assets/script.js", array("jquery"), "1.0.0");
        // add validation
        wp_enqueue_script("wce-validation", WCE_DIR_URL . "assets/jquery.validate.min.js", array("jquery"), "1.0.0");
    }
}
