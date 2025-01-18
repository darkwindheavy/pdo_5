<?php
class Usuario {
    private $db;

    public function __construct($db) {
        if ($db instanceof BaseDeDatos) {
            $this->db = $db;
        } else {
            throw new Exception("La clase Articulo necesita una instancia válida de BaseDeDatos.");
        }
    }

    // Método para validar el DNI según la normativa española
    private function validarDni($dni) {
        // Convertir la letra a mayúscula para asegurar la validez
        $dni = strtoupper($dni);
        
        // El DNI debe tener 8 dígitos seguidos de una letra
        if (preg_match('/^[0-9]{8}[A-Z]$/', $dni)) {
            $numero = substr($dni, 0, 8);
            $letra = substr($dni, -1);
            $letras_validas = "TRWAGMYFPDXBNJZSQVHLCKE";
            $letra_correcta = $letras_validas[$numero % 23];
            
            return $letra === $letra_correcta;
        }

        return false;
    }

    private function verificarDuplicado($campo, $valor, $id = null) {
        $conexion = $this->db->getConexion();
        $sql = "SELECT COUNT(*) FROM clientes WHERE $campo = :valor";
        
        // Si se proporciona un ID, lo excluimos de la verificación
        if ($id !== null) {
            $sql .= " AND id != :id";
        }
    
        $stmt = $conexion->prepare($sql);
        $params = [':valor' => $valor];
        if ($id !== null) {
            $params[':id'] = $id;
        }
        $stmt->execute($params);
    
        // Comprobar si existe un duplicado
        if ($stmt->fetchColumn() > 0) {
            switch ($campo) {
                case 'dni':
                    throw new Exception("El DNI ingresado ya existe. Por favor, utilice un DNI diferente.");
                case 'correo':
                    throw new Exception("El correo ingresado ya está registrado. Por favor, utilice un correo diferente.");
                case 'telefono':
                    throw new Exception("El número de teléfono ingresado ya está registrado. Por favor, utilice un número diferente.");
                default:
                    throw new Exception("El valor ingresado para $campo ya existe.");
            }
        }
    }
    
    
    
    private function validarCamposObligatorios($dni, $nombre, $correo, $rol) {
        if (empty($dni) || empty($nombre) || empty($correo) || empty($rol)) {
            throw new Exception("Por favor, complete todos los campos obligatorios.");
        }
        if (!preg_match("/^[0-9]{8}[A-Za-z]$/", $dni)) {
            throw new Exception("El DNI ingresado no es válido. Debe tener 8 dígitos y una letra.");
        }
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("El correo ingresado no es válido.");
        }
        if (!in_array($rol, ['administrador', 'editor', 'usuario'])) {
            throw new Exception("El rol ingresado no es válido. Debe ser 'administrador', 'editor' o 'usuario'.");
        }
    }
    

    public function crearUsuario($dni, $nombre, $correo, $telefono, $direccion, $localidad, $provincia, $rol, $contrasena = null) {
        $conexion = $this->db->getConexion();
    
        // Validar el DNI
        if (!$this->validarDni($dni)) {
            throw new Exception("El DNI no es válido.");
        }
        
        // Validar los campos obligatorios
        $this->validarCamposObligatorios($dni, $nombre, $correo, $rol);
        
        // Verificar duplicados
        $this->verificarDuplicado('dni', $dni);
        if (!empty($telefono)) {
            $this->verificarDuplicado('telefono', $telefono);
        }
        $this->verificarDuplicado('correo', $correo);
        
        // Generar contraseña aleatoria si no se proporciona
        if (!$contrasena) {
            $contrasena = bin2hex(random_bytes(8));
        }
        $hash_contrasena = password_hash($contrasena, PASSWORD_DEFAULT);
    
        // Insertar el usuario en la base de datos
        $sql = "INSERT INTO clientes (dni, nombre, correo, contrasena, telefono, direccion, localidad, provincia, rol) 
                VALUES (:dni, :nombre, :correo, :contrasena, :telefono, :direccion, :localidad, :provincia, :rol)";
        try {
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ':dni' => $dni,
                ':nombre' => $nombre,
                ':correo' => $correo,
                ':contrasena' => $hash_contrasena,
                ':telefono' => $telefono,
                ':direccion' => $direccion,
                ':localidad' => $localidad,
                ':provincia' => $provincia,
                ':rol' => $rol
            ]);

            // Forzar sincronización con phpMyAdmin
            $conexion->query("SELECT * FROM clientes LIMIT 1");
            
            if ($stmt->rowCount() > 0) {
                return "Usuario añadido exitosamente.";
            } else {
                throw new Exception("No se pudo añadir el usuario.");
            }
        } catch (PDOException $e) {
            // Comprobar si el error es de clave duplicada (código SQLSTATE 23000)
            if ($e->getCode() == 23000) {
                throw new Exception("El DNI ya existe en el sistema."); // Mensaje personalizado
            } else {
                // Para otros errores, re-lanzar la excepción original
                throw new Exception("Error al insertar usuario: " . $e->getMessage());
            }
    }
}
    
    
public function eliminarUsuario($id) {
    // Obtener la conexión
    $conexion = $this->db->getConexion();
    if ($conexion === null) {
        throw new Exception("La conexión a la base de datos no está disponible.");
    }

    // Ejecutar la consulta
    $sql = "DELETE FROM clientes WHERE id = :id";
    $stmt = $conexion->prepare($sql); // Solo la consulta se prepara aquí
    $stmt->execute([':id' => $id]);  // Los parámetros se pasan con execute()

    // Comprobar filas afectadas
    if ($stmt->rowCount() > 0) {
        return "Usuario eliminado exitosamente.";
    } else {
        throw new Exception("No se pudo eliminar el usuario. Es posible que el usuario no exista.");
    }
}

    

    public function editarUsuario($id, $dni, $nombre, $correo, $telefono, $direccion, $localidad, $provincia, $rol, $contrasena = null) {
        $conexion = $this->db->getConexion();
    
        // Validar el DNI
        if (!$this->validarDni($dni)) {
            throw new Exception("El DNI no es válido.");
        }
    
        // Validar los campos obligatorios
        $this->validarCamposObligatorios($dni, $nombre, $correo, $rol);
    
        // Verificar duplicados excluyendo al propio usuario
        $this->verificarDuplicado('dni', $dni, $id);
        $this->verificarDuplicado('correo', $correo, $id);
        $this->verificarDuplicado('telefono', $telefono, $id);
    
        // Construir la consulta SQL
        $sql = "UPDATE clientes SET dni = :dni, nombre = :nombre, correo = :correo, direccion = :direccion, localidad = :localidad, provincia = :provincia, telefono = :telefono";
        if ($contrasena) {
            $sql .= ", contrasena = :contrasena";
        }
        $sql .= " WHERE id = :id";
    
        // Construir los parámetros
        $params = [
            ':dni' => $dni,
            ':nombre' => $nombre,
            ':correo' => $correo,
            ':direccion' => $direccion,
            ':localidad' => $localidad,
            ':provincia' => $provincia,
            ':telefono' => $telefono,
            ':id' => $id
        ];
        if ($contrasena) {
            $params[':contrasena'] = password_hash($contrasena, PASSWORD_DEFAULT);
        }
    
        // Ejecutar la consulta
        $stmt = $conexion->prepare($sql);
        $stmt->execute($params);
    
        // Validar si se realizaron cambios
        if ($stmt->rowCount() === 0) {
            throw new Exception("No se pudo actualizar el cliente. Es posible que no haya cambios en los datos.");
        }
    }
    
    

    public function obtenerUsuario($dni) {
        try {
            $conexion = $this->db->getConexion();
            $sql = "SELECT * FROM clientes WHERE dni = :dni";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(':dni', $dni, PDO::PARAM_STR);
            $stmt->execute();
    
            if ($stmt->rowCount() == 0) {
                echo 'No se encontró ningún usuario con ese DNI.<br>';
            }
    
            $usuarioData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $usuarioData;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el usuario: " . $e->getMessage());
        }
    }
    
    

    public function obtenerUsuarioPorId($id) {
        try {
            $conexion = $this->db->getConexion();
            $sql = "SELECT * FROM clientes WHERE id = :id";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el usuario: " . $e->getMessage());
        }
    }
    
    
    public function listarUsuarios($roles = [], $busqueda = '', $orden = 'ASC') {
        try {
            // Verificar que $roles sea un array
            if (!is_array($roles)) {
                throw new Exception("El parámetro 'roles' debe ser un array.");
            }
    
            $conexion = $this->db->getConexion();
            $sql = "SELECT * FROM clientes WHERE 1=1";
    
            // Filtrar por roles
            if (!empty($roles)) {
                $placeholders = implode(',', array_fill(0, count($roles), '?'));
                $sql .= " AND rol IN ($placeholders)";
            }
    
            // Filtrar por búsqueda
            if (!empty($busqueda)) {
                $sql .= " AND (nombre LIKE ? OR dni LIKE ?)";
            }
    
            // Validar el orden
            $orden = strtoupper($orden);
            if (!in_array($orden, ['ASC', 'DESC'])) {
                $orden = 'ASC';
            }
            $sql .= " ORDER BY nombre $orden";
    
            // Preparar y ejecutar la consulta
            $stmt = $conexion->prepare($sql);
    
            $params = [];
            if (!empty($roles)) {
                $params = array_merge($params, $roles);
            }
            if (!empty($busqueda)) {
                $params[] = "%$busqueda%";
                $params[] = "%$busqueda%";
            }
    
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al listar los usuarios: " . $e->getMessage());
        }
    }

    public function listarUsuariosConPaginacion($roles = [], $busqueda = '', $orden = 'ASC', $limite = 10, $offset = 0) {
        try {
            $conexion = $this->db->getConexion();
            $sql = "SELECT * FROM clientes WHERE 1=1";
    
            // Filtrar por roles
            if (!empty($roles)) {
                $placeholders = implode(',', array_fill(0, count($roles), '?'));
                $sql .= " AND rol IN ($placeholders)";
            }
    
            // Filtrar por búsqueda
            if (!empty($busqueda)) {
                $sql .= " AND (nombre LIKE ? OR dni LIKE ?)";
            }
    
            // Validar el orden
            $orden = strtoupper($orden);
            if (!in_array($orden, ['ASC', 'DESC'])) {
                $orden = 'ASC';
            }
    
            // Concatenar LIMIT y OFFSET directamente en la cadena SQL
            $sql .= " ORDER BY nombre $orden LIMIT $limite OFFSET $offset";
    
            $stmt = $conexion->prepare($sql);
    
            // Vincular los parámetros dinámicamente
            $params = [];
            if (!empty($roles)) {
                $params = array_merge($params, $roles);
            }
            if (!empty($busqueda)) {
                $params[] = "%$busqueda%";
                $params[] = "%$busqueda%";
            }
    
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al listar los usuarios: " . $e->getMessage());
        }
    }

    public function obtenerUsuarioPorDniYCorreo($dni, $correo) {
        try {
            $conexion = $this->db->getConexion();
            $sql = "SELECT * FROM clientes WHERE dni = :dni AND correo = :correo";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([':dni' => $dni, ':correo' => $correo]);
            return $stmt->fetch(PDO::FETCH_ASSOC); // Devuelve el usuario encontrado o false si no existe
        } catch (PDOException $e) {
            throw new Exception("Error al buscar el usuario: " . $e->getMessage());
        }
    }

    public function guardarTokenRecuperacion($usuarioId, $token, $expiracion) {
        try {
            $conexion = $this->db->getConexion();
            $sql = "UPDATE clientes SET token_recuperacion = :token, token_expiracion = :expiracion WHERE id = :id";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ':token' => $token,
                ':expiracion' => $expiracion,
                ':id' => $usuarioId
            ]);
    
            if ($stmt->rowCount() === 0) {
                throw new Exception("No se pudo guardar el token de recuperación.");
            }
        } catch (PDOException $e) {
            throw new Exception("Error al guardar el token de recuperación: " . $e->getMessage());
        }
    }
    
    
    

    public function contarUsuarios($roles = [], $busqueda = '') {
        try {
            $conexion = $this->db->getConexion();
            $sql = "SELECT COUNT(*) as total FROM clientes WHERE 1=1";
    
            // Filtrar por roles
            if (!empty($roles)) {
                $placeholders = implode(',', array_fill(0, count($roles), '?'));
                $sql .= " AND rol IN ($placeholders)";
            }
    
            // Filtrar por búsqueda
            if (!empty($busqueda)) {
                $sql .= " AND (nombre LIKE ? OR dni LIKE ?)";
            }
    
            $stmt = $conexion->prepare($sql);
    
            $params = [];
            if (!empty($roles)) {
                $params = array_merge($params, $roles);
            }
            if (!empty($busqueda)) {
                $params[] = "%$busqueda%";
                $params[] = "%$busqueda%";
            }
    
            $stmt->execute($params);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Error al contar los usuarios: " . $e->getMessage());
        }
    }

    public function restablecerContrasena($token, $nuevaContrasena) {
        try {
            $conexion = $this->db->getConexion();
    
            // Verificar si el token es válido y no ha expirado
            $sql = "SELECT id FROM clientes WHERE token_recuperacion = :token AND token_expiracion > NOW()";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([':token' => $token]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$usuario) {
                throw new Exception("La solicitud de restablecimiento no es válida.");
            }
    
            // Actualizar la contraseña del usuario
            $nuevaContrasenaHash = password_hash($nuevaContrasena, PASSWORD_DEFAULT);
            $sql = "UPDATE clientes SET contrasena = :contrasena, token_recuperacion = NULL, token_expiracion = NULL WHERE id = :id";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ':contrasena' => $nuevaContrasenaHash,
                ':id' => $usuario['id']
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error al restablecer la contraseña: " . $e->getMessage());
        }
    }
    
    
    
    
}

