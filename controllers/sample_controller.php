<?php
require_once('cores/controller.php');

class SampleController extends Controller {
    function index(){
        $this->view('index', [
            
        ]);
    }
}