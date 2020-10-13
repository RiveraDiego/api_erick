<?php

class Suscripcion extends CI_Model
{
	public $table = "suscripciones";
	public $table_id = "sc_id";

	public function __construct()
	{
	}

	public function desuscribir($id){
		$this->db->where($this->table_id, $id);
		if($this->db->update($this->table, array("sc_estado"=>"D"))){
			return True;
		}else{
			return False;
		}
	}

	public function crear($data){
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function findById($id){
		$this->db->select();
		$this->db->from($this->table);
		$this->db->where($this->table_id,$id);
		$query = $this->db->get();
		if($query->num_rows() > 0){
			return $query->row();
		}else{
			return False;
		}
	}
}
