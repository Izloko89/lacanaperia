<?php //checar si tiene permisos
if($_SESSION["categoria"]!="administrador"){
	try{
		$bd=new PDO($dsnw, $userw, $passw, $optPDO);
		$sql="SELECT * FROM usuarios_permisos WHERE id_usuario=$userid;";
		$res=$bd->query($sql);
		$res=$res->fetchAll(PDO::FETCH_ASSOC);
		$d=$res[0];
		if($d[$seccion]==0){
			echo '<h1 align="center">Este usuario no tiene permiso para ver esta sección</h1>';
			include("partes/footer.php");
			exit;
		}
		
	}catch(PDOException $err){
		echo $err->getMessage();
		include("partes/footer.php");
		exit;
	}
}
?>