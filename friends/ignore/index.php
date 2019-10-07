<?php
    //TO-DO:
    //      * Read and Sanitize Input
    //      * Call all functions in the __construct function
    //      * Create messages to be echoed based on the result of each function
    class Ignore {
        private $db;
        public function __construct() {
            include '../../config/db.php';
            $this->db = new Data();
        }
        private function getOldIgnoreList($id) {
            $sql = "SELECT ignore_list FROM user_friends WHERE id = $id";
            $res = $this->db->conn->query($sql);
            $ignore_list;
            if($res->num_rows > 0) {
                while($row = $res->fetch_assoc()) {
                    $ignore_list = explode(',', $row['ignore_list']);
                }
            }
            return $ignore_list;
        }
        private function checkIfInIgnoreList($ignore_list, $fid) {
            foreach($ignore_list as $item) {
                if(trim($item) == trim(strval($fid))) {
                    return true;
                }
            }
            return false;
        }
        private function insertIntoIgnoreList($ignore_list, $fid, $id) {
            $a = $ignore_list;
            array_push($a, $fid);
            $ignoreString = implode(",", $a);
            $putSQL = "UPDATE user_friends SET ignore_list = $ignoreString WHERE id = $id";
            if($this->db->conn->query($putSQL)) {
                return true;
            }  else {
                return false;
            }
        }
    }
    $a = new Ignore();
?>