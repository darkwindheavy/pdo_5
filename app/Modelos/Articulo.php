<?php
require_once 'BaseDeDatos.php';

class Articulo {
    private $db;

    public function __construct($db) {
        if ($db instanceof BaseDeDatos) {
            $this->db = $db;
        } else {
            throw new Exception("La clase Articulo necesita una instancia válida de BaseDeDatos.");
        }
    }
    

    public function crearArticulo($codigo, $nombre, $descripcion, $categoria, $precio, $imagen) {
        // Verificar si el código ya existe
        if ($this->verificarCodigoDuplicado($codigo)) {
            throw new Exception("El código ya está en uso. Por favor, elija otro.");
        }
    
        $conexion = $this->db->getConexion(); // Obtener la conexión
        $sql = "INSERT INTO articulos (codigo, nombre, descripcion, categoria, precio, imagen) VALUES (:codigo, :nombre, :descripcion, :categoria, :precio, :imagen)";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':categoria', $categoria);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':imagen', $imagen);
        $stmt->execute();
    }
    

    // Método para editar un artículo existente
public function editarArticulo($id, $codigo, $nombre, $descripcion, $categoria, $precio, $imagen = null) {
    try {
        // Obtener la conexión desde la instancia de BaseDeDatos
        $conexion = $this->db->getConexion();

        // Verificar si el código ya existe en otro artículo
        if ($this->verificarCodigoDuplicado($codigo, $id)) {
            throw new Exception("El código ya está en uso por otro artículo. Por favor, elija otro.");
        }

        $sql = "UPDATE articulos SET codigo = :codigo, nombre = :nombre, descripcion = :descripcion, categoria = :categoria, precio = :precio";
        
        if ($imagen !== null) {
            $sql .= ", imagen = :imagen";
        }
        
        $sql .= " WHERE id = :id";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':categoria', $categoria);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($imagen !== null) {
            $stmt->bindParam(':imagen', $imagen);
        }

        $stmt->execute();
    } catch (PDOException $e) {
        throw new Exception("Error al editar el artículo: " . $e->getMessage());
    }
}

    

    // Método para eliminar un artículo existente
    public function eliminarArticulo($id) {
        $conexion = $this->db->getConexion(); // Obtener la conexión
        $sql = "DELETE FROM articulos WHERE id = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function listarArticulos($busqueda = '', $limite = 10, $offset = 0, $orden = 'nombre', $direccion = 'ASC') {
        // Validar los campos de orden y dirección
        $camposPermitidos = ['codigo', 'nombre', 'descripcion', 'categoria', 'precio'];
        if (!in_array($orden, $camposPermitidos)) {
            $orden = 'nombre'; // Valor por defecto
        }
        if (!in_array(strtoupper($direccion), ['ASC', 'DESC'])) {
            $direccion = 'ASC'; // Valor por defecto
        }
    
        // Construir la consulta
        $conexion = $this->db->getConexion();
        $sql = "SELECT * FROM articulos WHERE CONCAT(codigo, ' ', nombre, ' ', descripcion, ' ', categoria) LIKE :busqueda 
                ORDER BY $orden $direccion LIMIT :limite OFFSET :offset";
    
        $stmt = $conexion->prepare($sql);
    
        // Vincular parámetros
        $busqueda_param = "%" . $busqueda . "%";
        $stmt->bindParam(':busqueda', $busqueda_param, PDO::PARAM_STR);
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    
        // Ejecutar y devolver resultados
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    

    // Método para verificar si el código ya existe (evitar duplicados)
    private function verificarCodigoDuplicado($codigo, $id = null) {
        $conexion = $this->db->getConexion(); // Obtener la conexión

        $sql = "SELECT COUNT(*) FROM articulos WHERE codigo = :codigo";
        
        if ($id !== null) {
            $sql .= " AND id != :id";
        }

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);

        if ($id !== null) {
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        }

        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }





// Método para contar el total de artículos para la paginación
public function contarArticulos($busqueda = '') {
    try {
        // Obtener la conexión desde la instancia de BaseDeDatos
        $conexion = $this->db->getConexion();

        // Consulta SQL para contar artículos
        $sql = "SELECT COUNT(*) as total FROM articulos WHERE CONCAT(codigo, ' ', nombre, ' ', descripcion, ' ', categoria) LIKE :busqueda";
        $stmt = $conexion->prepare($sql);

        // Parámetro de búsqueda
        $busqueda_param = "%" . $busqueda . "%";
        $stmt->bindParam(':busqueda', $busqueda_param, PDO::PARAM_STR);
        $stmt->execute();

        // Obtener el resultado
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Validar el resultado y devolver un número válido
        return $result && isset($result['total']) ? (int) $result['total'] : 0;
    } catch (PDOException $e) {
        // Registrar el error y lanzar una excepción
        error_log("Error al contar los artículos: " . $e->getMessage());
        throw new Exception("Error al contar los artículos: " . $e->getMessage());
    }
}


public function obtenerArticuloPorId($id) {
    try {
        // Obtener la conexión desde la instancia de BaseDeDatos
        $conexion = $this->db->getConexion();

        $sql = "SELECT * FROM articulos WHERE id = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        throw new Exception("Error al obtener el artículo: " . $e->getMessage());
    }
}

public function borrarArticulo($id) {
    try {
        // Obtener la conexión desde la instancia de BaseDeDatos
        $conexion = $this->db->getConexion();

        $sql = "DELETE FROM articulos WHERE id = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    } catch (PDOException $e) {
        throw new Exception("Error al borrar el artículo: " . $e->getMessage());
    }
}

public function validarArticulo($codigo, $imagen) {
    // Validar el patrón del código
    if (!preg_match('/^[a-zA-Z]{3}\d{1,5}$/i', $codigo)) {
        throw new Exception("El código debe contener tres letras seguidas de hasta cinco números.");
    }

    // Validar el tamaño de la imagen
    if ($imagen['size'] > 300 * 1024) {
        throw new Exception("La imagen debe ser menor de 300 KB.");
    }

    // Validar el tipo MIME de la imagen
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($imagen['type'], $allowedMimeTypes)) {
        throw new Exception("El tipo de archivo de la imagen no es permitido. Solo se permiten JPG, PNG o GIF.");
    }
}

public function procesarImagen($imagen, $uploadDir = '../public/uploads/articulos/') {
    // Crear el directorio si no existe
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Obtener información de la imagen
    $imageInfo = getimagesize($imagen['tmp_name']);
    $nuevoNombre = uniqid() . '_' . basename($imagen['name']);
    $rutaDestino = $uploadDir . $nuevoNombre;

    // Redimensionar si excede 200x200 píxeles
    if ($imageInfo[0] > 200 || $imageInfo[1] > 200) {
        $this->redimensionarImagen($imagen['tmp_name'], $rutaDestino, 200, 200);
    } else {
        if (!move_uploaded_file($imagen['tmp_name'], $rutaDestino)) {
            throw new Exception("Error al subir la imagen.");
        }
    }

    return $nuevoNombre; // Devolver el nombre del archivo
}



public function redimensionarImagen($rutaOriginal, $rutaDestino, $anchoMax, $altoMax) {
    $info = getimagesize($rutaOriginal);
    list($anchoOriginal, $altoOriginal) = $info;

    $tipo = $info[2];
    $ratio = min($anchoMax / $anchoOriginal, $altoMax / $altoOriginal);

    $nuevoAncho = $anchoOriginal * $ratio;
    $nuevoAlto = $altoOriginal * $ratio;

    $imagenRedimensionada = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

    switch ($tipo) {
        case IMAGETYPE_JPEG:
            $imagenOriginal = imagecreatefromjpeg($rutaOriginal);
            break;
        case IMAGETYPE_PNG:
            $imagenOriginal = imagecreatefrompng($rutaOriginal);
            break;
        case IMAGETYPE_GIF:
            $imagenOriginal = imagecreatefromgif($rutaOriginal);
            break;
        default:
            throw new Exception("Formato de imagen no soportado.");
    }

    imagecopyresampled($imagenRedimensionada, $imagenOriginal, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $anchoOriginal, $altoOriginal);

    switch ($tipo) {
        case IMAGETYPE_JPEG:
            imagejpeg($imagenRedimensionada, $rutaDestino, 90);
            break;
        case IMAGETYPE_PNG:
            imagepng($imagenRedimensionada, $rutaDestino);
            break;
        case IMAGETYPE_GIF:
            imagegif($imagenRedimensionada, $rutaDestino);
            break;
    }

    imagedestroy($imagenRedimensionada);
    imagedestroy($imagenOriginal);
}




}





