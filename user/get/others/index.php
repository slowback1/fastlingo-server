<?php
    //get users in friends list's information
    class GetOthers {
        private $db;
        private $userid;
        private $friends_list;
        public function __construct() {
            include '../../../config/db.php';
            $this->db = new Data();
            $this->userid = $this->db->getUserId();
            $this->friends_list = $this->getFriends($this->userid);
            $resArr = $this->getOthersInfo($this->friends_list);
            echo json_encode($resArr);
        }
        private function getFriends($id) {
            $sql = "SELECT friends_list FROM user_friends WHERE id = '$id'";
            $res = $this->db->conn->query($sql);
            if($res->num_rows > 0) {
                while($row = $res->fetch_assoc()) {
                    return explode(",", $row['friends_list']);
                }
            }
        }
        private function getOthersInfo($listOfIds) {
            $idsql = "WHERE ";
            foreach($listOfIds as $id) {
                $idsql = $idsql . "user_login.id = $id OR ";
            }
            //get rid of the dangling OR
            $idsql = substr($idsql, 0, -3);
            $sql = "SELECT user_login.username, user_preferences.goal_duration, user_preferences.goal_type, user_friends.friends_list FROM user_login INNER JOIN user_preferences ON user_login.id=user_preferences.id INNER JOIN user_friends ON user_login.id=user_friends.id $idsql";
            $res = $this->db->conn->query($sql);
            $resArr = array();
            if($res->num_rows > 0) {
                while($row = $res->fetch_assoc()) {
                    array_push($resArr, $row);
                }
            }
            return $resArr;
        }
    }
    $a = new GetOthers();

?>