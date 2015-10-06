<?php
	include "config.php";
?>

<?php
$html = file_get_contents('header.html');
echo $html;

?>


          <h2>Agregar Problema</h2>
          <p>


					<?php


					$con = connection_query();



					$usuario = $_POST["usuario"];
					$pass = sha1($_POST["pass"]);

					$result = mysqli_query($con, "SELECT usuario, password FROM admon WHERE usuario = '$usuario' AND password = '$pass'");
					$entro = false;

					while($row = mysqli_fetch_array($result)) {
					  $entro = true;
					}

					if(!$entro){
						die('El usuario o la contraseña son incorrectos ');
					}


					mysqli_close($con);

					$con = connection_update();



					$nombre = $_POST["nombre"];
					$desc = $_POST["descripcion"];
					$fecha = $_POST["fecha"];
					$lenguaje = "cpp";
					$archivo = $nombre . ".cpp";

					  $sql = "INSERT INTO problema (nombre, descripcion, codigo, fecha_maxima, lenguaje)
					  VALUES ('$nombre', '$desc','$archivo', '$fecha', '$lenguaje')";

					$retval = mysql_query( $sql );
					if(! $retval )
					{
					  die('Could not enter data: ' . mysql_error());
					}





					//Subir el codigo del problema
					$target_path = "problemas/";

					//$target_path = $target_path . basename( $_FILES['uploadedfile']['name']);
					$target_path = $target_path . $archivo;

					if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
						echo "The file ".  basename( $_FILES['uploadedfile']['name']).
						" has been uploaded";
					} else{
						echo "There was an error uploading the file, please try again!";
					}

					//Converts to unix format
					//$file = file_get_contents("problemas/code.dis");
					//$file = str_replace("\r", "", $file);
					//file_put_contents("problemas/code.dis", $file);




					echo "Problema insertado con exito" ;
					// some code

					mysql_close($con);


					?>
          </p>


        </div>
      </div>
    </div>
    <div class="cleaner">&nbsp;</div>
  </div>
</div>
<br><br><br>
<div id="footer" class="navbar navbar-default navbar-fixed-bottom">
  <div class="container">
    Juez creado por: Daniel Serrano
    <br>
    Adaptado por: Alfredo Santamaria
  </div>
</div>
</html>
