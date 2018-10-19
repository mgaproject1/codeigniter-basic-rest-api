<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model("user_model");
    }

    public function index(){

    }

    public function register(){

        $data = array(
            "username" => $this->input->post("username"),
            "email" => $this->input->post("email"),
            "password" => $this->input->post("password"),
            "fullname" => $this->input->post("fullname")
        );

        echo json_encode($this->user_model->register($data));
    }

    public function login(){
        echo json_encode($this->user_model->login(array(
            "field" => $this->input->post("field"),
            "password" => $this->input->post("password")
        )));
    }
}
