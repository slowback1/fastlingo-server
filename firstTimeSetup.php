<?php
    //this file should be run once, then deleted.  It will create the appropriate tables in an already created database
    class FirstTimeSetup {
        private $db;
        public function __construct() {
            $this->db = new mysqli($this->settings['hostname'], $this->settings['username'], $this->settings['password'], $this->settings['dbname']);
            foreach($this->queries as $query) {
                $this->db->query($query);
            }
            echo "yeet!";
        }
        private $queries = array(
            "CREATE TABLE IF NOT EXISTS `user_login` ( `id` INT(10) NOT NULL AUTO_INCREMENT , `username` VARCHAR(2500) NOT NULL , `password` VARCHAR(2500) NOT NULL , `email` VARCHAR(2500) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB"
        );
        private $settings = array(
            "hostname" => "",
            "username" => "",
            "password" => "",
            "dbname"   => ""
        );

    }
?>