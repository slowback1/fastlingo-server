<?php
    //TO-DO:
    //      * Read and Sanitize Input
    //      * Call all functions in the __construct function
    //      * Create messages to be echoed based on the result of each function
    class RemoveFriend {
        private $db;
        public function __construct() {
            include '../../config/db.php';
            $this->db = new Data();
        }
        private function getFriendsList($id) {
            $sql = "SELECT friends_list FROM user_friends WHERE id = $id";
            $res = $this->db->conn->query($sql);
            if($res->num_rows > 0) {
                while($row = $res->fetch_assoc()) {
                    return explode(",", $row['friends_list']);
                }
            }
        }
        private function removeFriend($friends_list, $fid) {
            $index = array_search($fid, $friends_list);
            unset($friends_list[$index]);
        }
        private function updateFriendsList($friends_list, $id) {
            $friendsString = implode(",", $friends_list); 
            $sql = "UPDATE user_friends SET friends_list =  $friendsString WHERE id = $id";
            if($this->db->conn->query($sql)) {
                return true;
            } else {
                return false;
            }
        }
    }
    $a = new RemoveFriend();
?>