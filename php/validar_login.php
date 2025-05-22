<?php
session_start();
$host="localhost";
$usu="root";
$password = "";
$database = "toymeetpagina";
$conn = new mysqli($host, $usu, $password, $database);
/*if($conn===false){//comparacion de tipo e igual dato//
    die('No se pudo establecer la conexion a la base de datos');
}else{

}*/
if($conn==false){
    die('No se pudo establecer la conexion a la base de datos');
}else{
   
}
$nombre_usuario = $_POST['Nickname'];
$contrasena = $_POST['contrasena'];

$sql1="select * from  usuario";
$resultado = $conn->query($sql1);
$errorlogin=0;
$usuarioencontrado=0;
if(mysqli_num_rows($resultado)>0){
    while($row = mysqli_fetch_assoc($resultado)){
       
        if($row["nickname"]==$nombre_usuario && $row["contrasena"]==$contrasena){
            $_SESSION['id_usuario'] = $row['Id_usuario'];
            $_SESSION['nickname'] = $row['nickname'];
            header("Location: ../CodePage/index.php");
            exit();
        }else{
            if($row["nickname"]==$nombre_usuario){
                $usuarioencontrado++;
            }else{
                $errorlogin++;
            }
            
        }
    }
}else{
    echo "usuario o contrasena incorrectos";
}
if($usuarioencontrado!=0){
echo'contrasena incorrecta';
}else{
    if($errorlogin!=0){
    echo "usuario o contrasena incorrectos";
}
}



