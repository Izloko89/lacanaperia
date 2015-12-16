<?php
$target_dir = "../img/articulos/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$response["imagen"] = $_FILES["fileToUpload"]["name"];
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        //echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        $response["info"] =  "<br>El archivo no es una imagen.";
        $uploadOk = 0;
    }
}
// Check if file already exists
if (file_exists($target_file)) {
    $response["info"] = "<br>Error, la imagen: ". $target_file ." Ya existe";
    $uploadOk = 0;
}
// Check file size
if ($_FILES["fileToUpload"]["size"] > 500000) {
    $response["info"] = "Error, el archivo es muy grande.";
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    $response["status"] = "<br>Status: Su imagen no fue subida.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        $response["info"] = "<br>La imagen:  ". basename( $_FILES["fileToUpload"]["name"]) . " ha sido agregada.";
        $response["status"] = "<br> La operacion fue todo un exito";

    } else {
        $response["status"] = "<br>Status:, Hubo un error al subir el archivo con el servidor.";
    }
}
echo json_encode($response);
?>