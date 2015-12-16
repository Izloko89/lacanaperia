<?php session_start(); 
include("scripts/datos.php");
if(isset($_SESSION["id_usuario"])){
	if($_SESSION["id_usuario"]!=""){
		header('Location: '.LIGA.'home.php');
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" media="all" href="css/demo.min.css" />
<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="js/index.js"></script>
<title>Login - Administración de eventos</title>
</head>

<body>
	<div class="bg">
	<div class="logo"><img src="img/Blanco.png" width="100%"></div>
	<div class="servicio">
		<table>
			<tr><th>ATENCI&Oacute;N 24/7, CONT&Aacute;CTANOS:</th></tr>
			<tr><td>Whatsapp: 811-048-4378
					<br>Oficina: (0181) 8880-3040
					<br>Skype: alejsys@hotmail.com:</td>
				<td>Direcci&oacute;n: jaleman@S21sistemas.com.mx
					<br>edith@S21sistemas.com.mx
					<br>Ventas: ceci@S21sistemas.com.mx</td>
			</tr>
		</table>
	</div>
	<center>
	
	<form id="login"><div class="formulario">
		<h2>ADMINISTRACI&Oacute;N DE EVENTOS</h2>
		<h1>Inicio de sesi&oacute;n</h1><br>
		<input type="text" name="usuario" placeholder="Usuario" value="admin" /><br />
        <input type="password" name="pass" placeholder="Contraseña" value="admin"/><br/>
    	<input class="login" type="button" value="Ingresar" />
        <div class="respuesta"></div></div>
    </form>
    
    </center>
    </div>
    <footer><p>Copyright © S21 SISTEMAS 2015. Todos los derechos reservados.</p></footer>

</body>
</html>