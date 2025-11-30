<?php
require_once __DIR__ . '/../models/PerfilModel.php';

class PerfilController {
    private $model;

    public function __construct($database) {
        $this->model = new PerfilModel($database);
    }

    public function mostrarPerfil($usuario_id) {
        // Obtener datos del perfil
        $perfil = $this->model->obtenerPerfil($usuario_id);
        
        if (!$perfil) {
            // Si no existe el perfil, crear uno básico
            $perfil = [
                'nombre' => $_SESSION['user_nombre'] ?? 'Usuario',
                'email' => $_SESSION['user_email'] ?? '',
                'nivel_nombre' => 'Bronce',
                'puntos_acumulados' => 0,
                'total_compras' => 0
            ];
        }

        // Obtener siguiente nivel
        $puntos_actuales = $perfil['puntos_acumulados'] ?? 0;
        $proximo_nivel = $this->model->obtenerSiguienteNivel($puntos_actuales);
        
        // Calcular compras faltantes para el siguiente nivel
        $compras_faltantes = 0;
        if ($proximo_nivel) {
            $compras_actuales = $perfil['total_compras'] ?? 0;
            $compras_faltantes = max(0, $proximo_nivel['compras_necesarias'] - $compras_actuales);
        }

        return [
            'perfil' => $perfil,
            'proximo_nivel' => $proximo_nivel,
            'compras_faltantes' => $compras_faltantes
        ];
    }

    public function actualizarPerfil($usuario_id, $datos) {
        $nombre = trim($datos['nombre']);
        $email = trim($datos['email']);

        // Validaciones básicas
        if (empty($nombre) || empty($email)) {
            return ['success' => false, 'message' => 'Nombre y email son obligatorios'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'El formato del email no es válido'];
        }

        // Verificar si el email ya existe
        if ($this->model->verificarEmailExistente($email, $usuario_id)) {
            return ['success' => false, 'message' => 'El email ya está en uso por otro usuario'];
        }

        // Actualizar perfil
        $resultado = $this->model->actualizarPerfil($usuario_id, $nombre, $email);

        if ($resultado) {
            // Actualizar sesión
            $_SESSION['user_nombre'] = $nombre;
            $_SESSION['user_email'] = $email;
            
            return ['success' => true, 'message' => 'Perfil actualizado correctamente'];
        } else {
            return ['success' => false, 'message' => 'Error al actualizar el perfil'];
        }
    }
}
?>