<?php
require_once(dirname(__FILE__).'/../cores/model.php');

class ReceiptModel extends Model {
    public function save_receipt($title, $total, $currency, $id=null){
        $sql = 'INSERT INTO receipts(title, total, currency, created_at, updated_at) VALUES(:title, :total, :currency, :created_at, :updated_at)';
        $params = [
            ':title' => $title, 
            ':total' => $total,  
            ':currency' => $currency,
            ':created_at' => date('Y-m-d H:i:s'),
            ':updated_at' => date('Y-m-d H:i:s'),
        ];

        if(!empty($id)){
            $params[':id'] = $id;
            unset($params[':created_at']);

            $select_params = $params;
            unset($select_params[':currency'], $select_params[':updated_at']);

            $receipt = $this->query(
                'SELECT * FROM receipts WHERE id = :id AND title = :title AND total = :total', 
                $select_params);
            
            if(!empty($receipt) && isset($receipt['data']) && count($receipt['data']) > 0){
                return $id;
            }

            $receipt_old = $this->query('SELECT * FROM receipts WHERE id = :id', [':id' => $params[':id']]);

            $sql = 'UPDATE receipts SET title = :title, total = :total, currency = :currency, updated_at = :updated_at WHERE id = :id';
            
            $data = $this->query($sql, $params);

            $receipt_new = $this->query('SELECT * FROM receipts WHERE id = :id', [':id' => $params[':id']]);

            $this->log_update($id, 'receipts', json_encode($receipt_old['data'][0]), json_encode($receipt_new['data'][0]));

            return $id;
        }
        else{
            $data = $this->query($sql, $params);
            return $this->pdo->lastInsertId();
        }
    }

    public function save_item($receipt_id, $number, $amount, $total, $currency, $is_multiply, $id=null){
        $sql = 'INSERT INTO items(receipt_id, number, amount, total, currency, is_multiply, created_at, updated_at) VALUES(:receipt_id, :number, :amount, :total, :currency, :is_multiply, :created_at, :updated_at)';
        $params = [
            ':receipt_id' => (int) $receipt_id,
            ':number' => $number, 
            ':amount' => $amount,
            ':total' => $total,  
            ':currency' => $currency,
            ':is_multiply' => $is_multiply,
            ':created_at' => date('Y-m-d H:i:s'),
            ':updated_at' => date('Y-m-d H:i:s'),
        ];

        $is_update = false;

        if(!empty($id)){
            $params[':id'] = $id;

            unset($params[':receipt_id'], $params[':created_at'], $params[':currency']);

            $select_params = $params;
            unset($select_params[':updated_at']);

            $item = $this->query(
                'SELECT * FROM items WHERE id = :id AND number = :number AND amount = :amount AND total = :total AND is_multiply = :is_multiply', 
                $select_params);
            
            if(!empty($item) && isset($item['data']) && count($item['data']) == 0){
                $sql = 'UPDATE items SET number = :number, amount = :amount, total = :total, is_multiply = :is_multiply, updated_at = :updated_at WHERE id = :id';
                $is_update = true;
            }
            else{
                return $item;
            }
        }

        $item_old = null;
        if($is_update){
            $item_old = $this->query('SELECT * FROM items WHERE id = :id', [':id' => $params[':id']]);
        }

        $data = $this->query($sql, $params);

        if($is_update){
            $item_new = $this->query('SELECT * FROM items WHERE id = :id', [':id' => $params[':id']]);
            $this->log_update($id, 'items', json_encode($item_old['data'][0]), json_encode($item_new['data'][0]));
        }

        return $data;
    }

    public function list_receipt($id=null, $per_page=20, $page=1){
        $sql = 'select * from receipts where deleted_at is null';
        $params = [];

        if(!empty($id)){
            $sql .= ' and id = :id';
            $params[':id'] = $id;
        }
        else{
            $sql .= ' LIMIT :limit OFFSET :offset';
            $params[':limit'] = $per_page;
            $params[':offset'] = $per_page * ($page-1);
        }

        $data = $this->query($sql, $params);

        return $data;
    }

    public function receipt_detail($id){
        $data = $this->query(
            'select * from receipts where deleted_at is null and id = :id', 
            [
                ':id' => $id
            ]);
        
        $receipt = [];
        if(count($data['data']) > 0){
            $receipt = $data['data'][0];
            $data = $this->query(
                'select * from items where deleted_at is null and receipt_id = :receipt_id', 
                [
                    ':receipt_id' => $receipt['id']
                ]);
            
            if(count($data['data']) > 0){
                $receipt['items'] = $data['data'];
            }
        }
        
        return $receipt;
    }

    public function count_receipt(){
        $sql = 'SELECT count(*) AS count FROM receipts WHERE deleted_at IS NULL';
        $params = [];

        $data = $this->query($sql, $params);

        return $data;
    }

    public function delete_receipt($id){
        // $sql = 'DELETE FROM receipts WHERE id = :id';
        $sql = 'UPDATE receipts SET deleted_at = :deleted_at  WHERE id = :id';

        $receipt = $this->query('SELECT * FROM receipts WHERE id = :id', [':id' => $id]);
        $this->log_delete($id, 'receipts', json_encode($receipt['data'][0]));

        $data = $this->query($sql, [
            ':id' => $id,
            ':deleted_at' => date('Y-m-d H:i:s')
        ]);

        $this->delete_receipt_items($id);

        return $data;
    }

    public function delete_receipt_items($receipt_id, $not_delete_ids=[]){
        $sql_select = 'SELECT * FROM items WHERE receipt_id = :receipt_id';
        // $sql_delete = 'DELETE FROM items WHERE receipt_id = :receipt_id';
        $sql_delete = 'UPDATE items SET deleted_at = :deleted_at WHERE receipt_id = :receipt_id';
        
        if(count($not_delete_ids)>0){
            $sql_select .= ' AND id NOT IN('.implode(', ', $not_delete_ids).')';
            $sql_delete .= ' AND id NOT IN('.implode(', ', $not_delete_ids).')';
        }

        $items = $this->query($sql_select, [':receipt_id' => $receipt_id]);
        foreach($items['data'] as $item){
            $this->log_delete($item['id'], 'items', json_encode($item));
        }

        $data = $this->query($sql_delete, [
            ':receipt_id' => $receipt_id,
            ':deleted_at' => date('Y-m-d H:i:s')
        ]);
        return $data;
    }

}