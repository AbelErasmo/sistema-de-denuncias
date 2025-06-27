<?php
    $target_dir = "uploads/";
    $target_file = $target_dir .basename($_FILES["file"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if (isset($_POST["submit"])) {
        $check = getimagesize($_FILES["file"]["tmp_name"]);
        if ($check !== false) {
            echo"O ficheiro é uma imagem - ". $check["mine"] . ".";
            $uploadOk = 1;
        } else {
            echo "O ficheiro nao e uma imagem";
            $uploadOk = 0;
        }
    }

    /* Este metodo verifica-se o ficheiro existe*/
    if (file_exists($target_file)) {
        echo "O ficheiro ja existe.";
        $uploadOk = 0;
    }

    // Este verifica o tamanho do ficheiro
    if ($_FILES["file"]["size"] > 500) {
        echo "O ficheiro ultrapassa o tamanho maximo permitido";
        $uploadOk = 0;
    }

    /* Permitindo outros tipos */
    if ($imageFileType != "jpg" && $imageFileType != "png") {
        echo "Apenas os ficheiros JPG, JPEG, PNG sao permitidos";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "Desculpe, o ficheiro nao foi carregado.";
    } else {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            echo "Ficheiro " . htmlspecialchars(basename($_FILES["file"]["name"])) ." foi carregado com sucesso.";
        } else {
            echo "Deu um bug ao carregar o ficheiro.";
        }
    }

?>