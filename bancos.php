<?php include("partes/header.php"); 
include("scripts/permisos.php");
include("scripts/funciones.php"); 
            $bd=new PDO($dsnw,$userw,$passw,$optPDO);
            
            $total=0;
?>
<script src="js/bancos.js"></script>
<div id="contenido">
    <div class="formularios">
        <h3 class="titulo_form">Listado de Bancos</h3>
        <table width="100%">
        <?php
        try{
            $sql="SELECT * FROM bancos WHERE id_empresa=$empresaid;";
            $res=$bd->query($sql);
            $tabla="<tr>
                <th>Banco</th>
                <th>Cuenta</th>
                <th>Clave</th>
                <th>Acciones</th>
            </tr>";
            $bancos=array();
            foreach($res->fetchAll(PDO::FETCH_ASSOC) as $d){
                $tabla.='<tr>';
                $tabla.='<td>'.$d["nombre"].'</td>';
                $tabla.='<td>'.$d["cuenta"].'</td>';
                $tabla.='<td>'.$d["clabe"].'</td>';
                $tabla.='<td style="width:200px;">';
                $tabla.='<input type="button" value="Edo cuenta" onClick="edocuenta(this);" data-id="#banco'.$d["id_banco"].'" />';
                $tabla.='</td>';
                $tabla.='</tr>';
                $bancos[$d["id_banco"]]=$d;
            }
            echo $tabla;
        }catch(PDOException $err){
            echo "Error: ".$err->getMessage();
        }
        ?>
        </table>
    </div>
  <?php foreach($bancos as $i=>$d){ 
            
            $total=0;?>
            <center>
    <table id="banco<?php echo $i; ?>" class="edocuenta" style="display:none;">
        <tr>
            <td colspan="10"><h1>Estado de Cuenta</h1></td>
        </tr>
        <tr>
            <th style="padding-left: 50px;padding-right: 50px;">Nombre de banco</th>
            <th>Cuenta</th>
            <th>Clave</th>
        </tr>
        <tr>
            <td><?php echo $d["nombre"] ?></td>
            <td><?php echo $d["cuenta"] ?></td>
            <td><?php echo $d["clabe"] ?></td>
        </tr>
        <tr>
            <td style="padding-left: 20px;padding-right: 20px;"><h2>Fecha</h2></td>
            <td style="padding-left: 20px;padding-right: 20px;"><h2>Concepto</h2></td>
            <td><h2>Ingreso</h2></td>
            <td style="padding-left: 50px;padding-right: 50px;"><h2>Egreso</h2></td>
            <td style="padding-left: 50px;padding-right: 50px;"><h2>Saldo</h2></td>
        </tr>
        <?php //aquí van los movimientos del banco 
            try{
            $bd=new PDO($dsnw,$userw,$passw,$optPDO);
                $banco=$d["id_banco"];
                $mov=array();
                $sql="SELECT
                        ep.fecha,
                        e.nombre,
                        ep.cantidad AS ingreso
                    FROM eventos AS e
                    LEFT JOIN eventos_pagos AS ep ON CONCAT('1_', e.id_evento) = ep.id_evento
                        WHERE ep.id_banco = $banco
                        ORDER BY ep.fecha ASC;";
                $res=$bd->query($sql);
                    ?>
        <?php //aquí van los movimientos del banco 
                foreach($res->fetchAll(PDO::FETCH_ASSOC) as $dd){
                    ?>
        <tr>
            <td><?php echo $dd["fecha"]; ?></td>
            <td><?php echo $dd["nombre"]; ?></td>
            <td><?php $total=$total+$dd["ingreso"]; echo $dd["ingreso"]; ?></td>
            <td><?php ?></td>
            <td><?php  if($total < 0) { echo '<font color="red">' . $total . '</font>';} elseif($total >= 0){echo  $total;} ?></td>
        </tr>
        <?php
                }
            }catch(PDOException $err){
                echo $err->getMessage();
            }
            $bd=NULL;
        ?>
        
        
        <?php //aquí van los movimientos del banco 
            try{
            $bd=new PDO($dsnw,$userw,$passw,$optPDO);
                $banco=$d["id_banco"];
                $mov=array();
                $sql="SELECT
                        cp.fecha,
                        e.nombre,
                        cp.monto AS egreso
                    FROM eventos AS e
                    LEFT JOIN compras AS c ON c.id_evento = e.id_evento
                    LEFT JOIN compras_pagos AS cp ON cp.id_compra = c.id_compra
                        WHERE cp.id_banco = $banco
                        ORDER BY cp.fecha ASC;";
                $res=$bd->query($sql);
                    ?>
        <?php //aquí van los movimientos del banco 
                foreach($res->fetchAll(PDO::FETCH_ASSOC) as $dd){
                    ?>
        <tr>
            <td><?php echo $dd["fecha"]; ?></td>
            <td><?php echo $dd["nombre"]; ?></td>
            <td><?php ?></td>
            <td><?php $total=$total-$dd["egreso"]; echo $dd["egreso"]; ?></td>
            <td><?php  if($total < 0) { echo '<font color="red">' . $total . '</font>';} elseif($total >= 0){echo  $total;} ?></td>
        </tr>
        <?php
                }
            }catch(PDOException $err){
                echo $err->getMessage();
            }
            $bd=NULL;
        ?>
        
    </table></center>
    <?php } ?>
</div>
<?php include("partes/footer.php"); ?>