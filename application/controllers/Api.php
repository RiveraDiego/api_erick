<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\Libraries\RestServer;
use chriskacerguis\RestServer\RestController;

require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/Format.php';

class Api extends REST_Controller{

	function __construct(){
		// Construct the parent class
		parent::__construct();
		$this->load->database();
		$this->load->helper('url');
		$this->load->library('session');
		$this->load->model('Persona');
		$this->load->model('Evento');
		$this->load->model('Suscripcion');
		$this->load->model('User_token');
		header('Content-Type: application/json');
		header('Accept: application/json');
		
	}

	public function personas_get(){
		$this->response($this->Persona->findAll());
	}

	public function personas_post(){
		//$input_data = json_decode(trim(file_get_contents('php://input')), true);
		$id = $this->post("id");
		$this->response(["id"=>$id]);
	}

	
	public function registro_post(){
		$nombre = $this->post("nombre");
		// Apellido es opcional
		if($this->post("apellido")){
			$apellido = $this->post("apellido");
		}else{
			$apellido = "";
		}
		//Convertir la fecha al formato aceptado por mysql
		$fecha_nac = DateTime::createFromFormat("d-m-Y", $this->post("fecha_nacimiento"))->format("Y-m-d");
		$genero = $this->post("genero");
		// Telefono es opcional
		if($this->post("telefono")){
			$telefono = $this->post("telefono");
		}else{
			$telefono = "";
		}
		// Correo es opcional
		if($this->post("correo")){
			$correo = $this->post("correo");
		}else{
			$correo = "";
		}
		$usuario = $this->post("username");
		$password = $this->post("password");
		$fecha_creacion = date("Y-m-d H:i:s");
		$fecha_modificacion = date("Y-m-d H:i:s");
		$estado = "P";

		$data = array(
			"p_nombre"=>$nombre,
			"p_apellido"=>$apellido,
			"p_fecha_nac"=>$fecha_nac,
			"p_genero" => $genero,
			"p_correo" => $correo,
			"p_telefono" => $telefono,
			"p_usuario" => $usuario,
			"p_pass" => md5(sha1($password)),
			"p_fechaCreacion" => $fecha_creacion,
			"p_fechaModificacion" => $fecha_modificacion,
			"p_estado" => $estado
		);

		$persona_id = $this->Persona->insert($data);
		
		$persona = $this->Persona->findById($persona_id);

		if($this->sendNewEmail($persona->p_correo)){
			$email_status = "Enviado";
		}else{
			$email_status = "No enviado";
		}

		$response = array(
			"message"=>"Nuevo usuario registrado correctamente",
			"email_status"=>$email_status,
			"data"=>$persona
		);

		return $this->response($response);
	}

	public function sendNewEmail($email){
		/**
		 * Preguntar a erick si, la funcion enviar email sera un request por aparte, o si en el momento de crear 
		 * un nuevo usuario, se enviará automaticamente
		*/
		$this->load->library("email");
		$this->email->clear();

		$config['protocol'] = 'smtp';
		$config['smtp_host'] = 'smtp.sendgrid.net';
		$config['smtp_user'] = 'apikey';
		$config['smtp_pass'] = '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';
		$config['smtp_port'] = '587';
		$config['newline'] = '\n';

		$this->email->initialize($config);

		$this->email->from('die.menen@gmail.com',"Diego Menendez");
		$this->email->to($email);

		$this->email->subject("Test");
		$this->email->message("Este es solo un correo de prueba");
		if( $this->email->send()){
			return True;
		}else{
			return False;
		}
	}

	public function login_post(){
		if (!$this->post("username") or !$this->post("password")){
			return $this->response(["message"=>"Username or pasword not found"]);
		}
		$username = $this->post("username");
		$password = md5(sha1($this->post("password")));
		$persona = $this->Persona->findByUsernameAndPass($username, $password);
		if($persona){
			$response = array(
				"message"=>"success",
				"data"=>$persona
			);
		}else{
			$response = array(
				"message"=>"Usuario o contraseña incorrectos",
				"data"=>array()
			);
		}

		return $this->response($response);
	}

	public function olvido_contrasena_post(){
		if(!$this->post("email")){
			return $this->response(["message"=>"El correo electronico es requerido"]);
		}

		$correo = $this->post("email");
		$this->Persona->findByEmail($correo);
		$this->response($response);
	}

	public function confirmToken_get(){
		// Evaluar token
		
		$token = $this->input->get_request_header('token');
        $p_id = $this->User_token->checkToken($token);
       
        if($userId !=''){
            $sql = "SELECT * FROM users WHERE userId = ?";
            $results = $this->db->query($sql, array($userId));
            $loginData = $results->row();
           
            $dataupdate = array(
                   'updated'=> gmdate("Y-m-d H:i:s"),
                   'status'=>1
                   );
            $this->db->where('userId', $userId);
            $this->db->update('users', $dataupdate);
               
            $this->db->where('tktToken', $this->uri->segment(3));
            $this->db->where('tktReason', "register");
            $this->db->delete('tickets');
            $sess_array = array(
                                'userId' => $loginData->userId,
                                'email' => $loginData->email
                                );
               $this->session->set_userdata('logged_in', $sess_array);
               $this->session->set_flashdata('msg', 'You has been confirmed as member');
               redirect('users/index', 'refresh'); 
        }
        else{
            $this->session->set_flashdata('msg', 'Your request process not success, You entered with an incorrect code');
            redirect('users/index', 'refresh');
            }
       
    }
}
