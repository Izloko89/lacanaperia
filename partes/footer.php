    </div><!-- //div tag con clase fila para centrar el contenido -->
    <div id="footer" class="fila" style="height:100px;">
      <div id="footer_arriba" class="fondo_gris linea_arriba_gris tabla">
        <div class="footer_contenido fila">
		
		<?php if($_SESSION["usuario"] != "empleados")
		{ ?>
		
          <div class="celda centrado_v">
            <span class="link" data-url="home.php">Inicio</span>/
            <span class="link" data-url="cotizaciones.php">Cotizaciones</span>/
            <span class="link" data-url="eventos.php">Eventos</span>/
            <span class="link" data-url="almacen.php">Almacén</span>/
            <span class="link" data-url="compras.php">Compras</span>/
            <span class="link" data-url="bancos.php">Bancos</span>/
            <span class="link" data-url="modulos.php">Módulos</span>/
            <span class="link" data-url="scripts/logout.php">Cerrar Sesión</span>
          </div>
		<?php } else {?>
		  <div class="celda centrado_v">
           <!-- <span class="link" data-url="home.php">Inicio</span>/ -->
          
            <span class="link" data-url="modulos.php">Módulos</span>/
            <span class="link" data-url="scripts/logout.php">Cerrar Sesión</span>
          </div>
		  <?php } ?>
		  
		  
        </div>
      </div>
      <div id="footer_abajo" class="fondo_gris_dos tabla">
        <div class="footer_contenido fila">
          <div class="celda centrado_v"></div>
        </div>
      </div>
    </div>
</div>
</body>
</html>