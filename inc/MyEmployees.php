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

    // create db table
    public function createEmployeesTable()
    {
        $collate  = $this->wpdb->get_charset_collate();
        $createCommand = "
        CREATE TABLE IF NOT EXISTS {$this->table_name} (
            id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            fullname VARCHAR(50) NOT NULL,
            email VARCHAR(50) DEFAULT NULL,
            designation VARCHAR(50) DEFAULT NULL
            ) {$collate}
        ";

        require_once(ABSPATH . "/wp-admin/includes/upgrade.php");
        dbDelta($createCommand);
    }

    // drop db table
    public function dropEmployeesTable() {
        $dropCommand = "DROP TABLE IF EXISTS {$this->table_name}";
        $this->wpdb->query($dropCommand);
    }
}
