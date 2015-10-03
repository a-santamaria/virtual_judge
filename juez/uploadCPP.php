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
						//descomprimir el archivo y ejecutar cliente
						exec('unzip uploads/api.zip -d uploads/api');

                        // for de test normales
                        exec('ls -1 problemas/' . $problema_nombre . '/normal/ ', $archivos, $return);
                        //echo 'retrun= ' . $return . '<br> para problemas/' . $problema_nombre . '/normal/ ';
                        //echo 'cont= ' . count($archivos) . '<br>';
                        $totalNormal = count($archivos);
                        $contBienNormal = 0;

                        for($i = 0; $i < $totalNormal; $i++){
                            //echo 'nombre= ' . $archivos[$i] . '<br>';
    						exec('cp problemas/' . $problema_nombre . '/normal/'
                                  . $archivos[$i] .
                                 ' uploads/api/' . $problema_nombre . '.cpp');

    						exec('g++ -o clientApi uploads/api/*.cpp', $compilacion, $return);
                            //echo "g++ -o clientApi uploads/api/*.cpp<br>";
                            //echo "retrono de compilar " . $return . "<br>";
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
                                exec('mv clientApi uploads/api');
                                $salida = "";
        						exec('timeout -k 2 2  ./uploads/api/clientApi', $salida, $return);



                                for($j = 0; $j < count($salida); $j++)
                                {
                                    print $salida[$j];
                                    echo '<br>';
                                }

                                //echo "retrono de ejecutar " . $return . "<br>";

                                //retorno assert
                                if($return == 134){
                                    echo "<font color='red'> Assert cuando no se debia llamar </font>
                                          <br>";
                                }else if($return == 124){
                                    echo "<font color='red'> Time Limit!! </font>
                                          <br>";
                                }else if($return == 0){
                                    $contBienNormal++;

                                }else  if($return == -1){
                                    //no aprobo la prueba
                                    echo "<font color='red'> No aprobó la prueba!! </font>
                                          <br>";
                                }else{
                                    echo "return " . $return . "<br>";
                                    echo "<font color='red'> Runtime Error!! </font>
                                          <br>";
                                }
                            }
                            exec('rm uploads/api/' . $problema_nombre . '.cpp');
                        }
                        echo "<br>
                             <font color='green'> " . $contBienNormal . "/" . $totalNormal .
                             " preubas normales aprobadas </font>
                              <br><br>";

                        if(!$compilationError){
                            // for de test assert
                            exec('ls -1 problemas/' . $problema_nombre . '/assert/ ', $archivosAssert, $return);

                            $totalAssert = count($archivosAssert);
                            //echo "total = " . $total . "<br>";
                            $contBienAssert = 0;
                            for($i = 0; $i < $totalAssert; $i++){
                                //echo 'nombre= ' . $archivosAssert[$i] . '<br>';
        						exec('cp problemas/' . $problema_nombre . '/assert/'
                                      . $archivosAssert[$i] .
                                     ' uploads/api/' . $problema_nombre . '.cpp');
                                $compilacion = "";
        						exec('c++ -o clientApi uploads/api/*.cpp', $compilacion, $return);

                                if($return == 1){
                                    echo "<font color='red'> Compilation Error!! </font>
                                         <br>
                                         Recuerda que el nombre del archivo .h debe ser lista.h
                                         <br>
                                         Prueba compilar tu TAD antes de enviarlo
                                         <br>";
                                    //TODO matar todo
                                }else{
                                    exec('mv clientApi uploads/api');
                                    $salida2 = "";
            						exec('timeout -k 2 2 ./uploads/api/clientApi', $salida2, $return2);
                                    for($j = 0; $j < count($salida2); $j++)
                                    {
                                        print $salida2[$j];
                                        echo '<br>';
                                    }
                                    //echo "retrono de ejecutar " . $return2 . "<br>";
                                    //retorno assert
                                    if($return2 == 134){
                                        echo "Preuba de Asser aprobada <br>";
                                              $contBienAssert++;
                                    }else if($return2 == 0){
                                        echo "Se esperaba un Assert en esta prueba!!<br>";

                                    }else if($return2 == 124){
                                        echo "<font color='red'> Time Limit!! </font>
                                              <br>";
                                    }else{
                                        echo "<font color='red'> Runtime Error!! </font>
                                              <br>";
                                    }
                                }
                                exec('rm uploads/api/' . $problema_nombre . '.cpp');
                            }

                            if($totalAssert != 0){
                                echo "<br>
                                     <font color='green'> " . $contBienAssert . "/" . $totalAssert .
                                     " preubas assert aprobadas </font>
                                      <br><br>";
                             }
                        }
                        //guardar en la base de datos
                        guardarSolucion($usuario, $problema_nombre,
                                        $contBienNormal+$contBienAssert, $totalNormal+$totalAssert);
                        //delete everything inside api folder
                        exec('rm -r uploads/api/*');
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
