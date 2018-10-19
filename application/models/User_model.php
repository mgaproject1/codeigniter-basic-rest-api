<?php
class User_model extends CI_Model {


    public function check_username( $username = "" ){
        $check = $this->db->query("select user_id from s_users where username = '$username'")->row();
        return empty($check) ? true : false;
    }

    public function valid_username( $username = "" ){
        $check =  preg_match('/^[A-Za-z0-9_~\-!@#\$%\^&*\(\)]+$/', $username);
        return empty($check) ? false : true;
    }

    public function check_email( $email = "" ){
        $check = $this->db->query("select user_id from s_users where email = '$email'")->row();
        return empty($check) ? true : false;
    }

    public function valid_email($email = ""){
        $check = filter_var($email, FILTER_VALIDATE_EMAIL);

        return empty($check) ? false : true;
    }

    public function register( $fields = array() ){

        if( empty($fields) || !is_array($fields) )
            return false;

        extract($fields);
        $error = array();

        if( empty($username) ){
            $error["username"] = "Bu alan zorunlu!";
        }else{
            if( !$this->check_username($username) ){
                $error["username"] = "Kullanıcı adı kayıtlı";
            }

            if( !$this->valid_username($username) ){
                $error["username"] = "Sadece harf, rakam ve '_' kullanabilirsin";
            }
        }

        if( empty($email) ){
            $error["email"] = "Bu alan zorunlu!";
        }else{

            if( !$this->check_email($email) ){
                $error["email"] = "E-mail kayıtlı";
            }
        }

        if( empty($password) ){
            $error["password"] = "Bu alan zorunlu!";
        }

        if( empty($fullname) ){
            $error["fullname"] = "Bu alan zorunlu!";
        }

        if( count($error) == 0 ) {
            $this->db->insert("s_users", array(
                "username" => $username,
                "email" => $email,
                "user_pass" => $password,
                "user_status" => 1,
                "fullname" => $fullname
            ));

            $user_id = $this->db->insert_id();

            $result = array(
                "status" => true,
                "user_id" => $user_id
            );
        }else{
            $result = array(
                "status" => false,
                "errors" => $error
            );
        }

        return $result;
    }

    private function login_progress( $data = array() ){
        if( empty($data) || !is_array($data) )
            return false;

        extract($data);

        $sql = "select * from s_users where";

        if( $type == "email" ){
            $sql.= " email = '$value' and ";
        }elseif ($type == "username"){
            $sql.= " username = '$value' and ";
        }

        $sql.= "user_pass = '$password' and user_status = 1";

        $check = $this->db->query($sql)->row();

        return empty($check) ? false : $check;
    }

    public function login( $data = array() ){

        if( empty($data) || !is_array($data) )
            return false;

        extract($data);
        $error = array();

        if( empty($field) ){
            $error["field"] = "Bu alan zorunlu!";
        }

        if( empty($password) ){
            $error["password"] = "Bu alan zorunlu!";
        }

        if( count($error) == 0 ){

            if( $this->valid_email($field) ){
                $login_data = array(
                    "type" => "email",
                    "value" => $field
                );
            }else{
                $login_data = array(
                    "type" => "username",
                    "value" => $field
                );
            }

            $progress = $this->login_progress( array_merge($login_data, array("password" => $password)) );

            if( $progress ){
                $result = array(
                    "status" => true
                );
            }else{
                $result = array(
                    "status" => false,
                    "errors" => "Doğrulama başarısız!"
                );
            }
        }else{
            $result = array(
                "status" => false,
                "errors" => $error
            );
        }

        return $result;
    }

}