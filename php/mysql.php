<?php
$host="localhost";
$usu="root";
$password = "";
$database = "toymeet";
$conn = new mysqli($host, $usu, $password, $database);
/*if($conn===false){//comparacion de tipo e igual dato//
    die('No se pudo establecer la conexion a la base de datos');
}else{

}*/
if($conn==false){
    die('No se pudo establecer la conexion a la base de datos');
}else{
    echo 'Bienvenida a la base de datos'."<br>";
}
$sql1="select * from  vendedor where estado='activo' and ciudad='Lima'";
$resultado = $conn->query($sql1);
if(mysqli_num_rows($resultado)>0){
    while($row = mysqli_fetch_assoc($resultado)){
        echo"id:".$row["Id_vendedor"]."-estado:".$row["estado"]."-tiempo".$row["tiempo"]."-ciudad:".$row["ciudad"]."-Id_usuario".$row["Id_usuario"]."<br>";
    }
}else{
    echo "No hay datos";
}
echo "------------------------------------------------------------------------------------"."<br>";
/*$sql2="select * from  usuario where nombre like 'A%'";
$resultado2 = $conn->query($sql2);
if(mysqli_num_rows($resultado2)>0){
    while($row = mysqli_fetch_assoc($resultado)){
        echo"Id:".$row["Id_usuario"]."-nombre:".$row["nombre"]."-contrasena:".$row["contrasena"]."-correo:".$row["correo"]."-ApPaterno".$row["ap_paterno"]."-ApMaterno:".$row["ap_materno"]."-nacimiento:".$row["fecha_nac"]."-Celular".$row["nro_celular"]."-rol:".$rol["rol"]."foto_perfil".$rol["foto_perfil"]."<br>";
    }
}else{
    echo "No hay datos";
}*/
$sql2="select * from  categoria where nombre like 'E%'";
$resultado2 = $conn->query($sql2);
if(mysqli_num_rows($resultado2)>0){
    while($row = mysqli_fetch_assoc($resultado2)){
        echo"Id:".$row["Id_categoria"]."-nombre:".$row["nombre"]."-descripcion".$row["descripcion"]."<br>";
    }
}else{
    echo "No hay datos";
}
echo "------------------------------------------------------------------------------------"."<br>";
$sql3="select * from  calificacion_vendedor where puntuacion between 3 and 5";
$resultado3 = $conn->query($sql3);
if(mysqli_num_rows($resultado3)>0){
    while($row = mysqli_fetch_assoc($resultado3)){
        echo"Id:".$row["Id_calificacion"]."-puntuacion:".$row["puntuacion"]."-criterio:".$row["criterio"]."Idus:".$row["Id_usuario"]."Idven:".$row["Id_vendedor"]."<br>";
    }
}else{
    echo "No hay datos";
}