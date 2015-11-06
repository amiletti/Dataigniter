<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {

  public function index()
  {
    $this->load->view('users/index');
  }


  public function dataigniter()
  {
    $this->load->database();

    $this->config->load('dataigniter', TRUE);
    $config = $this->config->item('users', 'dataigniter');

    $config['request'] = $this->input->post();
    
    $this->load->library('Datainginter', $config);
    echo json_encode($this->datainginter->get_data());
  }
}
