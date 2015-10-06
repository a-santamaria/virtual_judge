<?php
include_once "config.php";
include_once "guardarSolucion.php";


$mensaje = 'sin cambiar';

//COnectamos a base de datos.
$con = connection_query();


$usuario = $_POST["usuario"];
$pass = sha1($_POST["pass"]);

$result = mysqli_query($con,
                    "SELECT usuario, password
                     FROM Usuario
                     WHERE usuario = '$usuario' AND password = '$pass'");
$entro = false;

while($row = mysqli_fetch_array($result)) {
  $entro = true;
}

if(!$entro){
//	die('El usuario o la contraseña son incorrectos ');
}



$target_path = "uploads/";

//Saber cual es el problema
$problema = $_POST["problema"];


$result = mysqli_query($con,
                        "SELECT id, nombre, lenguaje
                         FROM problema WHERE id = $problema");

$problema_nombre = 'Sin_nombre';
$lenguaje = "ninguno";
while($row = mysqli_fetch_array($result)) {
  $problema_nombre = $row['nombre'];
  $lenguaje = $row['lenguaje'];
}
?>


<?php
$html = file_get_contents('header.html');
echo $html;

?>
<div class="col-xs-12 col-sm-8 col-md-8">
          <h2><font color='#426E8A'>Resultados</font></h2>
          <p>
					<?php
					if(!$entro){
						die('El usuario o la contraseña son incorrectos ');
					}
					echo "<br> Está intentando resolver el problema: " . $problema_nombre . '<br><br><br>';

					//Subir el archivo al servidor, todos con el mismo nombre, para que no se llene.

					//$target_path = $target_path . basename( $_FILES['uploadedfile']['name']);
					$target_path = $target_path . 'api.zip';

					if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
						//echo "El archivo ".  basename( $_FILES['uploadedfile']['name']).
						//" ha sido subido al servidor con éxito<br>";
					} else{
						die ('Hubo un problema subiendo el archivo al servidor, por favor intenta de nuevo.');
					}
                    $compilationError = false;
					if($lenguaje == "cpp"){
						//descomprimir el archivo
						exec('unzip uploads/api.zip -d uploads/api');

                        //copiar prueba unitaria a la carpeta api
                        exec('cp problemas/' . $problema_nombre . '.cpp' .
                             ' uploads/api/' . $problema_nombre . '.cpp');

                        //compilar con prueba unitaria
                        exec('g++ -o uploads/api/unitTest uploads/api/*.cpp -lcppunit', $compilacion, $return);
                        echo "retrono de compilar " . $return . "<br>";

                        if($return == 1){

                            echo "<font color='red'> Compilation Error!! </font>
                                 <br>
                                 Recuerda que el nombre del archivo .h debe ser lista.h
                                 <br>
                                 Prueba compilar tu TAD antes de enviarlo
                                 <br>";
                            $compilationError = true;
                            break;
                        }else{
                            //ejecutarPruebas
                            $salida = "";
                            exec('timeout -k 2 2  uploads/api/unitTest > uploads/api/salida.txt', $salida, $return);

                            echo "retrono de ejecutar " . $return . "<br>";
                            for($j = 0; $j < count($salida); $j++)
                            {
                                print $salida[$j];
                                echo '<br>';
                            }

                            if($return == 124){
                                echo "<font color='red'> Time Limit!! </font>
                                      <br>";
                            }else if($return == 0){
                                //$contBienNormal++;

                            }else if($return == 127){
                                //no se
                            }else if($return == 1){
                                //retorno de que no sirvieron todaas las pruebas
                            }else{
                                /* echo "return " . $return . "<br>";
                                echo "<font color='red'> Runtime Error!! </font>
                                      <br>";
                                */
                            }

                            $FeedBackXML = file_get_contents('cppTestResults.xml');
                            //echo $FeedBackXML;

                            $xml = new SimpleXMLElement($FeedBackXML);


                            foreach ($xml->FailedTests->FailedTest as $failed) {
                                echo "-->Prueba: " . (string) $failed->Name . "<br>";
                                echo (string)$failed->Message . "<br>";
                                echo "<font color='red'>Fallada</font><br><br>";
                            }

                            foreach ($xml->SuccessfulTests->Test as $passed) {
                                echo "-->Prueba: " . (string) $passed->Name . "<br>";
                                echo "<font color='green'>Aprobada</font><br><br>";
                            }

                            echo "----------------Estadisticas-----------------<br>";
                            $numTests = (int)$xml->Statistics->Tests;
                            $numFails = (int)$xml->Statistics->FailuresTotal;
                            echo 'Pruebas: ' . (string)$xml->Statistics->Tests . ' ';
                            echo 'Errores: ' . (string)$xml->Statistics->Errors . ' ';
                            echo 'Fallas: ' . (string)$xml->Statistics->Failures . ' ';
                            //echo 'Total Fallas: ' . (string)$xml->Statistics->FailuresTotal. '<br>';

                        }
                        //guardar en la base de datos
                        guardarSolucion($usuario, $problema_nombre,
                                        $numTests-$numFails, $numTests);

                        //delete everything inside api folder
                        exec('rm -r uploads/api/*');
                        //delete xml results file
                        exec('rm cppTestResults.xml');
					}
                    //delete zip uploaded
                    exec('rm uploads/api.zip');

					/**
					Converts to unix format
					*/
					/*$file = file_get_contents("uploads/code.dis");
					$file = str_replace("\r", "", $file);
					file_put_contents("uploads/code.dis", $file);
					*/


					?>

          </p>


        </div>
    <div class="cleaner">&nbsp;</div>
  </div>
</div>
<div id="footer" class="navbar navbar-default navbar-fixed-bottom">
  <div class="container">
    Juez creado por: Daniel Serrano
    <br>
    Adaptado por: Alfredo Santamaria
  </div>
</div>
</html>
