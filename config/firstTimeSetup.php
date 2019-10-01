<?php
    //this file should be run once, then deleted.  It will create the appropriate tables in an already created database
    class FirstTimeSetup {
        private $db;
        private $settings;
        public function __construct() {
            include dirname(__FILE__) . "/settings.php";
            $this->settings = $settings;
            $this->db = new mysqli($this->settings['hostname'], $this->settings['username'], $this->settings['password'], $this->settings['dbname']);
            foreach($this->queries as $query) {
                $this->db->query($query);
            }

        }
        private $queries = array(
            "CREATE TABLE IF NOT EXISTS `user_login` ( `id` INT NOT NULL AUTO_INCREMENT , `username` VARCHAR NOT NULL , `password` VARCHAR NOT NULL , `email` VARCHAR NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB",
            "CREATE TABLE IF NOT EXISTS `user_preferences` ( `id` INT NOT NULL , `color_mode` ENUM('light','dark') NOT NULL DEFAULT 'light' , `goal_duration` TINYINT NOT NULL DEFAULT '16' , `goal_type` ENUM('hour','day') NOT NULL DEFAULT 'hour' , PRIMARY KEY (`id`)) ENGINE = InnoDB"
        );
        

    }
?>