<?php

class Evento extends CI_Model
{
	public $table = "eventos";
	public $table_id = "ev_id";

	public function __construct()
	{
	}

	public function findAll(){
        $query = $this->db->query("CALL SP_EventoListarTodos()");
        return $query->result();
	}

	public function findByUserId($id_user){
		$this->db->select();
		$this->db->from($this->table);
		$this->db->where('p_id',$id_user);
		$query = $this->db->get();
		if($query->num_rows() > 0){
			return $query->result();
		}else{
			return False;
		}
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

	public function update($id_evento, $data){
		$this->db->where($this->table_id, $id_evento);
		if($this->db->update($this->table, $data)){
			return True;
		}else{
			return False;
		}
	}

	public function cambiarEstado($id, $estado){
		$this->db->where($this->table_id, $id);
		if($this->db->update($this->table, array("ev_Estado"=> "{$estado}"))){
			return True;
		}else{
			return False;
		}
	}

	public function crear($data){
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function eliminar($id){
		$this->db->where($this->table_id, $id);
		if($this->db->delete($this->table)){
			return True;
		}else{
			return False;
		}
	}

	/*
    public function filtrar($alm_id){
    	$this->db->select('alm.alm_nombre, grd.grd_nombre, GROUP_CONCAT(concat(mat.mat_nombre) SEPARATOR ", ") as "materias" from mxg_materiasxgrado mxg JOIN mat_materia mat ON mxg.mxg_id_mat=mat.mat_id JOIN grd_grado grd ON mxg.mxg_id_grd=grd.grd_id JOIN alm_alumno alm ON alm.alm_id_grd=grd.grd_id WHERE alm.alm_id='.$alm_id.' GROUP BY alm.alm_id');
    	$query = $this->db->get();
    	return $query->row();
    }*/
}
