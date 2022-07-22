<?php
require_once(dirname(__FILE__).'/../cores/model.php');

class ReportModel extends Model {
    public function count_number($date_from, $date_to, $per_page=20, $page=1){
        $sql = 'select "number", count("number") from items where deleted_at is null and created_at >= :date_from and created_at <= :date_to group by "number" order by "number" asc LIMIT :limit OFFSET :offset';
        $params = [
            ':date_from' => $date_from,
            ':date_to' => $date_to,
            ':limit' => $per_page,
            ':offset' => $per_page * ($page-1)
        ];

        $data = $this->query($sql, $params);

        return $data;
    }

    public function count_row($date_from, $date_to){
        $sql = 'select count(*) from ( select "number", count("number") from items where deleted_at is null and created_at >= :date_from and created_at <= :date_to group by "number" order by "number" asc ) as tbl';
        $params = [
            ':date_from' => $date_from,
            ':date_to' => $date_to
        ];

        $data = $this->query($sql, $params);

        return $data;
    }

}