<?php //checar si tiene permisos

$path=$_SERVER['REQUEST_URI'];
	try{
		$bd=new PDO($dsnw, $userw, $passw, $optPDO);
		$sql="SELECT cotizacion,evento,almacen,compras,bancos,modulos,gastos FROM usuario_permisos WHERE id_usuario=$userid;";
		$query=$bd->query($sql);
		$res = $query->fetch(PDO::FETCH_ASSOC);
		$cotizacion=$res["cotizacion"];
		$evento=$res["evento"];
		$almacen=$res["almacen"];
		$compras=$res["compras"];
		$bancos=$res["bancos"];
		$modulos=$res["modulos"];
		$gastos=$res["gastos"];
		if(strpos($path,'cotizacion') !== false) {
			if($cotizacion==0){
    		echo '<h1 align="center">Este usuario no tiene permiso para ver esta sección</h1>';
			include("partes/footer.php");
			exit;
			}
		}
		if(strpos($path,'evento') !== false) {
			if($evento==0){
    		echo '<h1 align="center">Este usuario no tiene permiso para ver esta sección</h1>';
			include("partes/footer.php");
			exit;
			}
		}

		if(strpos($path,'almacen') !== false) {
			if($almacen==0){
    		echo '<h1 align="center">Este usuario no tiene permiso para ver esta sección</h1>';
			include("partes/footer.php");
			exit;
			}
		}
		if(strpos($path,'compras') !== false) {
			if($compras==0){
    		echo '<h1 align="center">Este usuario no tiene permiso para ver esta sección</h1>';
			include("partes/footer.php");
			exit;
			}
		}

		if(strpos($path,'modulos') !== false) {
			if($modulos==0){
    		echo '<h1 align="center">Este usuario no tiene permiso para ver esta sección</h1>';
			include("partes/footer.php");
			exit;
			}
		}
		
		if(strpos($path,'bancos') !== false) {
			if($bancos==0){
    		echo '<h1 align="center">Este usuario no tiene permiso para ver esta sección</h1>';
			include("partes/footer.php");
			exit;
			}
		}
		
		if(strpos($path,'gastos') !== false) {
			if($gastos==0){
    		echo '<h1 align="center">Este usuario no tiene permiso para ver esta sección</h1>';
			include("partes/footer.php");
			exit;
			}
		}
		
		
		
	}catch(PDOException $err){
		echo $err->getMessage();
		include("partes/footer.php");
		exit;
	}

?>