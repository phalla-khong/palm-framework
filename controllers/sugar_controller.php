<?php
require_once('cores/controller.php');

class SugarController extends Controller {
    function index(){
        $this->view('sugar/index');
    }
}