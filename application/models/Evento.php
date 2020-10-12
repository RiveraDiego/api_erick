<?php

class Evento extends CI_Model
{
	public $table = "eventos";
	public $table_id = "ev_id";

	public function __construct()
	{
	}

	public function findAll(){
        $this->db->select();
        $this->db->from($this->table);
        $query = $this->db->get();
        return $query->result();
    }

	/*
    public function filtrar($alm_id){
    	$this->db->select('alm.alm_nombre, grd.grd_nombre, GROUP_CONCAT(concat(mat.mat_nombre) SEPARATOR ", ") as "materias" from mxg_materiasxgrado mxg JOIN mat_materia mat ON mxg.mxg_id_mat=mat.mat_id JOIN grd_grado grd ON mxg.mxg_id_grd=grd.grd_id JOIN alm_alumno alm ON alm.alm_id_grd=grd.grd_id WHERE alm.alm_id='.$alm_id.' GROUP BY alm.alm_id');
    	$query = $this->db->get();
    	return $query->row();
    }*/
}
