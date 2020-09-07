<?php

namespace Omatech\Editora\app\Models;

use Illuminate\Support\Facades\DB;

class Model
{
    private $db;

	public function __construct()
    {
        $this->db = DB::connection('mysql');
	}

	protected function get_data($sql,  array $params = [])
    {
        $rows = $this->db->select($sql, $params);

        if (!isset($rows)) {
            return false;
        }

        return json_decode(json_encode($rows), true);
    }

    protected function get_one($sql, array $params = [])
    {
        $row = $this->db->selectOne($sql, $params);

        if (!isset($row)) {
			return false;
		}

		return json_decode(json_encode($row), true);
	}

    protected function insert_one($sql, array $params = [])
    {
		$row = $this->db->insert($sql, $params);

		if (!$row) {
			return false;
		}
		else {
			$id = DB::getPdo()->lastInsertId();
			if ($id) return $id;
			else return false;
		}  
	}

    protected function update_one($sql) {

        $ret = $this->db->update($sql);

        if (!$ret){
		    return false;
        }else {
		    return true;
        }
	}

    protected function delete($sql, array $params = [])
    {
        return $this->db->delete($sql, $params);
    }

    protected function execute ($sql, array $params = []) {
		$res = $this->db->select($sql, $params);
	}

    protected function escape ($string) {
		return $string;
	}
}
