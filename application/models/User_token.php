<?php

class User_token extends CI_Model
{
	public $table = "user_tokens";
	public $table_id = "sc_idid";

	public function __construct()
	{
	}

	public function findAll(){
        $this->db->select();
        $this->db->from($this->table);
        $query = $this->db->get();
        return $query->result();
	}
	
	public function generateToken($p_id){
        $static_str='TK';
        $currenttimeseconds = date("dmY_His");
        $token_id=$static_str.$p_id.$currenttimeseconds;
        $data = array(
			'token' => md5($token_id),
			'created_date' => date("Y-m-d H:i:s"),
			'p_id' => $p_id,
		);
		$this->db->insert($this->table, $data);
        return md5($token_id);
	}
	 
	/*
    public function filtrar($alm_id){
    	$this->db->select('alm.alm_nombre, grd.grd_nombre, GROUP_CONCAT(concat(mat.mat_nombre) SEPARATOR ", ") as "materias" from mxg_materiasxgrado mxg JOIN mat_materia mat ON mxg.mxg_id_mat=mat.mat_id JOIN grd_grado grd ON mxg.mxg_id_grd=grd.grd_id JOIN alm_alumno alm ON alm.alm_id_grd=grd.grd_id WHERE alm.alm_id='.$alm_id.' GROUP BY alm.alm_id');
    	$query = $this->db->get();
    	return $query->row();
    }*/
}