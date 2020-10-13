<?php

class User_token extends CI_Model
{
	public $table = "user_tokens";
	public $table_id = "id";

	public function __construct()
	{
	}
	
	public function generateToken($p_id){
        $static_str='TK';
        $currenttimeseconds = date("dmY_His");
		$token_id=$static_str.$p_id.$currenttimeseconds;
		$date_now = date("Y-m-d H:i:s");
		$end_date = date("Y-m-d", strtotime($date_now.' + 3 days'));
		$token_id = md5($token_id);
        $data = array(
			'token' => $token_id,
			'created_date' => date("Y-m-d H:i:s"),
			'end_date' => $end_date.' '.date('H:i:s'),
			'p_id' => $p_id,
		);
		$this->db->insert($this->table, $data);
        return $token_id;
	}

	public function deleteTokens($p_id){
		$this->db->where('p_id', $p_id);
		$this->db->delete($this->table);
	}
	 
	public function checkToken($token){
		$this->db->select();
		$this->db->from($this->table);
		$this->db->where("token",$token);
		$query = $this->db->get();
		if($query->num_rows() > 0){
			$data = $query->row();
			$date_now = date("Y-m-d H:i:s");
			if(date($data->end_date) < $date_now){
				$this->deleteTokens($data->p_id);
				return $this->generateToken($data->p_id);
			}else{
				return $data->token;
			}			
		}else{
			return False;
		}
	}
}
