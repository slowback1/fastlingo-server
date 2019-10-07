<?php
    //TO-DO:
    //      * Read and Sanitize Input
    //      * Call all functions in the __construct function
    //      * Create messages to be echoed based on the result of each function
    class ConfirmFriend {
        private $db;
        public function __construct() {
            include '../../config/db.php';
            $this->db = new Data();
        }
        private function checkIfInConfirmList($id, $fid) {
            $selSQL = "SELECT confirm_list FROM user_friends WHERE id = $fid";
            $res = $this->db->conn->query($selSQL);
            if($res->num_rows > 0) {
                while($row = $res->fetch_assoc()) {
                    $confirmList = explode("," , $row['confirm_list']);
                    foreach($confirmList as $confirmItem) {
                        if(trim($confirmItem) === trim($id)) {
                            return true;
                        }
                    }
                }
            }
            return false;
        }
        private function getOldFriendsList($fid) {
            $getSQL = "SELECT friends_list FROM user_friends WHERE id = $fid";
            $friends_list;
            $getRes = $this->db->conn->query($getSQL);
            if($getRes->num_rows > 0) {
                while($row = $getRes->fetch_assoc()) {
                    $friends_list = explode(",", $row['friends_list']);
                }
            }
            return $friends_list;
        }
        private function insertIntoFriendsList($friends_list, $id, $fid) {
            $friendsString = implode(",", $friends_list) . ", $id";
            $pubSQL = "UPDATE user_friendsd SET friends_list = $friendsString WHERE id = $fid";
            if($this->db->conn->query($putSQL)) {
                return true;
            } else {
                return false;
            }
        }
    }
$a = new ConfirmFriend();
?>
