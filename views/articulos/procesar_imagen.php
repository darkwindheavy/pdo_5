<?php
session_start();
require_once '../app/Controladores/funciones.php'; // Importar funciones reutilizables
require_once '../views/includes/header.php';

// Verificar si el usuario ha iniciado sesión
verificar_sesion_y_rol(['usuario', 'administrador', 'editor']); // Permitir a todos los roles acceder

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se ha subido un archivo
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['imagen']['tmp_name'];
        $fileName = $_FILES['imagen']['name'];
        $fileSize = $_FILES['imagen']['size'];
        $fileType = $_FILES['imagen']['type'];
        $anchura = intval($_POST['anchura']);
        $altura = intval($_POST['altura']);

        // Verificar si el archivo es una imagen
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($fileType, $allowedMimeTypes)) {
            // Obtener información de la imagen
            $imageInfo = getimagesize($fileTmpPath);
            if ($imageInfo !== false) {
                // Crear una imagen desde el archivo original
                switch ($imageInfo[2]) {
                    case IMAGETYPE_JPEG:
                        $image = imagecreatefromjpeg($fileTmpPath);
                        break;
                    case IMAGETYPE_PNG:
                        $image = imagecreatefrompng($fileTmpPath);
                        break;
                    case IMAGETYPE_GIF:
                        $image = imagecreatefromgif($fileTmpPath);
                        break;
                    default:
                        die("Formato de imagen no soportado.");
                }

                // Redimensionar la imagen
                $imagenRedimensionada = imagescale($image, $anchura, $altura);

                // Generar un nombre único para evitar colisiones
                $nuevoNombre = uniqid() . '_' . basename($fileName);
                $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/PDO_5_MVC/public/uploads/images/';;
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $uploadPath = $uploadDir . $nuevoNombre;

                // Guardar la imagen redimensionada en el directorio uploads/images/
                switch ($imageInfo[2]) {
                    case IMAGETYPE_JPEG:
                        imagejpeg($imagenRedimensionada, $uploadPath);
                        break;
                    case IMAGETYPE_PNG:
                        imagepng($imagenRedimensionada, $uploadPath);
                        break;
                    case IMAGETYPE_GIF:
                        imagegif($imagenRedimensionada, $uploadPath);
                        break;
                }

                // Mostrar mensaje de éxito al usuario con botones para ver la imagen y volver al formulario
                echo '<div class="edit-container">';
                echo '<p>Imagen subida exitosamente.</p>';
                echo '<div class="button-container">';
                echo '<a href="' . $uploadPath . '" target="_blank" class="btn-primary">Ver Imagen</a>';
                echo '<a href="/PDO_5_MVC/public/index.php?page=misc/subir_imagen" class="btn-secondary">Subir Otra Imagen</a>';
                echo '</div>';
                echo '</div>';

                // Liberar la memoria
                imagedestroy($image);
                imagedestroy($imagenRedimensionada);
            } else {
                echo "El archivo subido no es una imagen válida.";
            }
        } else {
            echo "Tipo de archivo no permitido. Solo se permiten imágenes JPEG, PNG o GIF.";
        }
    } else {
        echo "Error al subir el archivo.";
    }
} else {
    echo "Método de solicitud no válido.";
}

require_once '../views/includes/footer.php'; // Importar el pie de página común




