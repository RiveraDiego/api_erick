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

	public function personas_get($id_user){
		$token = $this->input->get_request_header('token', TRUE);
		$user_token = $this->User_token->checkToken($token);
		if(!$user_token){
			return $this->response(array(
				"status"=>"No autorizado"
			),REST_Controller::HTTP_FORBIDDEN);
		}

		$persona = $this->Persona->findById($id_user);
		if($persona){
			$response = array(
				"status"=>"success",
				"token"=>$user_token,
				"data"=>$persona
			);
		}else{
			$response = array(
				"status"=>"error",
				"token"=>$user_token
			);
		}

		return $this->response($response);
	}

	public function activar_cuenta_get($id_user){
		$token = $this->input->get_request_header('token', TRUE);
		$user_token = $this->User_token->checkToken($token);
		if(!$user_token){
			return $this->response(array(
				"status"=>"No autorizado"
			),REST_Controller::HTTP_FORBIDDEN);
		}

		$persona = $this->Persona->activarDesactivarCuenta($id_user,"A");

		if($persona){
			$response = array(
				"status"=>"success",
				"token"=>$user_token,
				"data"=>"Cuenta activada exitosamente"
			);
		}else{
			$response = array(
				"status"=>"error",
				"token"=>$user_token,
				"data"=>"No se pudo activar la cuenta"
			);
		}

		return $this->response($response);
	}

	public function desactivar_cuenta_get($id_user){
		$token = $this->input->get_request_header('token', TRUE);
		$user_token = $this->User_token->checkToken($token);
		if(!$user_token){
			return $this->response(array(
				"status"=>"No autorizado"
			),REST_Controller::HTTP_FORBIDDEN);
		}

		$persona = $this->Persona->activarDesactivarCuenta($id_user,"I");

		if($persona){
			$response = array(
				"status"=>"success",
				"token"=>$user_token,
				"data"=>"Cuenta desactivada exitosamente"
			);
		}else{
			$response = array(
				"status"=>"error",
				"token"=>$user_token,
				"data"=>"No se pudo desactivar la cuenta"
			);
		}

		return $this->response($response);
	}

	public function cuenta_pendiente_get($id_user){
		$token = $this->input->get_request_header('token', TRUE);
		$user_token = $this->User_token->checkToken($token);
		if(!$user_token){
			return $this->response(array(
				"status"=>"No autorizado"
			),REST_Controller::HTTP_FORBIDDEN);
		}

		$persona = $this->Persona->activarDesactivarCuenta($id_user,"P");

		if($persona){
			$response = array(
				"status"=>"success",
				"token"=>$user_token,
				"data"=>"Cuenta en estado pendiente de activar"
			);
		}else{
			$response = array(
				"status"=>"error",
				"token"=>$user_token,
				"data"=>"No se pudo cambiar el estado de la cuenta"
			);
		}

		return $this->response($response);
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

		$data = array(
			"p_nombre"=>$nombre,
			"p_apellido"=>$apellido,
			"p_fecha_nac"=>$fecha_nac,
			"p_genero" => $genero,
			"p_correo" => $correo,
			"p_telefono" => $telefono,
			"p_usuario" => $usuario,
			"p_pass" => md5(sha1($password))
		);
		
		$persona_id = $this->Persona->insert($data);
			
		$persona = $this->Persona->findById($persona_id);

		if($this->sendNewEmail($persona->p_correo, "Registro Exitoso", "Su cuenta ha sido creada con exito")){
			$email_status = "Enviado";
		}else{
			$email_status = "No enviado";
		}

		$token = $this->User_token->generateToken($persona->p_id);

		$response = array(
			"status"=>"Nuevo usuario registrado correctamente",
			"email_status"=>$email_status,
			"token"=>$token,
			"data"=>$persona
		);

		return $this->response($response);
	}

	public function sendNewEmail($email, $subject = "API_ERICK", $message = "Este es un correo de prueba"){
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

		$this->email->subject($subject);
		$this->email->message($message);
		if( $this->email->send()){
			return True;
		}else{
			return False;
		}
	}

	public function login_post(){
		if (!$this->post("username") or !$this->post("password")){
			return $this->response(["status"=>"Username or pasword not found"]);
		}
		$username = $this->post("username");
		$password = md5(sha1($this->post("password")));
		$persona = $this->Persona->findByUsernameAndPass($username, $password);
		if($persona){
			$user_tokens = $this->User_token->deleteTokens($persona->p_id);
			$token = $this->User_token->generateToken($persona->p_id);
			$response = array(
				"status"=>"success",
				"token"=>$token,
				"data"=>$persona
			);
		}else{
			$response = array(
				"status"=>"Usuario o contraseña incorrectos",
				"data"=>array()
			);
		}

		return $this->response($response);
	}

	public function olvido_contrasena_post(){
		if(!$this->post("email")){
			return $this->response(["status"=>"El correo electronico es requerido"]);
		}

		$correo = $this->post("email");
		$persona = $this->Persona->findByEmail($correo);

		$newPass = $this->Persona->generateRandomPass();

		$data = array(
			"p_pass"=>md5(sha1($newPass))
		);

		if($this->sendNewEmail($persona->p_correo, "Cambio contraseña", "Su nueva contraseña es: ".$newPass)){
			$email_status = "Enviado";
			$persona_update = $this->Persona->updatePass($persona->p_id, $data);
		}else{
			$email_status = "No enviado";
		}

		if($persona_update){
			$response = array(
				"message" => "success",
				"email_status"=>$email_status
			);
		}else{
			$response = array(
				"message" => "error",
				"email_status"=>$email_status
			);
		}

		$this->response($response);
	}

	public function listar_eventos_get(){
		$token = $this->input->get_request_header('token', TRUE);
		$user_token = $this->User_token->checkToken($token);
		if(!$user_token){
			return $this->response(array(
				"status"=>"No autorizado"
			),REST_Controller::HTTP_FORBIDDEN);
		}

		$eventos = $this->Evento->findAll();

		$response = array(
			"status"=> "success",
			"token"=>$user_token,
			"data"=>$eventos
		);
		return $this->response($response);
	}

	public function evento_get($id_evento){
		$token = $this->input->get_request_header('token', TRUE);
		$user_token = $this->User_token->checkToken($token);
		if(!$user_token){
			return $this->response(array(
				"status"=>"No autorizado"
			),REST_Controller::HTTP_FORBIDDEN);
		}

		$evento = $this->Evento->findById($id_evento);

		if($evento){
			$persona = $this->Persona->findById($evento->p_id);
		}else{
			$response = array(
				"status"=>"error",
				"token"=>$token,
				"data"=>null
			);

			return $this->response($response,REST_Controller::HTTP_NOT_FOUND);
		}

		if($persona){
			unset($evento->p_id);
		}else{
			$persona = array();
		}
		
		$evento->persona = $persona;

		$response = array(
			"status"=>"success",
			"token"=>$user_token,
			"data"=>$evento
		);

		return $this->response($response,REST_Controller::HTTP_OK);
	}

	public function editar_evento_put($id_evento){
		$token = $this->input->get_request_header('token', TRUE);
		$user_token = $this->User_token->checkToken($token);
		if(!$user_token){
			return $this->response(array(
				"status"=>"No autorizado"
			),REST_Controller::HTTP_FORBIDDEN);
		}

		$error_code = "REST_Controller::HTTP_OK";

		$data = $this->put();

		$evento_update = $this->Evento->update($id_evento, $data);
		
		if($evento_update){
			$evento = $this->Evento->findById($id_evento);
			$persona = array();
			if($evento){
				$persona = $this->Persona->findById($evento->p_id);
				if($persona){
					unset($evento->p_id);
				}
			}else{
				$response = array(
					"status"=>"error",
					"token"=>$user_token,
				);
				return $this->response($response);
			}
			$evento->persona = $persona;
			$response = array(
				"status"=>"success",
				"token"=>$user_token,
				"data"=>$evento
			);
		}else{
			$response = array(
				"status"=>"error",
				"token"=>$user_token
			);
			$error_code = "REST_Controller::HTTP_404";
		}

		return $this->response($response,$error_code);
	}

	public function activar_evento_get($id){
		$token = $this->input->get_request_header('token', TRUE);
		$user_token = $this->User_token->checkToken($token);
		if(!$user_token){
			return $this->response(array(
				"status"=>"No autorizado"
			),REST_Controller::HTTP_FORBIDDEN);
		}

		$evento_activado = $this->Evento->cambiarEstado($id, "A");

		if($evento_activado){
			$response = array(
				"status"=>"success",
				"token"=>$user_token,
				"data"=>True
			);
			return $this->response($response,REST_Controller::HTTP_OK);
		}else{
			$response = array(
				"status"=>"error",
				"token"=>$user_token,
				"data"=>False
			);
			return $this->response($response,REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function cancelar_evento_get($id){
		$token = $this->input->get_request_header('token', TRUE);
		$user_token = $this->User_token->checkToken($token);
		if(!$user_token){
			return $this->response(array(
				"status"=>"No autorizado"
			),REST_Controller::HTTP_FORBIDDEN);
		}

		$evento_activado = $this->Evento->cambiarEstado($id, "C");

		if($evento_activado){
			$response = array(
				"status"=>"success",
				"token"=>$user_token,
				"data"=>True
			);
			return $this->response($response,REST_Controller::HTTP_OK);
		}else{
			$response = array(
				"status"=>"error",
				"token"=>$user_token,
				"data"=>False
			);
			return $this->response($response,REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function realizar_evento_get($id){
		$token = $this->input->get_request_header('token', TRUE);
		$user_token = $this->User_token->checkToken($token);
		if(!$user_token){
			return $this->response(array(
				"status"=>"No autorizado"
			),REST_Controller::HTTP_FORBIDDEN);
		}

		$evento_activado = $this->Evento->cambiarEstado($id, "R");

		if($evento_activado){
			$response = array(
				"status"=>"success",
				"token"=>$user_token,
				"data"=>True
			);
			return $this->response($response,REST_Controller::HTTP_OK);
		}else{
			$response = array(
				"status"=>"error",
				"token"=>$user_token,
				"data"=>False
			);
			return $this->response($response,REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function crear_evento_post(){
		$token = $this->input->get_request_header('token', TRUE);
		$user_token = $this->User_token->checkToken($token);
		if(!$user_token){
			return $this->response(array(
				"status"=>"No autorizado"
			),REST_Controller::HTTP_FORBIDDEN);
		}

		//ev_nombreEvento,ev_fechaEvento,ev_lugarEvento,ev_HoraInicio,ev_HoraFin,ev_descripcion,p_id
		$data = $this->post();
		$evento = $this->Evento->crear($data);

		if($evento){
			$evento_info = $this->Evento->findById($evento);
			$persona = $this->Persona->findById($evento_info->p_id);
			if($persona){
				unset($evento_info->p_id);
			}
			$evento_info->persona = $persona;
			$response = array(
				"status"=>"success",
				"token"=>$user_token,
				"data"=>$evento_info
			);
		}else{
			$response = array(
				"status"=>"error",
				"token"=>$user_token
			);
		}

		return $this->response($response);
	}

	public function evento_desuscribir_get($id_sus){
		$token = $this->input->get_request_header('token', TRUE);
		$user_token = $this->User_token->checkToken($token);
		if(!$user_token){
			return $this->response(array(
				"status"=>"No autorizado"
			),REST_Controller::HTTP_FORBIDDEN);
		}

		$suscripcion = $this->Suscripcion->desuscribir($id_sus);

		if($suscripcion){
			$response = array(
				"status"=>"success",
				"token"=>$user_token,
				"data"=>True
			);
		}else{
			$response = array(
				"status"=>"error",
				"token"=>$user_token,
				"data"=>False
			);
		}

		return $this->response($response);
	}

	public function eliminar_evento_delete($id){
		$token = $this->input->get_request_header('token', TRUE);
		$user_token = $this->User_token->checkToken($token);
		if(!$user_token){
			return $this->response(array(
				"status"=>"No autorizado"
			),REST_Controller::HTTP_FORBIDDEN);
		}

		$evento = $this->Evento->findById($id);

		if($evento){
			if($this->Evento->eliminar($id)){
				$response = array(
					"status"=>"success",
					"token"=>$user_token,
					"data"=>True
				);
			}else{
				$response = array(
					"status"=>"error",
					"token"=>$user_token,
					"data"=>False
				);
			}
		}else{
			$response = array(
				"status"=>"error",
				"token"=>$user_token,
				"data"=>False
			);
		}

		return $this->response($response);
	}

	public function eventos_usuario_get($id_user){
		$token = $this->input->get_request_header('token', TRUE);
		$user_token = $this->User_token->checkToken($token);
		if(!$user_token){
			return $this->response(array(
				"status"=>"No autorizado"
			),REST_Controller::HTTP_FORBIDDEN);
		}

		$persona = $this->Persona->findById($id_user);

		if($persona){
			$eventos = $this->Evento->findByUserId($id_user);
			if($eventos){
				$response = array(
					"status"=>"success",
					"token"=>$user_token,
					"data"=>array(
						"persona"=>$persona,
						"eventos"=>$eventos
					)
				);
			}else{
				$response = array(
					"status"=>"error",
					"token"=>$user_token
				);
			}
		}else{
			$response = array(
				"status"=>"error",
				"token"=>$user_token
			);
		}

		

		return $this->response($response);
	}

	public function suscribir_evento_post(){
		$token = $this->input->get_request_header('token', TRUE);
		$user_token = $this->User_token->checkToken($token);
		if(!$user_token){
			return $this->response(array(
				"status"=>"No autorizado"
			),REST_Controller::HTTP_FORBIDDEN);
		}
		
		$data = array(
			"p_id"=>$this->post("p_id"),
			"ev_id"=>$this->post("ev_id")
		);

		if($new_sus = $this->Suscripcion->crear($data)){
			$suscripcion = $this->Suscripcion->findById($new_sus);
			$persona = $this->Persona->findById($suscripcion->p_id);
			$evento = $this->Evento->findById($suscripcion->ev_id);

			unset($suscripcion->p_id);
			unset($suscripcion->ev_id);
			$suscripcion->persona = $persona;
			$suscripcion->evento = $evento;
			$response = array(
				"status"=>"success",
				"token"=>$user_token,
				"data"=>$suscripcion
			);
		}else{
			$response = array(
				"status"=>"error",
				"token"=>$user_token
			);
		}

		return $this->response($response);
	}

	public function editar_usuario_put(){
		$token = $this->input->get_request_header('token', TRUE);
		$user_token = $this->User_token->checkToken($token);
		if(!$user_token){
			return $this->response(array(
				"status"=>"No autorizado"
			),REST_Controller::HTTP_FORBIDDEN);
		}

		//p_nombre, p_apellido, p_fecha_nac, p_genero, p_correo, p_telefono, p_pass
		$p_id = $this->put("p_id");
		$p_nombre = $this->put("p_nombre");
		$p_apellido = $this->put("p_apellido");
		$p_fecha_nac = DateTime::createFromFormat("d-m-Y", $this->put("p_fecha_nac"))->format("Y-m-d");
		$p_genero = $this->put("p_genero");
		$p_correo = $this->put("p_correo");
		$p_telefono = $this->put("p_telefono");
		$p_pass = md5(sha1($this->put("p_pass")));
		
		$data = array(
			"p_nombre"=>$p_nombre,
			"p_apellido"=>$p_apellido,
			"p_fecha_nac"=>$p_fecha_nac,
			"p_genero"=>$p_genero,
			"p_correo"=>$p_correo,
			"p_telefono"=>$p_telefono,
			"p_pass"=>$p_pass
		);

		$persona_update = $this->Persona->update($p_id, $data);

		if($persona_update){
			$persona = $this->Persona->findById($p_id);
			$response = array(
				"message" => "success",
				"token" => $user_token,
				"data" => $persona
			);
		}else{
			$response = array(
				"message" => "error",
				"token" => $user_token
			);
		}

		return $this->response($response);

	}

	public function eliminar_usuario_delete($id_user){
		$token = $this->input->get_request_header('token', TRUE);
		$user_token = $this->User_token->checkToken($token);
		if(!$user_token){
			return $this->response(array(
				"status"=>"No autorizado"
			),REST_Controller::HTTP_FORBIDDEN);
		}

		$persona = $this->Persona->eliminar($id_user);

		if($persona){
			$response = array(
				"status"=>"success",
				"token"=>$user_token
			);
		}else{
			$response = array(
				"status"=>"error",
				"token"=>$user_token
			);
		}

		return $this->response($response);
	}
}
