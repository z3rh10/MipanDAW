<?php

namespace App\Model;
class Model
{
 protected $conexion;

 public function __construct($dbname,$dbuser,$dbpass,$dbhost)
 {
   $mvc_bd_conexion = mysqli_connect($dbhost, $dbuser, $dbpass,$dbname);

   if (!$mvc_bd_conexion) {
     die('No ha sido posible realizar la conexión con la base de datos: ' . mysqli_error());
   }


   mysqli_set_charset($mvc_bd_conexion,'utf8');

   $this->conexion = $mvc_bd_conexion;
 }



 public function bd_conexion()
 {

 }






//MIPAN
// Usuarios


 //admin ya registrados
 public function buscarAdmin($usuarioAdmin, $passAdmin)
 {
   $usuarioAdmin = htmlspecialchars($usuarioAdmin);
   $passAdmin = htmlspecialchars($passAdmin);

   $sql = "select * from admin where usuario='". $usuarioAdmin . "' and pass='" . $passAdmin . "';";

   $result = mysqli_query($this->conexion,$sql);
   $admins = array();

   while ($row = mysqli_fetch_assoc($result))
   {
     $admins[] = $row;
   }

   return $admins;
 }

//jefes ya registrados
 public function buscarJefe($usuarioJefe, $passJefe)
 {
   $usuarioJefe = htmlspecialchars($usuarioJefe);
   $passJefe = htmlspecialchars($passJefe);

   $especialidadJefe = "JEFE";

   $sql = "select * from trabajador where usuario='". $usuarioJefe . "' and pass='" . $passJefe . "' and especialidad = '".$especialidadJefe."';";

   $result = mysqli_query($this->conexion,$sql);
   $jefes = array();

   while ($row = mysqli_fetch_assoc($result))
   {
     $jefes[] = $row;
   }

   return $jefes;
 }

 //Trabajadores ya registrados
 public function buscarTrabajador($usuarioTrabajador, $passTrabajador)
 {
   $usuarioTrabajador = htmlspecialchars($usuarioTrabajador);
   $passTrabajador = htmlspecialchars($passTrabajador);

   $especialidadTrabajadorMAESTRO_DE_PALA = "MAESTRO_DE_PALA";
   $especialidadTrabajadorOFICIAL_DE_MESA = "OFICIAL_DE_MESA";
   $especialidadTrabajadorOFICIAL_DE_MASA = "OFICIAL_DE_MASA";
   $especialidadTrabajadorAYUDANTE = "AYUDANTE";
   $especialidadTrabajadorAPRENDIZ = "APRENDIZ";
   $especialidadTrabajadorDEPENDIENTE = "DEPENDIENTE";
   $especialidadTrabajadorREPARTIDOR = "REPARTIDOR";

   $sql = "select * from trabajador where usuario='". $usuarioTrabajador . "' and pass='" . $passTrabajador . "' and
   (especialidad = '".$especialidadTrabajadorMAESTRO_DE_PALA."' OR  especialidad = '".$especialidadTrabajadorOFICIAL_DE_MASA."' OR especialidad = '".$especialidadTrabajadorOFICIAL_DE_MESA."' OR especialidad = '".$especialidadTrabajadorAYUDANTE."' OR  especialidad = '".$especialidadTrabajadorAPRENDIZ."' OR especialidad = '".$especialidadTrabajadorDEPENDIENTE."' OR especialidad = '".$especialidadTrabajadorREPARTIDOR."');";

   $result = mysqli_query($this->conexion,$sql);
   $trabajadores = array();

   while ($row = mysqli_fetch_assoc($result))
   {
     $trabajadores[] = $row;
   }

   return $trabajadores;
 }

 public function buscarTrabajadorRepartidor($usuarioTrabajador, $passTrabajador)
 {
   $usuarioTrabajador = htmlspecialchars($usuarioTrabajador);
   $passTrabajador = htmlspecialchars($passTrabajador);

   $especialidadTrabajadorREPARTIDOR = "REPARTIDOR";

   $sql = "select * from trabajador where usuario='". $usuarioTrabajador . "' and pass='" . $passTrabajador . "' and
   (especialidad = '".$especialidadTrabajadorREPARTIDOR."');";

   $result = mysqli_query($this->conexion,$sql);
   $trabajadores = array();

   while ($row = mysqli_fetch_assoc($result))
   {
     $trabajadores[] = $row;
   }

   return $trabajadores;
 }

 public function buscarTrabajadorDependiente($usuarioTrabajador, $passTrabajador)
 {
   $usuarioTrabajador = htmlspecialchars($usuarioTrabajador);
   $passTrabajador = htmlspecialchars($passTrabajador);

   $especialidadTrabajadorDEPENDIENTE = "DEPENDIENTE";

   $sql = "select * from trabajador where usuario='". $usuarioTrabajador . "' and pass='" . $passTrabajador . "' and
   (especialidad = '".$especialidadTrabajadorDEPENDIENTE."');";

   $result = mysqli_query($this->conexion,$sql);
   $trabajadores = array();

   while ($row = mysqli_fetch_assoc($result))
   {
     $trabajadores[] = $row;
   }

   return $trabajadores;
 }

 

//jefes sin registrar, nuevos
 public function buscarNuevoJefe($dni,$email,$telefono,$usuario)
 {
   $dni = htmlspecialchars($dni);
   $email = htmlspecialchars($email);
   $telefono = htmlspecialchars($telefono);
   $usuario = htmlspecialchars($usuario);

   $sql = "select * from trabajador where dni= '". $dni . "' and email = '".$email."' and telefono = ".$telefono."
   and usuario = '".$usuario."' and especialidad = 'JEFE' ;";

   $result = mysqli_query($this->conexion,$sql);
   $jefes = array();

   while ($row = mysqli_fetch_assoc($result))
   {
     $jefes[] = $row;
   }

   return $jefes;
 }




 public function verTodasPanaderias()
 {
   $sql = "select * from panaderia;";

   $result = mysqli_query($this->conexion,$sql);

   $panaderias = array();
   while ($row = mysqli_fetch_assoc($result))
   {
     $panaderias[] = $row;
   }

   return $panaderias;
 }


 public function buscarPanaderiaPorNombre($nombre)
 {
   $nombre = htmlspecialchars($nombre);

   $sql = "select * from panaderia where nombre like '" . $nombre . "';";

   $result = mysqli_query($this->conexion,$sql);

   $panaderias = array();
   while ($row = mysqli_fetch_assoc($result))
   {
     $panaderias[] = $row;
   }

   return $panaderias;
 }

 public function buscarPanaderiaPorCodigoPostal($cp)
 {
   $cp = htmlspecialchars($cp);

   $sql = "select * from panaderia where codigoPostal like '" . $cp . "';";

   $result = mysqli_query($this->conexion,$sql);

   $panaderias = array();
   while ($row = mysqli_fetch_assoc($result))
   {
     $panaderias[] = $row;
   }

   return $panaderias;
 }


 //-----

 public function buscarTrabajadorPorPanaderia($panaderia)
 {
   $panaderia = htmlspecialchars($panaderia);

   $sql = "select * from trabajador where panaderia like '" . $panaderia . "';";

   $result = mysqli_query($this->conexion,$sql);

   $trabajadores = array();
   while ($row = mysqli_fetch_assoc($result))
   {
     $trabajadores[] = $row;
   }

   return $trabajadores;
 }

 public function buscarTrabajadorPorEspecialidad($especialidad)
 {
   $especialidad = htmlspecialchars($especialidad);

   $sql = "select * from trabajador where especialidad like '" . $especialidad . "';";

   $result = mysqli_query($this->conexion,$sql);

   $trabajadores = array();
   while ($row = mysqli_fetch_assoc($result))
   {
     $trabajadores[] = $row;
   }

   return $trabajadores;
 }



 public function buscarClientePorCodigoPostal($codigoPostal)
 {
   $codigoPostal = htmlspecialchars($codigoPostal);

   $sql = "select * from cliente where codigoPostal like '" . $codigoPostal . "';";

   $result = mysqli_query($this->conexion,$sql);

   $clientes = array();
   while ($row = mysqli_fetch_assoc($result))
   {
     $clientes[] = $row;
   }

   return $clientes;
 }


 public function buscarClientePorUsuario($usuario)
 {
   $usuario = htmlspecialchars($usuario);

   $sql = "select * from cliente where usuario like '" . $usuario . "';";

   $result = mysqli_query($this->conexion,$sql);

   $clientes = array();
   while ($row = mysqli_fetch_assoc($result))
   {
     $clientes[] = $row;
   }

   return $clientes;
 }



















 public function registroJefe($d, $n, $a, $e, $t, $u, $p)
 {
  $d = htmlspecialchars($d);
  $n = htmlspecialchars($n);
  $a = htmlspecialchars($a);
  $e = htmlspecialchars($e);
  $t = htmlspecialchars($t);

  $especialidad = "JEFE";

  $u = htmlspecialchars($u);
  $p = htmlspecialchars($p);


  $sql = "insert into trabajador (dni, nombre, apellidos, email, telefono, especialidad, usuario, pass) values 
  ('" . $d . "', '" . $n . "', '" . $a ."', '" . $e ."', '" . $t . "','" . $especialidad . "', '" . $u ."', '" . $p ."');";

  $result = mysqli_query($this->conexion,$sql);

  return $result;
}



public function verificarPanaderiaJefe($usuarioJefe)
{
 $usuarioJefe = htmlspecialchars($usuarioJefe);


 $sql = "select * from trabajador where usuario= '". $usuarioJefe . "' and especialidad = 'JEFE' and panaderia IS NULL;";

 $result = mysqli_query($this->conexion,$sql);
 $jefesSinPanaderia = array();

 while ($row = mysqli_fetch_assoc($result))
 {
   $jefesSinPanaderia[] = $row;
 }

 return $jefesSinPanaderia;
}



public function buscarNuevaPanaderia($nombreP,$telefonoP,$direccionP,$ubicacionP)
{
 $nombreP = htmlspecialchars($nombreP);
 $telefonoP = htmlspecialchars($telefonoP);
 $direccionP = htmlspecialchars($direccionP);
 $ubicacionP = htmlspecialchars($ubicacionP);


 $sql = "select * from panaderia where nombre= '". $nombreP . "' and direccion = '".$direccionP."' and telefono = '".$telefonoP."'  and ubicacion = '".$ubicacionP."';";

 $result = mysqli_query($this->conexion,$sql);
 $panaderias = array();



 $result = mysqli_query($this->conexion,$sql);



 while ($row = mysqli_fetch_assoc($result))
 {
   $panaderias[] = $row;
 }

 return $panaderias;
}


public function registroPanaderia($nombreP, $logoP, $telefonoP, $direccionP, $ubicacionP, $horarioP, $codigoPostalP)
{

  $nombreP = htmlspecialchars($nombreP);
  $logoP = htmlspecialchars($logoP);
  $telefonoP = htmlspecialchars($telefonoP);
  $direccionP = htmlspecialchars($direccionP);
  $ubicacionP = htmlspecialchars($ubicacionP);
  $horarioP = htmlspecialchars($horarioP);
  $codigoPostalP = htmlspecialchars($codigoPostalP);


  $sql = "insert into panaderia (nombre, logo, telefono, direccion, ubicacion, horario, codigoPostal) values 
  ('" . $nombreP . "', '" . $logoP . "', '" . $telefonoP ."', '" . $direccionP ."', '" . $ubicacionP . "','" . $horarioP ."','"
  . $codigoPostalP."');";


  
  

  $result = mysqli_query($this->conexion,$sql);

  return $result;
}


//AL REGISTRAR LA NUEVA PANADERIA TAMBIÉN SE ACTUALIZAN LOS DATOS DEL JEFE A LA PAR
public function actualizaCampoPanaderiaDeJefe($nombreP, $usuActivo)
{

  $nombreP = htmlspecialchars($nombreP);
  $usuActivo = htmlspecialchars($usuActivo);
  //al añadir la panaderia, se actualizan los datos del JEFE
  $sql = "update trabajador set panaderia = '".$nombreP."' where especialidad = 'JEFE' and usuario = '". $usuActivo . "';";

  $result = mysqli_query($this->conexion,$sql);

  return $result;
}

public function sesionPanaderiaJefe($usuarioJefe)
{
 $usuarioJefe = htmlspecialchars($usuarioJefe);

 $sql = "select panaderia from trabajador where usuario='".$usuarioJefe."' limit 1;";

 $result = mysqli_query($this->conexion,$sql);

 $panaderias = array();
 $row = mysqli_fetch_assoc($result);

 return $row;

}
public function sesionPanaderiaTrabajador($usuarioTrabajador)
{
 $usuarioTrabajador = htmlspecialchars($usuarioTrabajador);

 $sql = "select panaderia from trabajador where usuario='".$usuarioTrabajador."' limit 1;";

 $result = mysqli_query($this->conexion,$sql);

 $panaderias = array();
 $row = mysqli_fetch_assoc($result);

 return $row;

}

public function verPerfilJefes($usu)
{
 $usu =  htmlspecialchars($usu);

 $sql = "select * from trabajador where usuario='".$usu."';";

 $result = mysqli_query( $this->conexion,$sql);

 $perfiles = array();
 $row = mysqli_fetch_assoc($result);

 return $row;

}

public function validarDatosPerfil($dni, $n, $a, $e, $t, $p)
{
  return 
  (is_string($dni) &
   is_string($n) &
   is_string($a) &
   is_string($e) &
   is_numeric($t) &
   is_string($p)); 
}

public function editarPerfil($dni, $n, $a, $e, $t, $p)
{

 $dni = htmlspecialchars($dni);

 $n = htmlspecialchars($n);
 $a = htmlspecialchars($a);
 $e = htmlspecialchars($e);
 $t = htmlspecialchars($t);
 $p = htmlspecialchars($p);



 $sql = "UPDATE trabajador SET nombre='".$n."', apellidos='".$a."', email='".$e."', telefono=".$t.", pass='".$p."' where dni='".$dni."';";
   //$stmt = $this->conexion->prepare($sql);
 $result = mysqli_query($this->conexion, $sql);

 return $result;
}

public function verPanaderiaJefe($nombrePanaderia)
{
 $nombrePanaderia =  htmlspecialchars($nombrePanaderia);

 $sql = "select * from panaderia where nombre='".$nombrePanaderia."';";

 $result = mysqli_query( $this->conexion,$sql);

 $panaderias = array();
 $row = mysqli_fetch_assoc($result);

 return $row;

}

public function validarDatosPanaderia($n, $l, $t, $d, $u, $h, $cp)
{
  return 
  (is_string($n) &
   is_string($l) &
   is_numeric($t) &
   is_string($d) &
   is_string($u) &
   is_string($h) &
   is_numeric($cp)); 
}

public function editarPanaderia($n, $l, $t, $d, $u, $h, $cp)
{

 $n = htmlspecialchars($n);

 $l = htmlspecialchars($l);
 $t = htmlspecialchars($t);
 $d = htmlspecialchars($d);
 $u = htmlspecialchars($u);
 $h = htmlspecialchars($h);

 $cp = htmlspecialchars($cp);



 $sql = "UPDATE panaderia SET logo='".$l."', telefono=".$t.", direccion='".$d."' , codigoPostal ='".$cp."', ubicacion='".$u."', horario='".$h."' where nombre='".$n."';";
   //$stmt = $this->conexion->prepare($sql);
 $result = mysqli_query($this->conexion, $sql);

 return $result;
}



public function dameProductos()
{
 $sql = "select * from producto order by precio asc";

 $result = mysqli_query($this->conexion,$sql);

 $productos = array();
 while ($row = mysqli_fetch_assoc($result))
 {
   $productos[] = $row;
 }

 return $productos;
}

public function verProducto($id)
{
 $id = htmlspecialchars($id);

 $sql = "select * from producto where id=".$id;

 $result = mysqli_query( $this->conexion,$sql);

 $productos = array();
 $row = mysqli_fetch_assoc($result);

 return $row;

}

public function insertarProducto($p, $n, $i, $d, $pr, $c)
{
 $p = htmlspecialchars($p);
 $n = htmlspecialchars($n);
 $i = htmlspecialchars($i);
 $d = htmlspecialchars($d);
 $pr = htmlspecialchars($pr);
 $c = htmlspecialchars($c);
 

 $sql = "insert into producto (panaderia, nombre, imagen, descripcion, precio, cantidad) values ('" .
 $p . "','" . $n . "', '" . $i . "' , '" . $d . "' ," . $pr . "," . $c . ")";

 $result = mysqli_query($this->conexion,$sql);

 return $result;
}

public function editarProducto($p, $n, $i, $d, $pr, $c, $id)
{
 $p = htmlspecialchars($p);
 $n = htmlspecialchars($n);
 $i = htmlspecialchars($i);
 $d = htmlspecialchars($d);
 $pr = htmlspecialchars($pr);
 $c = htmlspecialchars($c);
 $id = htmlspecialchars($id);


 $sql = "UPDATE producto SET panaderia='".$p."',nombre='".$n."', imagen='".
 $i."', descripcion='".$d."', precio=".$pr.", cantidad=".$c." where id=".$id.";";
   //$stmt = $this->conexion->prepare($sql);
 $result = mysqli_query($this->conexion, $sql);

 return $result;
}

public function validarDatosProducto($p, $n, $i, $d, $pr, $c)
{
  return 
  (is_string($p) &
   is_string($n) &
   is_string($i) &
   is_string($d) &
   is_numeric($pr) &
   is_numeric($c)); 
}


public function borrarProductoJefes($id)
{
 $id = htmlspecialchars($id);

 $sql = "delete from producto where id = ".$id.";";

 $result = mysqli_query($this->conexion,$sql);

 return $result;

}



public function dameEmpleados()
{
 $sql = "select * from trabajador;";

 $result = mysqli_query($this->conexion,$sql);

 $empleados = array();
 while ($row = mysqli_fetch_assoc($result))
 {
   $empleados[] = $row;
 }

 return $empleados;
}

public function verEmpleado($dni)
{
 $dni = htmlspecialchars($dni);

 $sql = "select * from trabajador where dni= '".$dni."';";

 $result = mysqli_query( $this->conexion,$sql);

 $empleados = array();
 $row = mysqli_fetch_assoc($result);

 return $row;

}

public function validarDatosEmpleado($p, $d,$n, $a, $e, $t, $esp ,$usu, $pass)
{
  return 
  (is_string($p) &
   is_string($n) &
   is_string($n) &
   is_string($a) &
   is_string($e) &
   is_numeric($t) &
   is_string($usu) &
   is_string($pass)); 
}

public function insertarEmpleado($p, $d, $n, $a, $e, $t, $esp ,$usu, $pass)
{
 $p = htmlspecialchars($p);

 $d = htmlspecialchars($d);
 $n = htmlspecialchars($n);
 $a = htmlspecialchars($a);
 $e = htmlspecialchars($e);
 $t = htmlspecialchars($t);
 $esp = htmlspecialchars($esp);
 $usu = htmlspecialchars($usu);
 $pass = htmlspecialchars($pass);
 

 $sql = "insert into trabajador(dni, nombre, apellidos, email, telefono, panaderia, especialidad, usuario, pass) 
 values ('" . $d . "','" . $n . "', '" . $a . "' , '" . $e . "' ," . $t . ", '" . $p . "' , '" . $esp . "',
 '" . $usu . "' , '" . $pass . "');";

 $result = mysqli_query($this->conexion,$sql);

 return $result;
}

public function editarEmpleado($p, $d, $n, $a, $e, $t, $esp ,$usu, $pass)
{
  $p = htmlspecialchars($p);

  $d = htmlspecialchars($d);
  //Por si se cambia también el DNI
  $d2 =htmlspecialchars($d);

  $n = htmlspecialchars($n);
  $a = htmlspecialchars($a);
  $e = htmlspecialchars($e);
  $t = htmlspecialchars($t);
  $esp = htmlspecialchars($esp);
  $usu = htmlspecialchars($usu);
  $pass = htmlspecialchars($pass);


  $sql = "UPDATE trabajador SET panaderia='".$p."', dni='".$d."', nombre ='".
  $n."', apellidos='".$a."', email='".$e."', telefono=".$t.", especialidad='".$esp."', usuario='".$usu."', pass='".$pass.
  "' where dni='".$d2."';";
   //$stmt = $this->conexion->prepare($sql);
  $result = mysqli_query($this->conexion, $sql);

  return $result;
}

public function borrarEmpleadoJefes($dni)
{
 $dni = htmlspecialchars($dni);

 $sql = "delete from trabajador where dni = '".$dni."';";

 $result = mysqli_query($this->conexion,$sql);

 return $result;

}

public function borrarClienteAdmin($usu)
{
 $usu = htmlspecialchars($usu);

 $sql = "delete from cliente where usuario = '".$usu."';";

 $result = mysqli_query($this->conexion,$sql);

 return $result;

}


public function damePedidos()
{
 $sql = "select * from pedido;";

 $result = mysqli_query($this->conexion,$sql);

 $pedidos = array();
 while ($row = mysqli_fetch_assoc($result))
 {
   $pedidos[] = $row;
 }

 return $pedidos;
}
//---------------------------------------------------
//                    TRABAJADORES
//---------------------------------------------------


public function cambiarPedidoAEnCurso($p)
{
  $p = htmlspecialchars($p);


  $sql = "update pedido SET pendiente = false, enCurso= true, pagado = false where id=".$p.";";
   //$stmt = $this->conexion->prepare($sql);
  $result = mysqli_query($this->conexion, $sql);

  return $result;
}

public function cambiarPedidoAPagado($p)
{
  $p = htmlspecialchars($p);


  $sql = "update pedido SET pendiente = false, enCurso= false, pagado = true where id=".$p.";";
   //$stmt = $this->conexion->prepare($sql);
  $result = mysqli_query($this->conexion, $sql);

  return $result;
}



public function insertarPedidoTienda($panaderia, $fechaPedido, $vendedor, $tipoVenta, $productoList, $cantidadList, $totalList)
{

 $panaderia = htmlspecialchars($panaderia);
 $fechaPedido = htmlspecialchars($fechaPedido);
 $vendedor = htmlspecialchars($vendedor);
 $tipoVenta = htmlspecialchars($tipoVenta);
 $productoList = htmlspecialchars($productoList);
 $cantidadList = htmlspecialchars($cantidadList);
 $totalList = htmlspecialchars($totalList);

 

 $sql = "insert into Pedido(panaderia, fechaPedido, vendedor, tipoVenta, producto, cantidad, importe) 
 values ('" . $panaderia . "','" . $fechaPedido . "', '" . $vendedor . "' ,
  '" . $tipoVenta ."', '" . $productoList . "' , '" . $cantidadList . "', " . $totalList . ");";

 $result = mysqli_query($this->conexion,$sql);

 return $result;
}



public function finalizarEstadoPedidoTienda($fechaPedido)
{

  $sql = "update pedido SET pendiente = false, enCurso= false, pagado = true where fechaPedido='".$fechaPedido."';";
   //$stmt = $this->conexion->prepare($sql);
  $result = mysqli_query($this->conexion, $sql);

  return $result;
}







//---------------------------------------------------
//                    CLIENTES
//---------------------------------------------------

public function dameClientes()
{
 $sql = "select * from cliente;";

 $result = mysqli_query($this->conexion,$sql);

 $clientes = array();
 while ($row = mysqli_fetch_assoc($result))
 {
   $clientes[] = $row;
 }

 return $clientes;
}

public function buscarCliente($usuarioCliente, $passCliente)
{
 $usuarioCliente = htmlspecialchars($usuarioCliente);
 $passCliente = htmlspecialchars($passCliente);

 $sql = "select * from cliente where usuario='". $usuarioCliente . "' and pass='" . $passCliente . "';";

 $result = mysqli_query($this->conexion,$sql);
 $clientes = array();

 while ($row = mysqli_fetch_assoc($result))
 {
   $clientes[] = $row;
 }

 return $clientes;
}
//clientes sin registrar, nuevos
public function buscarNuevoCliente($dni,$direccion,$telefono,$usuario)
{
 $dni = htmlspecialchars($dni);
 $direccion = htmlspecialchars($direccion);
 $telefono = htmlspecialchars($telefono);
 $usuario = htmlspecialchars($usuario);

 $sql = "select * from cliente where dni= '". $dni . "' and direccion = '".$direccion."' and telefono = ".$telefono."
 and usuario = '".$usuario."' ;";

 $result = mysqli_query($this->conexion,$sql);
 $clientes = array();

 while ($row = mysqli_fetch_assoc($result))
 {
   $clientes[] = $row;
 }

 return $clientes;
}


public function registroCliente($d, $n, $a, $t, $direc, $cp, $u, $p)
{
  $d = htmlspecialchars($d);
  $n = htmlspecialchars($n);
  $a = htmlspecialchars($a);
  $t = htmlspecialchars($t);
  $direc = htmlspecialchars($direc);
  $cp = htmlspecialchars($cp);

  $u = htmlspecialchars($u);
  $p = htmlspecialchars($p);


  $sql = "insert into cliente (dni, nombre, apellidos, telefono, direccion, codigoPostal, usuario, pass) values 
  ('" . $d . "', '" . $n . "', '" . $a ."', " . $t .", '" . $direc . "', '". $cp . "', '" . $u  . "', '" . $p ."');";

  $result = mysqli_query($this->conexion,$sql);

  return $result;
}


public function damePanaderiasCercaDeCliente($usuarioActivo)
{

  $usuarioActivo = htmlspecialchars($usuarioActivo);

  $sql = "select p.* FROM panaderia p RIGHT JOIN Cliente c ON p.codigoPostal = c.codigoPostal 
  WHERE c.usuario = '" . $usuarioActivo . "' AND p.codigoPostal = c.codigoPostal ;";

  $result = mysqli_query($this->conexion,$sql);

  $panaderias = array();
  while ($row = mysqli_fetch_assoc($result))
  {
   $panaderias[] = $row;
 }

 return $panaderias;
}


public function dameProductosPanaderiaSeleccionada($panaderiaSeleccionada)
{

  $panaderiaSeleccionada = htmlspecialchars($panaderiaSeleccionada);

  $sql = "select * from producto where panaderia='". $panaderiaSeleccionada ."';";

  $result = mysqli_query($this->conexion,$sql);

  $productos = array();
  while ($row = mysqli_fetch_assoc($result))
  {
   $productos[] = $row;
 }

 return $productos;
}



public function dameDireccionClientePedidoOnline($clienteSeleccionado)
{

  $clienteSeleccionado = htmlspecialchars($clienteSeleccionado);

  $sql = "select direccion from cliente where usuario='". $clienteSeleccionado ."' limit 1;";

  $result = mysqli_query($this->conexion,$sql);

  $direcciones = array();
  while ($row = mysqli_fetch_assoc($result))
  {
   $direcciones[] = $row;
 }

 return $direcciones;
}




public function insertarPedidoOnline($panaderia, $fechaPedido, $tipoVenta,
  $cliente, $direccionCliente, $productoList,
  $cantidadList, $totalList)
{

 $panaderia = htmlspecialchars($panaderia);
 $fechaPedido = htmlspecialchars($fechaPedido);
 $tipoVenta = htmlspecialchars($tipoVenta);
 $cliente = htmlspecialchars($cliente);
 $direccionCliente = htmlspecialchars($direccionCliente);
 $productoList = htmlspecialchars($productoList);
 $cantidadList = htmlspecialchars($cantidadList);
 $totalList = htmlspecialchars($totalList);

 

 $sql = "insert into Pedido(panaderia, fechaPedido, tipoVenta, cliente, direccionCliente, producto, cantidad, importe) 
 values ('" . $panaderia . "','" . $fechaPedido . "', '" . $tipoVenta . "' , '" . $cliente . "' ,'" . $direccionCliente .
 "', '" . $productoList . "' , '" . $cantidadList . "', " . $totalList . ");";

 $result = mysqli_query($this->conexion,$sql);

 return $result;
}


public function damePedidosCliente()
{

  $sql = "select * from pedido;";

  $result = mysqli_query($this->conexion,$sql);

  $pedidos = array();
  while ($row = mysqli_fetch_assoc($result))
  {
   $pedidos[] = $row;
 }

 return $pedidos;
}

public function verPerfilClientes($usu)
{
 $usu =  htmlspecialchars($usu);

 $sql = "select * from cliente where usuario='".$usu."';";

 $result = mysqli_query( $this->conexion,$sql);

 $perfiles = array();
 $row = mysqli_fetch_assoc($result);

 return $row;

}

public function editarPerfilClientes($dni, $n, $a, $t, $cp, $d, $p)
{

 $dni = htmlspecialchars($dni);

 $n = htmlspecialchars($n);
 $a = htmlspecialchars($a);
 $t = htmlspecialchars($t);
 $cp = htmlspecialchars($cp);
 $d = htmlspecialchars($d);
 $p = htmlspecialchars($p);



 $sql = "UPDATE cliente SET nombre='".$n."', apellidos='".$a."', codigoPostal='".$cp."', telefono='".$t."'
 , direccion='".$d."', pass='".$p."' where dni='".$dni."';";
   //$stmt = $this->conexion->prepare($sql);
 $result = mysqli_query($this->conexion, $sql);

 return $result;
}





}

?>