<?php
require_once('cores/controller.php');

class FruitController extends Controller {
    function index(){
        $this->view('fruit/index');
    }
}