<?php
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
        private function checkFTS() {
            if(file_exists(dirname(__FILE__) . '/firstTimeSetup.php')) {
                include dirname(__FILE__) . '/firstTimeSetup.php';
                $a = new FirstTimeSetup();
                //uncomment the below line in production to autodelete the firsttimesetup file
                //unlink('../firstTimeSetup.php');
            }
        }
        private function setSettings() {
            include dirname(__FILE__) . '/settings.php';
            $this->settings = $settings;
        }
        private function connectToDB() {
            $this->conn = new mysqli($this->settings['hostname'], $this->settings['username'], $this->settings['password'], $this->settings['dbname']);
            if($this->conn->connect_error) {
                die($this->conn->connect_error);
            }
        }
        private function setJWT() {
            include dirname(__FILE__) . '/vendor/firebase/php-jwt/src/JWT.php';
            $this->jwt = new JWT();
        }
        public function hashPW($p) {
            return password_hash($p, PASSWORD_BCRYPT);
        }
        public function sanitize($input) {
            return htmlspecialchars(stripslashes(trim($input)));
        }  
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
        private function getToken() {
            $headers = getallheaders();
            $token = $headers['jwt'];
            return $token;
        }
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
        
        public function setToken($username, $password) {
            //I HOPE YOU LOOK AT THIS FUTURE SELF:  THIS FUNCTION NEEDS TO BE CALLED WITH THE HASHED PASSWORD NOT THE PLAINTEXT ONE!!!
            $token = array(
                "username" => $username,
                "password" => $password
            );
            return $this->jwt::encode($token, $this->settings['secret_key']);   
        }
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
                    return false;
                }
            } else {
                return false;
            }
            
        }
        //tablename is a string
        //data is an associative array, with one or more 'color_mode'(string), 'goal_type'(string), and 'goal_duration'(int) values
        //id is an int
        public function getUpdateQuery($tableName, $data, $id) {
            $gsql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$this->settings['dbname'] ."' AND TABLE_NAME = '$tableName'";
            foreach($data as $key => $value) {
                if($key === 'id') {
                    unset($data[$key]);
                }
            }
            $k = $this->conn->query($gsql);
            $keys = array();
            if($k->num_rows > 0) {
                while($row = $k->fetch_assoc()) {
                    array_push($keys, $row["COLUMN_NAME"]);
                }
            }
            $values = array();
            foreach($keys as $key) {
                array_key_exists($key, $data) ? array_push($values, array($key, $data[$key])) : false;
            }
            if(sizeof($values) === 0) {
                return false;
            }
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