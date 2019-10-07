<?php
    //this class is the big top-level class when it comes to anything related to the database
    //it houses the mysqli connection, as well as any functions that are not unique to a particular endpoint, such as verifying authentication tokens
    //possible to-do: 
        //if this file gets too large, I may split off the generic functions into a seperate class(es) that gets loaded into this class
    class Data {
        public $conn;
        private $settings;
        private $jwt;
        public function __construct() {
            $this->checkFTS();
            $this->setSettings();
            $this->connectToDB();
            $this->setJWT();
        }
        //checks if firsttimesetup.php needs to run
        private function checkFTS() {
            if(file_exists(dirname(__FILE__) . '/firstTimeSetup.php')) {
                include dirname(__FILE__) . '/firstTimeSetup.php';
                $a = new FirstTimeSetup();
                //uncomment the below line in production to autodelete the firsttimesetup file
                //unlink('../firstTimeSetup.php');
            }
        }
        //sets $this->settings 
        private function setSettings() {
            include dirname(__FILE__) . '/settings.php';
            $this->settings = $settings;
        }
        //opens a mysqli connection, and checks for connection errors
        private function connectToDB() {
            $this->conn = new mysqli($this->settings['hostname'], $this->settings['username'], $this->settings['password'], $this->settings['dbname']);
            if($this->conn->connect_error) {
                die($this->conn->connect_error);
            }
        }
        //sets $this->jwt to the JWT class
        private function setJWT() {
            include dirname(__FILE__) . '/vendor/firebase/php-jwt/src/JWT.php';
            $this->jwt = new JWT();
        }
        public function hashPW($p) {
            return password_hash($p, PASSWORD_BCRYPT);
        }
        //returns a string, sanitized to help prevent sql injection and other bad things
        public function sanitize($input) {
            return htmlspecialchars(stripslashes(trim($input)));
        }  
        //returns a bool, checks whether username/email is inside the database or not
        public function checkUnique($input, $type) {
            if(empty($input) || empty($type)) {
                return false;
            }
            $sql = "SELECT COUNT(*) FROM user_login WHERE $type = '$input'";
            $r = $this->conn->query($sql);
            $c = ($r->fetch_row())[0];
            $res;
            if($c > 0) {
                $res = false;
            } else {
                $res = true;
            }
            return $res;
        }
        //returns a (encoded) JWT, taken from the request header
        private function getToken() {
            $headers = getallheaders();
            $token = $headers['jwt'];
            return $token;
        }
        //returns an int corresponding to user id
        public function getUserId() {
            $token = $this->getToken();
            $decoded = $this->jwt::decode($token, $this->settings['secret_key'], array('HS256'));
            $uname = $decoded->username;
            $sql = "SELECT id FROM user_login WHERE username = '$uname'";
            $res = $this->conn->query($sql);
            if($res->num_rows > 0) {
                return $res->fetch_row()[0];
            } else {
                return false;
            }
        }
        //returns a JWT, to be sent back with response
        public function setToken($username, $password) {
            $token = array(
                "username" => $username,
                "password" => $password
            );
            return $this->jwt::encode($token, $this->settings['secret_key']);   
        }
        //returns a bool, verifying whether or not token corresponds to a user
        public function verifyToken() {
            $token = $this->getToken();
            $decoded = $this->jwt::decode($token, $this->settings['secret_key'], array('HS256'));
            $uname = $decoded->username;
            $pw = $decoded->password;
            $sql = "SELECT password FROM user_login WHERE username = '$uname'";
            $r = $this->conn->query($sql);
            if($r->num_rows > 0) {
                if($pw === $r->fetch_row()[0]){
                    return true;
                } else {
                    //bad password
                    return false;
                }
            } else {
                //bad username
                return false;
            }
            
        }
        //tablename is a string
        //data is an associative array, with one or more non-id values
        //id is an int
        //returns a sql querystring, or false
        public function getUpdateQuery($tableName, $data, $id) {
            //dont want to ever change id
            if(array_key_exists('id', $data)) {
                unset($data['id']);
            }
            //fetch column names
            $gsql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$this->settings['dbname'] ."' AND TABLE_NAME = '$tableName'";
            $k = $this->conn->query($gsql);
            $keys = array();
            if($k->num_rows > 0) {
                while($row = $k->fetch_assoc()) {
                    array_push($keys, $row["COLUMN_NAME"]);
                }
            }
            //add values to be updated to $values
            $values = array();
            foreach($keys as $key) {
                array_key_exists($key, $data) ? array_push($values, array($key, $data[$key])) : false;
            }

            //return false if nothing is going to be updated
            if(sizeof($values) === 0) {
                return false;
            }
            //build sql querystring
            $resstr = "UPDATE $tableName SET ";
            foreach($values as $value) {
                if(!empty($value[1])) {
                    $resstr = $resstr . $value[0] . " = '$value[1]' , ";
                }
            }
            $resstr = substr($resstr, 0 , -2) . " WHERE id = $id";
            return $resstr;
        }
    }
?>