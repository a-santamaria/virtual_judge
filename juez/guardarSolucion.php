<?php
include_once "config.php";

function guardarSolucion($usuario, $problema, $aprobadas, $total) {

    $con = connection_query();
    $result = mysqli_query($con,
                "SELECT id, usuario, problema, fecha, aprobadas, totalPruebas
                 FROM usuario_problema
                 WHERE usuario = '$usuario' AND problema = '$problema'");
    $entro = false;

    $aprobadasAntes = 0;
    $totalAntes = 1;
    //TODO BUSCAR LA MEJOR
    $lastId = -1;
    $mayor = 0;
    while($row = mysqli_fetch_array($result)) {
        if($row['aprobadas'] / $row['totalPruebas'] > $mayor){
            $aprobadasAntes = $row['aprobadas'];
            $totalAntes = $row['totalPruebas'];
            $mayor = $aprobadasAntes/$totalAntes;
        }
        $entro = true;
        if($row['id'] > $lastId){
            $lastId = $row['id'];
        }
    }
    mysqli_close($con);
    $con = connection_update();
    $fecha = date('Y-m-d');

    /*echo "INSERT INTO usuario_problema (usuario, problema, fecha, aprobadas, totalPruebas, archivo)
            VALUES ('$usuario', '$problema', now(), '$aprobadas', '$total', '$ar')";
            */
    $lastId = $lastId + 1;
    $nombreArchivo = $problema . $lastId . '.zip';
    $dir = 'uploads/'. $usuario;

    exec( 'if [ ! -d "' . $dir . '" ]; then
            mkdir '. $dir .
          '; fi');
    exec('mv uploads/api.zip ' . $dir . '/' . $nombreArchivo );

    $sql = "INSERT INTO usuario_problema (usuario, problema, fecha, aprobadas, totalPruebas, archivo)
            VALUES ('$usuario', '$problema', now() , '$aprobadas', '$total', '$nombreArchivo')";

   $retval = mysql_query( $sql );
   if(! $retval )
   {
     die('Could not enter data: ' . mysql_error());
   }


    if($entro){
        if($aprobadas/$total > $aprobadasAntes/$totalAntes){
            echo "<p>Ya habias completado este problema,
                <font color='green'> pero tu puntaje mejoró!!</font> </p>";

        }else{
            echo "<p>Ya habias completado este problema,
                <font color='red'> tu puntaje no mejoró :( </font> </p>";
        }
    }else{
        echo "<p>Felicitaciones has hecho un problema más, has ganado puntos.</p>";

    }

}
