<?php

class Persona extends CI_Model
{
	public $table = "personas";
	public $table_id = "p_id";

	public function __construct()
	{
	}

	public function findAll(){
        $query = $this->db->query("CALL SP_UsuarioListarTodos()");
        return $query->result();
	}
	
	public function findByUsernameAndPass($username, $password){
		$this->db->select();
		$this->db->from($this->table);
		$this->db->where("p_usuario",$username);
		$this->db->where("p_pass",$password);
		$query = $this->db->get();
		if($query->num_rows() > 0){
			return $query->row();
		}else{
			return False;
		}
	}
	
	public function findByEmail($email){
		$this->db->select();
		$this->db->from($this->table);
		$this->db->where("p_correo",$email);
		$query = $this->db->get();
		if($query->num_rows() > 0){
			return $query->row();
		}else{
			return False;
		}
	}

	public function findById($p_id){
		$this->db->select();
		$this->db->from($this->table);
		$this->db->where($this->table_id,$p_id);
		$query = $this->db->get();
		if($query->num_rows() > 0){
			return $query->row();
		}else{
			return False;
		}	
	}

	public function insert($data){
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function updatePass($p_id, $data){
		$this->db->where($this->table_id, $p_id);
		if($this->db->update($this->table, $data)){
			return True;
		}else{
			return False;
		}
	}

	public function update($p_id, $data){
		$this->db->where($this->table_id, $p_id);
		if($this->db->update($this->table, $data)){
			return True;
		}else{
			return False;
		}
	}
	
	public function activarDesactivarCuenta($p_id, $estado){
		$this->db->where($this->table_id, $p_id);
		if($this->db->update($this->table, array("p_estado"=>$estado))){
			return True;
		}else{
			return False;
		}
	}

	public function generateRandomPass() {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%&=?¿)(';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < 10; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	public function eliminar($id_user){
		$this->db->where($this->table_id, $id_user);
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
