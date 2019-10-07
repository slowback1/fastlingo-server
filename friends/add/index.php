<?php
    //NOTE: this adds person to the "to-be confirmed" list in the DB, does not actually add to friends list, see ../confirm/index.php for that
    //TO-DO:
    //      * Read and Sanitize Input
    //      * Call all functions in the __construct function
    //      * Create messages to be echoed based on the result of each function
    class AddFriend {
        private $db;
        public function __construct() {
            include '../../config/db.php';
            $this->db = new Data();
            
        }
        private function checkIfAlreadyFriend($id, $fid) {
            $selSQL = "SELECT friends_list, confirm_list FROM user_friends WHERE id = $id OR $fid";
            $isInList = false;
            $a = "a";
            $res = $this->db->conn->query($selSQL);
            if($res->num_rows > 0) {
                while($row = $res->fetch_assoc()) {
                    $friends_list = $row['friends_list'];
                    $confirm_list = $row['confirm_list'];
                    $this->iterateList($friends_list, $id) ? true : $isInList = true;
                    $this->iterateList($confirm_list, $id) ? true : $isInList = true;
                    
                }
            }
            if($isInList) {
                return true;
            } else {
                return false;
            }
        }
        private function iterateList($list, $id) {
            $isUnique = true;
            foreach($list as $item) {
                if($item == $id) {
                    $isUnique = false;
                }
            }
            return $isUnique;
        }
        private function getOldConfirmList($fid) {
            $getSQL = "SELECT confirm_list FROM user_friends WHERE id = $fid";
            $confirm_list;
            $getRes = $this->db->conn->query($getSQL);
            if($getRes->num_rows > 0) {
                while($row = $getRes->fetch_assoc()) {
                    $confirm_list = explode(",", $row['confirm_list']);
                }
            }
            return $confirm_list;
        }
        private function insertIntoConfirmList($confirmList, $id, $fid) {
            $confirmString = implode(",", $confirmList) . ", $id";
            $putSQL = "UPDATE user_friends SET confirm_list = $confirmString WHERE id = $fid";
            if($this->db->conn->query($putSQL)) {
                return true; 
            } else {
                return false;
            }
            
        }
    }
    $a = new AddFriend();

?>