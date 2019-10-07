<?php
//get self information
    class GetSelf {
        private $db;
        private $userid;
        public function __construct() {
            include '../../../config/db.php';
            $this->db = new Data();
            $this->userid = $this->db->getUserId();

            $resArr = $this->getUserInfo($this->userid);
            echo json_encode($resArr);
        }
        private function getUserInfo($id) {
            //that's a doozy of a query!
            $sql = "SELECT user_login.username, user_preferences.color_mode, user_preferences.goal_duration, user_preferences.goal_type, user_friends.friends_list, user_friends.confirm_list FROM user_login INNER JOIN user_preferences ON user_login.id=user_preferences.id INNER JOIN user_friends ON user_login.id=user_friends.id WHERE user_login.id = '$id'";
            $res = $this->db->conn->query($sql);
            if($res->num_rows > 0) {
                while($row = $res->fetch_assoc()) {
                    $row["result"] = true;
                    return $row;
                }
            } else {
                return array("result" => false);
            }
        }
    }
    $a = new GetSelf();
?>