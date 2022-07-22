<?php
require_once('cores/controller.php');

class HomeController extends Controller {
    function index(){
        $this->view('index');
    }
}