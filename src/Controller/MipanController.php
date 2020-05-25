<?php
// src/Controller/MipanController.php

namespace App\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Model\Model;
use App\Config\Config;
use Unirest\Request;



class MipanController extends AbstractController
{

	// ERRORES
	public function error404NotFound()
	{
		$params = array(

			'error' => ''		
		);

		return $this->render('mipan/exceptions/error404NotFound.html.twig', $params);
	}


	// ---------


	public function inicio()
	{
		$params = array(

			'error' => ''		
		);

		$sesion = $this->container->get('session');

		$sesion->set('Conectado', null);
		$sesion->set('Panaderia', null);

		$sesion->invalidate();

		// $reply= confirm("¿Seguro que desea salir?");
		// if ($reply==true){
		// 	echo "asd";
		// }else{
		// 	echo "123";
		// }

		return $this->render('mipan/inicio.html.twig', $params);
	}

	public function inicioAdmin()
	{
		$params = array(

			'error' => ''	
		);

		return $this->render('mipan/vistasAdmin/menuAdmin.html.twig', $params);
	}

	public function inicioJefes()
	{
		$params = array(

			'error' => ''	
		);

		return $this->render('mipan/vistasJefe/menuJefe.html.twig', $params);
	}



	public function registroNuevaPanaderia()
	{
		$params = array(

			'error' => ''	
		);

		return $this->render('mipan/vistasJefe/panaderia/registroNuevaPanaderia.html.twig', $params);
	}

	public function confirmarRegistroNuevaPanaderia()
	{

		$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario, Config::$mvc_bd_clave, Config::$mvc_bd_hostname);
		$m2 = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario, Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

		$error = '';

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {


			$nombreP = $_POST['nombreP'];
			$logoP = $_POST['logoP'];
			$telefonoP = $_POST['telefonoP'];

			$direccionP = $_POST['direccionP'];
			$ubicacionP = $_POST['ubicacionP'];

			$horarioP = $_POST['horarioP'];

			$codigoPostalP = $_POST['codigoPostalP'];

			$usuActivo = $_POST['usuActivo'];



            //el md5 produce un error al comparar los datos de la BD
            // $usuario = $m->buscarUsuario($nombre, md5($pass));

            //Busqueda de datos únicos
			$panaderia = $m->buscarNuevaPanaderia($nombreP,$telefonoP,$direccionP,$ubicacionP);

		//Usuario Encontrado/Para Tutores
			if(count($panaderia) == 0){

				$m->registroPanaderia($nombreP, $logoP, $telefonoP, $direccionP, $ubicacionP, $horarioP, $codigoPostalP);

				$m2->actualizaCampoPanaderiaDeJefe($nombreP, $usuActivo);

			} else {
				$error = 'Ya está registrada.';
			}
		}

		$params = array('nombreP' => '', 'error' => $error);

		return
		$this->render('mipan/vistasJefe/menuJefe.html.twig', $params);

	}

	public function inicioSesion()
	{
		$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario, Config::$mvc_bd_clave, Config::$mvc_bd_hostname);
		$error = '';

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {

			$usuarioI = $_POST['usuarioI'];
			$passI = $_POST['passI'];


			//Admin
			$usuarioA = null;

			//Jefe
			$usuarioJ = null;
			//Trabajador
			$usuarioT = null;
			//Cliente
			$usuarioC = null;
            //el md5 produce un error al comparar los datos de la BD
            // $usuario = $m->buscarUsuario($nombre, md5($pass));

			$usuarioA = $m->buscarAdmin($usuarioI, $passI);

			$usuarioJ = $m->buscarJefe($usuarioI, $passI);
			$usuarioT = $m->buscarTrabajador($usuarioI, $passI);
			$usuarioC = $m->buscarCliente($usuarioI, $passI);

			$trabajadorRepartidor = $m->buscarTrabajadorRepartidor($usuarioI, $passI);
			$trabajadorDependiente = $m->buscarTrabajadorDependiente($usuarioI, $passI);

		//Usuario Encontrado
			if(count($usuarioJ)>0){
				$session = new \Symfony\Component\HttpFoundation\Session\Session();
				$session->set('Conectado', $usuarioI);
			//session_start();

				//ahora se verifica a que pagina redirigir
				$jefeSinPanaderiaRegistrada = $m->verificarPanaderiaJefe($usuarioI);

				if(count($jefeSinPanaderiaRegistrada)>0){

					$response = $this->forward("App\Controller\MipanController:registroNuevaPanaderia");

				}else{

					$panaderiaJefe = $m->sesionPanaderiaJefe($usuarioI);
					// Comprobar que es la que imprime
					// echo $panaderiaJefe;
					$panaderiaJefe = implode("", $panaderiaJefe);

					$session->set('Panaderia', $panaderiaJefe);

					$response = $this->forward("App\Controller\MipanController:inicioJefes");

				}

				return $response;

			} else if(count($usuarioT)>0) {


				if(count($trabajadorRepartidor)>0) {

					$session = new \Symfony\Component\HttpFoundation\Session\Session();
					$session->set('Conectado', $usuarioI);
			//session_start();

				//ahora se verifica a que pagina redirigir

					$panaderiaTrabajador = $m->sesionPanaderiaTrabajador($usuarioI);
					// Comprobar que es la que imprime

					$panaderiaTrabajador = implode("", $panaderiaTrabajador);

					$session->set('Panaderia', $panaderiaTrabajador);

					$response = $this->forward("App\Controller\MipanController:inicioTrabajadoresRepartidor");

					return $response;

				} else if (count($trabajadorDependiente)>0) {

					$session = new \Symfony\Component\HttpFoundation\Session\Session();
					$session->set('Conectado', $usuarioI);
			//session_start();

				//ahora se verifica a que pagina redirigir

					$panaderiaTrabajador = $m->sesionPanaderiaTrabajador($usuarioI);
					// Comprobar que es la que imprime

					$panaderiaTrabajador = implode("", $panaderiaTrabajador);

					$session->set('Panaderia', $panaderiaTrabajador);

					$response = $this->forward("App\Controller\MipanController:inicioTrabajadoresDependiente");

					return $response;

				}else {


					$session = new \Symfony\Component\HttpFoundation\Session\Session();
					$session->set('Conectado', $usuarioI);
			//session_start();

				//ahora se verifica a que pagina redirigir

					$panaderiaTrabajador = $m->sesionPanaderiaTrabajador($usuarioI);
					// Comprobar que es la que imprime
					// echo $panaderiaTrabajador;
					$panaderiaTrabajador = implode("", $panaderiaTrabajador);

					$session->set('Panaderia', $panaderiaTrabajador);

					$response = $this->forward("App\Controller\MipanController:inicioTrabajador");

					return $response;
				}

			}else if(count($usuarioC)>0) {

				$session = new \Symfony\Component\HttpFoundation\Session\Session();
				$session->set('Conectado', $usuarioI);
			//session_start();

				//ahora se verifica a que pagina redirigir

				$response = $this->forward("App\Controller\MipanController:inicioClientes");

				return $response;

			} else if (count($usuarioA)>0) {

				$session = new \Symfony\Component\HttpFoundation\Session\Session();
				$session->set('Conectado', $usuarioI);
			//session_start();


				$response = $this->forward("App\Controller\MipanController:inicioAdmin");

				return $response;
				
			}else{
				$error = 'El usuario o contraseña no son correctos.';
			}
			
		}

		$param = array('usuarioI' => '', 'passI' => '', 'error' => $error);

		return $this->render('mipan/inicio.html.twig', $param);
	}

	public function cerrarSesion() 
	{
		$sesion = $this->container->get('session');

		$sesion->set('Conectado', null);
		$sesion->set('Panaderia', null);

		$sesion->invalidate();
		$response = $this->forward("App\Controller\MipanController:inicio");

		return $response;
	}




	public function verTodasPanaderias()
	{

		$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
			Config::$mvc_bd_clave, Config::$mvc_bd_hostname);


		$params = array(
			'panaderias' => $m->verTodasPanaderias(),
			'resultado' => array(),
			'mensaje' => ''
		);


		return
		$this->render('mipan/vistasAdmin/panaderias/verTodasPanaderias.html.twig', $params);
	}

	public function editarPanaderiaAdmin(){


		$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
			Config::$mvc_bd_clave, Config::$mvc_bd_hostname);


		$params = array(
			'nombre' => '',
			'logo' => '',
			'telefono' => '',
			'direccion' => '',
			'ubicacion' => '',
			'horario' => '',
			'codigoPostal' => ''


		);

		$nombre = $_GET['nombre'];

		

		$params = array(
			'panaderia' => $m->verPanaderiaJefe($nombre),
			'error' => '',
			'mensaje' => ''

		);

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {


             // comprobar campos formulario
			if ($m->validarDatosPanaderia($_POST['nombre'], $_POST['logo'],
				$_POST['telefono'], $_POST['direccion'], $_POST['ubicacion'],
				$_POST['horario'], $_POST['codigoPostal'])) {


				if($m->editarPanaderia($_POST['nombre'], $_POST['logo'],
					$_POST['telefono'], $_POST['direccion'], $_POST['ubicacion'],
					$_POST['horario'] , $_POST['codigoPostal'])){

					$params['mensaje'] = 'Actualizado correctamente.';

			}else{
				$params['mensaje'] = 'No se ha podido actualizar los datos. Revisa los datos del formulario';
			}


		} else {

			$params['mensaje'] = 'Error al Validar Datos, tipo de datos erroneos.';
		}
	}

	return
	$this->render('mipan/vistasAdmin/panaderias/editarPanaderiaAdmin.html.twig', $params);

}


public function buscarPanaderiaPorNombre()
{

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);


	$params = array(
		'nombre' => '',
		'panaderias' => $m->verTodasPanaderias(),
		'resultado' => array(),
		'mensaje' => '',
	);


	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		$params['nombre'] = $_POST['nombre'];
		$params['resultado'] = $m->buscarPanaderiaPorNombre($_POST['nombre']);

		if(count($params['resultado']) == 0)
			$params['mensaje']='No se han encontrado panaderias con el nombre indicado';
	}

	return
	$this->render('mipan/vistasAdmin/panaderias/verTodasPanaderias.html.twig', $params);

}


public function buscarPanaderiaPorCodigoPostal()
{

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

	
	$params = array(
		'codigoPostal' => '',
		'panaderias' => $m->verTodasPanaderias(),
		'resultado' => array(),
		'mensaje' => '',
	);


	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		$params['codigoPostal'] = $_POST['codigoPostal'];
		$params['resultado'] = $m->buscarPanaderiaPorCodigoPostal($_POST['codigoPostal']);

		if(count($params['resultado']) == 0)
			$params['mensaje']='No se han encontrado panaderias con el código postal indicado';
	}

	return
	$this->render('mipan/vistasAdmin/panaderias/verTodasPanaderias.html.twig', $params);

}


// ---

public function verTodosUsuarios()
{

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);


	$params = array(
		'trabajadores' => $m->dameEmpleados(),
		'clientes' => $m->dameClientes(),
		'resultadoT' => array(),
		'resultadoC' => array(),
		'mensaje' => ''
	);


	return
	$this->render('mipan/vistasAdmin/usuarios/verTodosUsuarios.html.twig', $params);
}

public function editarTrabajadorAdmin(){


	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);


	$params = array(
		'panaderia' => '',

		'dni' => '',
		'nombre' => '',
		'apellidos' => '',
		'email' => '',
		'telefono' => '',
		'especialidad' => '',
		'usuario' => '',
		'pass' => '',

		'mensaje' => ''
		

	);

	$dni = $_GET['dni'];

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

	$params = array(
		'empleado' => $m->verEmpleado($dni),
		'error' => '',
		'mensaje' => ''
	);

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {


             // comprobar campos formulario
		if ($m->validarDatosEmpleado($_POST['panaderia'], $_POST['dni'], $_POST['nombre'],
			$_POST['apellidos'], $_POST['email'], $_POST['telefono'],
			$_POST['especialidad'], $_POST['usuario'], $_POST['pass'])) {


			if($m->editarEmpleado($_POST['panaderia'], $_POST['dni'], $_POST['nombre'],
				$_POST['apellidos'], $_POST['email'], $_POST['telefono'],
				$_POST['especialidad'], $_POST['usuario'], $_POST['pass'])){

				$params['mensaje'] = 'Editado correctamente.';

		}else{
			$params['mensaje'] = 'No se ha podido editar el Empleado. Revisa los datos del formulario';
		}


	} else {

		$params['mensaje'] = 'Error al Validar Datos, tipo de datos erroneos.';
	}
}

return
$this->render('mipan/vistasAdmin/usuarios/editarTrabajadorAdmin.html.twig', $params);

}


public function borrarEmpleadoAdmin()
{

	$dni = $_GET['dni'];

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

	$params = array(
		'empleado' => $m->verEmpleado($dni),
	);

	$m->borrarEmpleadoJefes($dni);

	return
	$this->render('mipan/vistasAdmin/empleados/borrarEmpleadoAdmin.html.twig' , $params);
	
}


public function editarClienteAdmin(){

	$params = array(
		'dni' => '',
		'nombre' => '',
		'apellidos' => '',
		'telefono' => '',
		'direccion' => '',
		'codigoPostal' => '',
		'pass' => ''


	);

	$usuario = $_GET['usuario'];

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

	$params = array(
		'perfil' => $m->verPerfilClientes($usuario),
		'error' => '',
		'mensaje' => ''

	);

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		if($m->editarPerfilClientes($_POST['dni'], $_POST['nombre'],
			$_POST['apellidos'], $_POST['telefono'], $_POST['codigoPostal'], $_POST['direccion'],
			$_POST['pass'])){

			$params['mensaje'] = 'Actualizado correctamente.';

	}else{
		$params['mensaje'] = 'No se ha podido actualizar los datos. Revisa los datos del formulario';
	}

}

return
$this->render('mipan/vistasAdmin/usuarios/editarClienteAdmin.html.twig', $params);

}

public function borrarClienteAdmin()
{

	$usu = $_GET['usu'];

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

	$params = array(
		'perfil' => $m->verPerfilClientes($usu),
	);

	$m->borrarClienteAdmin($usu);

	return
	$this->render('mipan/vistasAdmin/usuarios/borrarClienteAdmin.html.twig' , $params);
	
}


public function buscarTrabajadorPorPanaderia()
{

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);


	$params = array(
		'panaderia' => '',
		'trabajadores' => $m->dameEmpleados(),
		'clientes' => $m->dameClientes(),
		'resultadoT' => array(),
		'resultadoC' => array(),
		'mensaje' => ''
	);

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		$params['panaderia'] = $_POST['panaderia'];
		$params['resultadoT'] = $m->buscarTrabajadorPorPanaderia($_POST['panaderia']);

		if(count($params['resultadoT']) == 0)
			$params['mensaje']='No se han encontrado trabajadores con el nombre de panaderia indicado';
	}

	return
	$this->render('mipan/vistasAdmin/usuarios/verTodosUsuarios.html.twig', $params);

}

public function buscarTrabajadorPorEspecialidad()
{

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);


	$params = array(
		'especialidad' => '',
		'trabajadores' => $m->dameEmpleados(),
		'clientes' => $m->dameClientes(),
		'resultadoT' => array(),
		'resultadoC' => array(),
		'mensaje' => ''
	);

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		$params['especialidad'] = $_POST['especialidad'];
		$params['resultadoT'] = $m->buscarTrabajadorPorEspecialidad($_POST['especialidad']);

		if(count($params['resultadoT']) == 0)
			$params['mensaje']='No se han encontrado trabajadores con la especialidad indicada';
	}

	return
	$this->render('mipan/vistasAdmin/usuarios/verTodosUsuarios.html.twig', $params);

}
//-----

public function buscarClientePorCodigoPostal()
{

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);


	$params = array(
		'codigoPostal' => '',
		'trabajadores' => $m->dameEmpleados(),
		'clientes' => $m->dameClientes(),
		'resultadoT' => array(),
		'resultadoC' => array(),
		'mensaje' => ''
	);

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		$params['codigoPostal'] = $_POST['codigoPostal'];
		$params['resultadoC'] = $m->buscarClientePorCodigoPostal($_POST['codigoPostal']);

		if(count($params['resultadoC']) == 0)
			$params['mensaje']='No se han encontrado clientes con el C.P. indicado';
	}

	return
	$this->render('mipan/vistasAdmin/usuarios/verTodosUsuarios.html.twig', $params);

}

public function buscarClientePorUsuario()
{

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);


	$params = array(
		'usuario' => '',
		'trabajadores' => $m->dameEmpleados(),
		'clientes' => $m->dameClientes(),
		'resultadoT' => array(),
		'resultadoC' => array(),
		'mensaje' => ''
	);

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		$params['usuario'] = $_POST['usuario'];
		$params['resultadoC'] = $m->buscarClientePorUsuario($_POST['usuario']);

		if(count($params['resultadoC']) == 0)
			$params['mensaje']='No se han encontrado clientes con el usuario indicado';
	}

	return
	$this->render('mipan/vistasAdmin/usuarios/verTodosUsuarios.html.twig', $params);

}
















public function registroJefes()
{

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario, Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

	$error = '';

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		$dniN = $_POST['dniN'];
		$nombreN = $_POST['nombreN'];
		$apellidosN = $_POST['apellidosN'];
		$emailN = $_POST['emailN'];
		$telefonoN = $_POST['telefonoN'];
			//TIPO: JEFE
		$usuarioN = $_POST['usuarioN'];
		$passN = $_POST['passN'];
		$pass2N = $_POST['pass2N'];


            //el md5 produce un error al comparar los datos de la BD
            // $usuario = $m->buscarUsuario($nombre, md5($pass));

            //Busqueda de datos únicos
		$jefe = $m->buscarNuevoJefe($dniN,$emailN,$telefonoN,$usuarioN);

		//Usuario Encontrado/Para Tutores
		if(count($jefe) == 0){
			$m->registroJefe($dniN, $nombreN, $apellidosN, $emailN, $telefonoN, $usuarioN, $passN);
		} else {
			$error = 'Ya está registrado.';
		}
	}

	$params = array('emailN' => '', 'error' => $error);

	return
	$this->render('mipan/inicio.html.twig', $params);

}

public function verPerfilJefes()
{

	$usu = $_GET['usuario'];

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);


	$params = array(
		'perfil' => $m->verPerfilJefes($usu),
		'error' => ''
	);


	return
	$this->render('mipan/vistasJefe/perfil/verPerfilJefes.html.twig',
		$params);
}

public function editarPerfilJefes(){

	$params = array(
		'dni' => '',
		'nombre' => '',
		'apellidos' => '',
		'email' => '',
		'telefono' => '',
		'usuario' => '',
		'pass' => ''


	);

	$usuario = $_GET['usuario'];

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

	$params = array(
		'perfil' => $m->verPerfilJefes($usuario),
		'error' => '',
		'mensaje' => ''

	);

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {


             // comprobar campos formulario
		if ($m->validarDatosPerfil($_POST['dni'], $_POST['nombre'],
			$_POST['apellidos'], $_POST['email'], $_POST['telefono'],
			$_POST['pass'])) {


			if($m->editarPerfil($_POST['dni'], $_POST['nombre'],
				$_POST['apellidos'], $_POST['email'], $_POST['telefono'],
				$_POST['pass'])){

				$params['mensaje'] = 'Actualizado correctamente.';

		}else{
			$params['mensaje'] = 'No se ha podido actualizar los datos. Revisa los datos del formulario';
		}


	} else {

		$params['mensaje'] = 'Error al Validar Datos, tipo de datos erroneos.';
	}
}

return
$this->render('mipan/vistasJefe/perfil/editarPerfilJefes.html.twig', $params);

}

public function verPanaderia()
{

	$nombrePanaderia = $_GET['nombre'];

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);


	$params = array(
		'panaderia' => $m->verPanaderiaJefe($nombrePanaderia),
		'error' => ''
	);


	return
	$this->render('mipan/vistasJefe/panaderia/verPanaderiaJefe.html.twig', $params);
}

public function editarPanaderia(){

	$params = array(
		'nombre' => '',
		'logo' => '',
		'telefono' => '',
		'direccion' => '',
		'ubicacion' => '',
		'horario' => '',
		'codigoPostal' => ''


	);

	$nombre = $_GET['nombre'];

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

	$params = array(
		'panaderia' => $m->verPanaderiaJefe($nombre),
		'error' => '',
		'mensaje' => ''

	);

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {


             // comprobar campos formulario
		if ($m->validarDatosPanaderia($_POST['nombre'], $_POST['logo'],
			$_POST['telefono'], $_POST['direccion'], $_POST['ubicacion'],
			$_POST['horario'], $_POST['codigoPostal'])) {


			if($m->editarPanaderia($_POST['nombre'], $_POST['logo'],
				$_POST['telefono'], $_POST['direccion'], $_POST['ubicacion'],
				$_POST['horario'] , $_POST['codigoPostal'])){

				$params['mensaje'] = 'Actualizado correctamente.';

		}else{
			$params['mensaje'] = 'No se ha podido actualizar los datos. Revisa los datos del formulario';
		}


	} else {

		$params['mensaje'] = 'Error al Validar Datos, tipo de datos erroneos.';
	}
}

return
$this->render('mipan/vistasJefe/panaderia/editarPanaderiaJefe.html.twig', $params);

}




public function productosJefes()
{
	$params = array(
		'error' => ''	
	);

	return $this->render('mipan/vistasJefe/productos/menuJefeProductos.html.twig', $params);
}

public function listarProductos()
{
	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

	$params = array(
		'productos' => $m->dameProductos(),
		'error' => ''
	);

	return
	$this->render('mipan/vistasJefe/productos/menuJefeProductos.html.twig', $params);

}

public function verProducto()
{

	$id = $_GET['id'];

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);


	$params = array(
		'producto' => $m->verProducto($id),
		'error' => ''
	);


	return
	$this->render('mipan/vistasJefe/productos/verProductoJefe.html.twig',
		$params);
}

public function insertarProductos()
{

	$params = array(

		'panaderia' => '',
		'nombre' => '',

		'imagen' => '',
		'descripcion' => '',
		'precio' => '',
		'cantidad' => '',

		'mensaje' => ''

	);

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

         // comprobar campos formulario
		if ($m->validarDatosProducto($_POST['panaderia'], $_POST['nombre'],
			$_POST['imagen'], $_POST['descripcion'], $_POST['precio'],
			$_POST['cantidad'])) {

			if ($m->insertarProducto($_POST['panaderia'], $_POST['nombre'],
				$_POST['imagen'], $_POST['descripcion'], $_POST['precio'], $_POST['cantidad'])) {




				$response = $this->forward("App\Controller\MipanController:listarProductos");
			return $response;

		} else {


			$params = array(
				'panaderia' => $_POST['panaderia'],
				'nombre' => $_POST['nombre'],
				'imagen' => $_POST['imagen'],
				'descripcion' => $_POST['descripcion'],
				'precio' => $_POST['precio'],
				'cantidad' => $_POST['cantidad'],

				'mensaje' => 'Error al Insertar, datos erroneos.'
			);
		}

	}else{
		$params['mensaje'] = 'Error al Validar Datos, tipo de datos erroneos.';
	}
}

return
$this->render('mipan/vistasJefe/productos/insertarProductoJefe.html.twig', $params);

}


public function editarProducto(){

	$params = array(
		'id' => '',
		'panaderia' => '',
		'nombre' => '',
		'imagen' => '',
		'descripcion' => '',
		'precio' => '',
		'cantidad' => ''
		

	);

	$id = $_GET['id'];

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

	$params = array(
		'producto' => $m->verProducto($id),
		'error' => '',
		'mensaje' => ''
	);

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {


             // comprobar campos formulario
		if ($m->validarDatosProducto($_POST['panaderia'], $_POST['nombre'],
			$_POST['imagen'], $_POST['descripcion'], $_POST['precio'],
			$_POST['cantidad'])) {


			if($m->editarProducto($_POST['panaderia'], $_POST['nombre'],
				$_POST['imagen'], $_POST['descripcion'], $_POST['precio'],
				$_POST['cantidad'],$_POST['id'])){

				$params['mensaje'] = 'Editado correctamente.';

		}else{
			$params['mensaje'] = 'No se ha podido editar el Producto. Revisa los datos del formulario';
		}


	} else {

		$params['mensaje'] = 'Error al Validar Datos, tipo de datos erroneos.';
	}
}

return
$this->render('mipan/vistasJefe/productos/editarProductoJefe.html.twig', $params);

}

public function borrarProductoJefes()
{

	$id = $_GET['id'];

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

	$params = array(
		'producto' => $m->verProducto($id),
	);

	$m->borrarProductoJefes($id);

	return
	$this->render('mipan/vistasJefe/productos/borrarProductoJefe.html.twig' , $params);
	
}




public function listarEmpleados()
{
	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

	$params = array(
		'empleados' => $m->dameEmpleados(),
		'error' => ''
	);

	return
	$this->render('mipan/vistasJefe/empleados/menuJefeEmpleados.html.twig', $params);

}

public function verEmpleado()
{

	$dni = $_GET['dni'];

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);


	$params = array(
		'empleado' => $m->verEmpleado($dni),
		'error' => ''
	);


	return
	$this->render('mipan/vistasJefe/empleados/verEmpleadoJefe.html.twig',
		$params);
}

public function insertarEmpleado()
{

	$params = array(

		'panaderia' => '',

		'dni' => '',
		'nombre' => '',
		'apellidos' => '',
		'email' => '',
		'telefono' => '',
		'especialidad' => '',

		'usuario' => '',
		'pass' => '',

		'mensaje' => ''

	);

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

         // comprobar campos formulario
		if ($m->validarDatosEmpleado($_POST['panaderia'], $_POST['dni'], $_POST['nombre'],
			$_POST['apellidos'], $_POST['email'], $_POST['telefono'],
			$_POST['especialidad'], $_POST['usuario'], $_POST['pass'])) {

			if ($m->insertarEmpleado($_POST['panaderia'], $_POST['dni'], $_POST['nombre'],
				$_POST['apellidos'], $_POST['email'], $_POST['telefono'],
				$_POST['especialidad'], $_POST['usuario'], $_POST['pass'])) {




				$response = $this->forward("App\Controller\MipanController:listarEmpleados");
			return $response;

		} else {


			$params = array(
				'panaderia' => $_POST['panaderia'],

				'dni' => $_POST['dni'],
				'nombre' => $_POST['nombre'],
				'apellidos' => $_POST['apellidos'],
				'email' => $_POST['email'],
				'telefono' => $_POST['telefono'],
				'especialidad' => $_POST['especialidad'],
				'usuario' => $_POST['usuario'],
				'pass' => $_POST['pass'],

				'mensaje' => 'Error al Insertar, datos erroneos.'
			);
		}

	}else{
		$params['mensaje'] = 'Error al Validar Datos, tipo de datos erroneos.';
	}
}

return
$this->render('mipan/vistasJefe/empleados/insertarEmpleadoJefe.html.twig', $params);

}


public function editarEmpleado(){

	$params = array(
		'panaderia' => '',

		'dni' => '',
		'nombre' => '',
		'apellidos' => '',
		'email' => '',
		'telefono' => '',
		'especialidad' => '',
		'usuario' => '',
		'pass' => '',

		'mensaje' => ''
		

	);

	$dni = $_GET['dni'];

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

	$params = array(
		'empleado' => $m->verEmpleado($dni),
		'error' => '',
		'mensaje' => ''
	);

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {


             // comprobar campos formulario
		if ($m->validarDatosEmpleado($_POST['panaderia'], $_POST['dni'], $_POST['nombre'],
			$_POST['apellidos'], $_POST['email'], $_POST['telefono'],
			$_POST['especialidad'], $_POST['usuario'], $_POST['pass'])) {


			if($m->editarEmpleado($_POST['panaderia'], $_POST['dni'], $_POST['nombre'],
				$_POST['apellidos'], $_POST['email'], $_POST['telefono'],
				$_POST['especialidad'], $_POST['usuario'], $_POST['pass'])){

				$params['mensaje'] = 'Editado correctamente.';

		}else{
			$params['mensaje'] = 'No se ha podido editar el Empleado. Revisa los datos del formulario';
		}


	} else {

		$params['mensaje'] = 'Error al Validar Datos, tipo de datos erroneos.';
	}
}

return
$this->render('mipan/vistasJefe/empleados/editarEmpleadoJefe.html.twig', $params);

}


public function borrarEmpleadoJefes()
{

	$dni = $_GET['dni'];

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

	$params = array(
		'empleado' => $m->verEmpleado($dni),
	);

	$m->borrarEmpleadoJefes($dni);

	return
	$this->render('mipan/vistasJefe/empleados/borrarEmpleadoJefe.html.twig' , $params);
	
}







public function listarPedidos()
{
	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

	$params = array(
		'pedidos' => $m->damePedidos(),
		'mensaje' => ''
	);

	return
	$this->render('mipan/vistasJefe/pedidos/listarPedidosJefe.html.twig', $params);

}

//-----------------------------------------------------------------------
//									TRABAJADORES
//------------------------------------------------------------------------
//LOS DEMÁS TIPOS DE TRABAJADORES
public function inicioTrabajador()
{

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario, Config::$mvc_bd_clave, Config::$mvc_bd_hostname);
	
	$params = array(
		'productos' => $m->dameProductos(),
		'mensaje' => ''		
	);

	return $this->render('mipan/vistasTrabajador/menuTrabajador.html.twig', $params);
}


public function inicioTrabajadoresRepartidor()
{
	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario, Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

	$params = array(

		'pedidos' => $m->damePedidos(),
		'mensaje' => ''		
	);

	return $this->render('mipan/vistasTrabajador/menuTrabajadorRepartidor.html.twig', $params);
}

public function inicioTrabajadoresDependiente()
{

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario, Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

	$params = array(
		'productos' => $m->dameProductos(),
		'mensaje' => ''	
	);

	return $this->render('mipan/vistasTrabajador/menuTrabajadorDependiente.html.twig', $params);
}



public function cambiarPedidoAEnCurso(){


	$pedido = $_GET['pedido'];

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

	$params = array(
		
		'pedidos' => $m->damePedidos(),
		'id' => '',
		'mensaje' => ''		

	);

	if($m->cambiarPedidoAEnCurso($pedido)){

		$params['mensaje'] = 'Estado de Pedido actualizado (En curso).';

	}else{
		$params['mensaje'] = 'No se ha podido actualizar el estado del Pedido.';
	}



	return
	$this->render('mipan/vistasTrabajador/menuTrabajadorRepartidor.html.twig', $params);

}

public function cambiarPedidoAPagado(){


	$pedido = $_GET['pedido'];

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

	$params = array(
		
		'pedidos' => $m->damePedidos(),
		'id' => '',
		'mensaje' => ''		

	);

	if($m->cambiarPedidoAPagado($pedido)){

		$params['mensaje'] = 'Estado de Pedido actualizado (Pagado).';

	}else{
		$params['mensaje'] = 'No se ha podido actualizar el estado del Pedido.';
	}



	return
	$this->render('mipan/vistasTrabajador/menuTrabajadorRepartidor.html.twig', $params);

}


public function nuevoPedidoTienda()
{

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

	$params = array(
		'productos' => $m->dameProductos(),
		'mensaje' => ''
	);

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		$panaderia = $_POST['panaderia'];
		$vendedor= $_POST['vendedor'];
		$productoList = $_POST['listaProductos2'];
		$cantidadList = $_POST['listaCantidades2'];
		$totalList = $_POST['totalPedido2'];
		$tipoVenta = "TIENDA";

		$fechaPedido = date("Y-m-d H:i:s");


		if ($m->insertarPedidoTienda($panaderia, $fechaPedido, $vendedor, $tipoVenta, $productoList, $cantidadList, $totalList)) {

			$m->finalizarEstadoPedidoTienda($fechaPedido);

			// $m->actualizarCantidadProducto();


			$params['mensaje'] = 'Pedido realizado correctamente.';
			
			echo '<script language="javascript">alert("Pedido finalizado");window.location.href="/inicioDependiente"</script>';

			$response = $this->forward("App\Controller\MipanController:inicioTrabajadoresDependiente");
			return $response;



		}else{
			$params['mensaje'] = 'Pedido fallido.';
		}



	}else{
		$params['mensaje'] = 'Error FORM.';
	}

	return $this->render('mipan/vistasTrabajador/menuTrabajadorDependiente.html.twig', $params);

}



//-----------------------------------------------------------------------
//									CLIENTES
//------------------------------------------------------------------------


public function inicioClientes()
{
	$params = array(

		'error' => ''	
	);


	return $this->render('mipan/vistasCliente/menuCliente.html.twig', $params);
}
public function cerrarSesionClientes() 
{
	$sesion = $this->container->get('session');

	$sesion->set('Conectado', null);

	$sesion->invalidate();
	$response = $this->forward("App\Controller\MipanController:inicio");

	return $response;
}

public function registroClientes()
{

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario, Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

	$error = '';

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		$dniN = $_POST['dniN'];
		$nombreN = $_POST['nombreN'];
		$apellidosN = $_POST['apellidosN'];
		$direccionN = $_POST['direccionN'];
		$telefonoN = $_POST['telefonoN'];
		$codigoPostalN = $_POST['codigoPostalN'];

		$usuarioN = $_POST['usuarioN'];
		$passN = $_POST['passN'];
		$pass2N = $_POST['pass2N'];


            //el md5 produce un error al comparar los datos de la BD
            // $usuario = $m->buscarUsuario($nombre, md5($pass));

            //Busqueda de datos únicos
		$clie = $m->buscarNuevoCliente($dniN,$direccionN,$telefonoN,$usuarioN);

		//Usuario Encontrado/Para Tutores
		if(count($clie) == 0){
			$m->registroCliente($dniN, $nombreN, $apellidosN, $telefonoN, $direccionN, $codigoPostalN, $usuarioN, $passN);
		} else {
			$error = 'Ya está registrado.';
		}
	}

	$params = array('dniN' => '', 'error' => $error);

	return
	$this->render('mipan/inicio.html.twig', $params);

}


//Muestra una lista de Panaderias con el CodigoPostal igual al del Cliente.
//Con algunos de sus productos para mostrarlos.
public function verCatalogo()
{
	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

	$usuarioActivo = $_GET['usuarioActivo'];

	$params = array(
		'panaderias' => $m->damePanaderiasCercaDeCliente($usuarioActivo),
		'mensaje' => ''
	);

	return
	$this->render('mipan/vistasCliente/catalogo/verCatalogoCliente.html.twig', $params);

}

public function verCatalogoC()
{
	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

	$usuarioActivo = $_GET['usuarioActivo'];

	$params = array(
		'panaderias' => $m->damePanaderiasCercaDeCliente($usuarioActivo),
		'mensaje' => ''
	);

	return
	$this->render('mipan/vistasCliente/carrito/verCatalogoClienteC.html.twig', $params);

}


public function verProductosPanaderia()
{
	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

	$panaderiaSeleccionada = $_GET['panaderia'];

	$params = array(
		'productos' => $m->dameProductosPanaderiaSeleccionada($panaderiaSeleccionada),
		'mensaje' => ''
	);

	return
	$this->render('mipan/vistasCliente/catalogo/verProductosPanaderiaCliente.html.twig', $params);

}

public function seleccionProductosPanaderia()
{
	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

	$panaderiaSeleccionada = $_GET['panaderia'];

	$params = array(
		'productos' => $m->dameProductosPanaderiaSeleccionada($panaderiaSeleccionada),
		'clientes' => $m->dameClientes(),
		'mensaje' => ''
	);

	return
	$this->render('mipan/vistasCliente/catalogo/seleccionProductosPanaderiaCliente.html.twig', $params);

}





public function nuevoPedidoOnline()
{

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

	$params = array(
		'mensaje' => ''
	);

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		$panaderia = $_POST['panaderia'];
		$cliente = $_POST['cliente'];

		// $direccionCliente = $m->dameDireccionClientePedidoOnline($cliente);
		$direccionCliente = $_POST['direccionCliente'];

		$productoList = $_POST['listaProductos2'];
		$cantidadList = $_POST['listaCantidades2'];
		$totalList = $_POST['totalPedido2'];
		$tipoVenta = "REPARTO";
		$fechaPedido = date("Y-m-d H:i:s");


		if ($m->insertarPedidoOnline($panaderia, $fechaPedido, $tipoVenta,
			$cliente, $direccionCliente, $productoList, $cantidadList, $totalList)) {

			$response = $this->forward("App\Controller\MipanController:verPedidosClientes");
			return $response;


		}



	}

	return $this->render('mipan/vistasCliente/menuCliente.html.twig', $params);

}

public function verPedidosClientes()
{
	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);


	$params = array(
		'pedidos' => $m->damePedidosCliente(),
		'mensaje' => ''
	);

	return
	$this->render('mipan/vistasCliente/pedidos/verPedidosCliente.html.twig', $params);

}





public function verPerfilClientes()
{

	$usu = $_GET['usuario'];

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);


	$params = array(
		'perfil' => $m->verPerfilClientes($usu),
		'error' => ''
	);


	return
	$this->render('mipan/vistasCliente/perfil/verPerfilClientes.html.twig',
		$params);
}

public function editarPerfilClientes(){

	$params = array(
		'dni' => '',
		'nombre' => '',
		'apellidos' => '',
		'telefono' => '',
		'direccion' => '',
		'codigoPostal' => '',
		'pass' => ''


	);

	$usuario = $_GET['usuario'];

	$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
		Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

	$params = array(
		'perfil' => $m->verPerfilClientes($usuario),
		'error' => '',
		'mensaje' => ''

	);

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		if($m->editarPerfilClientes($_POST['dni'], $_POST['nombre'],
			$_POST['apellidos'], $_POST['telefono'], $_POST['codigoPostal'], $_POST['direccion'],
			$_POST['pass'])){

			$params['mensaje'] = 'Actualizado correctamente.';

	}else{
		$params['mensaje'] = 'No se ha podido actualizar los datos. Revisa los datos del formulario';
	}

}

return
$this->render('mipan/vistasCliente/perfil/editarPerfilClientes.html.twig', $params);

}






}
?>