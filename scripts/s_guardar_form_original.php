<?php session_start();
$empresaid=$_SESSION["id_empresa"];
$userid=$_SESSION["id_usuario"];
header("Content-type: Application/json");

include("datos.php");
include("pivotes.php");
include("ocupa_user.php");
include("func_guardar.php");
$campoClave="clave";

switch($_POST["metodo"]){

	case 'pivote':
	//en el caso de que sea nuevo hay un control booleano para guardar primero el de la tabla piloto y luego
	//las siguientes tablas
	
	//strings pivote
	$primero=true;
	$clave="";
	$id="";
	$campoPivote="";
	
	//ajusta los datos de los formularios
	$paquete=array();
	foreach($_POST["datos"] as $form=>$datos){
		foreach($datos as $item=>$valor){
			$paquete[$form][$valor["name"]]=$valor["value"];
			if($valor["name"]=="fecha"){
				$paquete[$form][$valor["name"]]=fixFecha($valor["value"]);
			}
			if($valor["name"]=="fechaevento"){
				$paquete[$form][$valor["name"]]=fixFecha($valor["value"]);
			}
			if($valor["name"]=="fechamontaje"){
				$paquete[$form][$valor["name"]]=fixFecha($valor["value"]);
			}
			if($valor["name"]=="fechadesmont"){
				$paquete[$form][$valor["name"]]=fixFecha($valor["value"]);
			}
		}
	}
	unset($_POST["datos"]);
	
		try{
			$bd=new PDO($dsnw, $userw, $passw, $optPDO);
			$bd->beginTransaction();
			
			foreach($paquete as $form=>$datos){
			//asigna el nombre de la tabla
				$tabla=str_replace("f_","",$form);
			
			//crea el sql query
				$campos="";
				$values="";
				
			//si el id de referencia está asignado y no está vacio entonces asigna una vez el campo y clave id_{tabla principal en singular}
				if($id!="" and !$primero){
					$campos.= $campoPivote.",";
					$values.= "'".$id."',";
				}
				
			//añade el id_usuario y el id_empresa a la primera consulta
				if($primero){
					if($ocupaUser[$tabla]){
						$campos.="id_empresa, id_usuario,";
						$values.="$empresaid, $userid,";//*/
					}else{
						$campos.="id_empresa,";
						$values.="$empresaid,";
					}
				}
				
				//excepciones para el campo clave
				switch($tabla){
					case 'usuarios':
						$campos="id_empresa,";
						$values="$empresaid,";
					break;
				}
				
			//Checa si no existe una misma clave. En este script siempre habrá un primer form con la clave del registro
				if($primero){
				  if($tabla!="usuarios"){
					$clave=$datos["clave"];
					
					//excepciones para el campo clave
					switch($tabla){
						case 'usuarios':
							$campoClave="usuario";
							$clave=$datos["usuario"];
							$campos.="id_empresa,";
							$values.="$empresaid,";
						break;
					}
					
					$campoPivote=$pivotes[$tabla];
					$sql="SELECT $campoPivote FROM $tabla WHERE id_empresa=$empresaid AND $campoClave ='$clave';";
					if($ocupaUser[$tabla]){
						$sql="SELECT $campoPivote FROM $tabla WHERE id_usuario=$userid AND id_empresa=$empresaid AND $campoClave ='$clave';";
					}
					$res=$bd->query($sql);
				  }//if de las excepciones
				}
				
			//Hace el string de sql
				foreach($datos as $campo => $valor){
					if($primero and $campo=="clave"){
						$clave=$valor;
					}
					$campos.= $campo.",";
					$values.= "'".$valor."',"; //numerico no lleva ''
				}
				//generar el update on duplicate key
				$gpo="";
				foreach($datos as $campo => $valor){
					$gpo.="$campo='$valor',";
				}
				$gpo="ON DUPLICATE KEY UPDATE ".trim($gpo,",");
				
				//corre la consulta
				$campos=trim($campos,",");
				$values=substr($values, 0, -1);
				$sql="INSERT INTO $tabla ($campos) VALUES ($values) $gpo;";
				
				//Excepción: para evitar el duplicado en la tabla usuario_contacto
				if($tabla=="usuarios_contacto"){
					$id_usuario=$datos["id_usuario"];
					$sql=str_replace("id_usuario,id_usuario,","id_usuario,",$sql);
					$sql=str_replace("('$userid','$userid',","('$id_usuario',",$sql);
				}elseif($tabla=="usuarios_permisos"){
					$id_usuario=$datos["id_usuario"];
					$id_permiso=$datos["id_permiso"];
					$sql=str_replace("id_usuario,id_permiso,id_usuario,","id_permiso,id_usuario,",$sql);
					$sql=str_replace("('$id_usuario','$id_permiso','$id_usuario',","($id_permiso,'$id_usuario',",$sql);
					$sql=str_replace("id_permiso='$id_permiso',","",$sql);
				}
				
				$res=$bd->exec($sql);
				
				if($primero){		
				//corre la consulta para obtener el dato pivote id
					$campoPivote=$pivotes[$tabla];
					
					//excepciones para el campo clave
					switch($tabla){
						case 'usuarios':
							$campoClave="usuario";
							$clave=$datos["usuario"];
						break;
					}
					
					$sql="SELECT $campoPivote FROM $tabla WHERE id_empresa=$empresaid AND $campoClave ='$clave';";
					if($ocupaUser[$tabla]){
						$sql="SELECT $campoPivote FROM $tabla WHERE id_usuario=$userid AND id_empresa=$empresaid AND $campoClave ='$clave';";
					}
					$res=$bd->query($sql);
					$res=$res->fetchAll(PDO::FETCH_ASSOC);
					$id=$res[0][$campoPivote];//*/
					$primero=false;
				}
			}
			$bd->commit();
			$r["continuar"]=true;
			$r["info"]="Registro añadido satisfactoriamente";
		}catch(PDOException $err){
			$bd->rollBack();
			$r["continuar"]=false;
			$r["info"]="Error encontrado: ".$err->getMessage()."\n<br />$sql";
			$r["datos"]=$datos;
		}//*/
	break;
	
	
	//metodo para individual
	case 'individual':
		$campoClave="clave";
		//asigna el nombre de la tabla
		$tabla=str_replace("f_","",$_POST["tabla"]);
		
		//excepciones para el campo clave
		switch($tabla){
			case 'usuarios':
				$campoClave="usuario";
			break;
		}
		
		//ajusta los datos de los formularios
		$datos=$_POST["datos"];
		$campos="id_empresa,";
		$values="$empresaid,";
		foreach($datos as $ind=>$val){
			$campos.="$ind,";
			$values.="'$val',";
		}
		$campos=trim($campos,",");
		$values=substr($values, 0, -1);
		
		$sqlInsert="INSERT INTO $tabla ($campos) VALUES ($values);";
		try{
			$bd=new PDO($dsnw, $userw, $passw, $optPDO);
			$bd->beginTransaction();
			
			//Checa si la clave ya ha sido registrada
			if(isset($datos["clave"])){
				$clave=$datos["clave"];
				$sql="SELECT * FROM $tabla WHERE id_empresa=$empresaid AND $campoClave ='$clave';";
				$res=$bd->query($sql);
				if($res->rowCount()>0){
					$r["continuar"]=false;
					$r["info"]="Error encontrado:<br />La clave: $clave, ya existe";
					echo json_encode($r);
					exit;
				}
			}
			
			//guarda el registro
			$bd->query($sqlInsert);
			$r["continuar"]=true;
			$r["info"]="Nuevo registro en $tabla añadido satisfactoriamente";
			$bd->commit();
		}catch(PDOException $err){
			$bd->rollBack();
			$r["continuar"]=false;
			$r["info"]="Error encontrado: ".$err->getMessage()."<br />".$sql;
		}
	break;
	
	//default que no hay ningun metodo
	default:
		var_dump($_POST);
	break;

}

echo json_encode($r);

?>