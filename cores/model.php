<?php
require_once('config.php');

class Model {
    private $db_config = [];
    public $pdo = null;
    protected $table = null;

    public function __construct() {
        $config = require 'config.php';
        $this->db_config = $config['database'];
        $this->connect();
    }

    public function __destruct() {
        $this->pdo = null;
    }

    private function connect(){
        $dsn = "pgsql:host=".$this->db_config['host'].";port=5432;dbname=".$this->db_config['db'].";";
        $options = [
            PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
        ];
        try {
            // make a database connection
            $this->pdo = new PDO(
                $dsn, 
                $this->db_config['user'], 
                $this->db_config['password'], 
                $options
            );
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function query($sql, $params=[]){
        if(!empty($sql)){
            try{
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($params);
                $data = [
                    'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
                    'count' => $stmt->rowCount()
                ];
                $stmt = null;
                return $data;
            } catch (Exception $e) {
                die($e->getMessage());
            }
        }

        return null;
    }

    private function log_action($action, $activity_id, $activity_type, $old_data=null, $new_data=null){        
        $this->query(
            'INSERT INTO activity_logs(activityable_id, activityable_type, action, old_data, new_data, created_at, updated_at) VALUES(:activityable_id, :activityable_type, :action, :old_data, :new_data, :created_at, :updated_at)',
            [
                ':activityable_id' => $activity_id, 
                ':activityable_type' => $activity_type, 
                ':action' => $action, 
                ':old_data' => $old_data, 
                ':new_data' => $new_data,
                ':created_at' => date('Y-m-d H:i:s'),
                ':updated_at' => date('Y-m-d H:i:s'),
            ]);
    }

    protected function log_insert($activity_id, $activity_type, $new_data){
        $this->log_action('insert', $activity_id, $activity_type, null, $new_data);
    }

    protected function log_update($activity_id, $activity_type, $old_data, $new_data){
        $this->log_action('update', $activity_id, $activity_type, $old_data, $new_data);
    }

    protected function log_delete($activity_id, $activity_type, $old_data){
        $this->log_action('delete', $activity_id, $activity_type, $old_data, null);
    }

    // public function create($column_data){
    //     if(is_array($column_data) && count($column_data)>0){
    //         try {
    //             $sql = "INSERT INTO ".$this->table."(symbol,company) VALUES(:symbol,:company)";
    //             $stmt = $this->pdo->prepare($sql);
                
    //             // pass values to the statement
    //             $stmt->bindValue(':symbol', $symbol);
    //             $stmt->bindValue(':company', $company);
                
    //             // execute the insert statement
    //             $stmt->execute();

    //             var_dump();
    //         } catch (Exception $e) {
    //             // die($e->getMessage());
                
    //         }
    //     }
        
    //     return 0;
    // }
    // public function update($column_data){
    //     try {
    //         $sql = 'UPDATE stocks '
    //             . 'SET company = :company, '
    //             . 'symbol = :symbol '
    //             . 'WHERE id = :id';

    //         $stmt = $this->pdo->prepare($sql);

    //         // bind values to the statement
    //         $stmt->bindValue(':symbol', $symbol);
    //         $stmt->bindValue(':company', $company);
    //         $stmt->bindValue(':id', $id);
    //         // update data in the database
    //         $stmt->execute();

    //         // return the number of row affected
    //         return $stmt->rowCount();
    //     } catch (Exception $e) {
    //         die($e->getMessage());
    //     }
    // }

    // public function delete($id){
    //     try{
    //         $sql = 'DELETE FROM stocks WHERE id = :id';

    //         $stmt = $this->pdo->prepare($sql);
    //         $stmt->bindValue(':id', $id);

    //         $stmt->execute();

    //         return $stmt->rowCount();
    //     } catch (Exception $e) {
    //         die($e->getMessage());
    //     }
    // }

    // public function select($table, $where, $columns=[]){
    //     try{
    //         $sql = 'DELETE FROM stocks WHERE id = :id';

    //         $stmt = $this->pdo->prepare($sql);
    //         $stmt->bindValue(':id', $id);
    //         $stmt->execute();
            
    //         return $stmt->fetchAll();
    //     } catch (Exception $e) {
    //         die($e->getMessage());
    //     }
    // }

}