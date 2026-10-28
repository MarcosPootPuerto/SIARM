<?php
// modelos/UsuarioModel.php

// Subir un nivel desde 'modelos/' para encontrar 'conexion.php' en la raíz
require_once __DIR__ . '/../conexion.php';

class UsuarioModel {
    
    // Método para obtener todos los usuarios y sus dispositivos
    public static function obtenerTodosConDispositivos($pdo) {
        $stmt = $pdo->query("SELECT * FROM usuarios ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    // Verificar si un correo ya existe
    public static function existeCorreo($pdo, $correo) {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ?");
        $stmt->execute([$correo]);
        return $stmt->fetch() !== false;
    }

    // Verificar si un correo ya existe en otro usuario
    public static function existeCorreoEnOtroUsuario($pdo, $correo, $idExcluir = 0) {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ? AND id != ?");
        $stmt->execute([$correo, $idExcluir]);
        return $stmt->fetch() !== false;
    }

    // Crear un nuevo usuario
    public static function crear($pdo, $nombre, $correo, $contrasena, $rol) {
        $hash = password_hash($contrasena, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$nombre, $correo, $hash, $rol]);
    }

    // Actualizar usuario
    public static function actualizar($pdo, $id, $nombre, $correo, $rol, $contrasena = '') {
        if (!empty($contrasena)) {
            $hash = password_hash($contrasena, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, correo = ?, contrasena = ?, rol = ? WHERE id = ?");
            return $stmt->execute([$nombre, $correo, $hash, $rol, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, correo = ?, rol = ? WHERE id = ?");
            return $stmt->execute([$nombre, $correo, $rol, $id]);
        }
    }

    // Eliminar usuario
    public static function eliminar($pdo, $id) {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        return $stmt->execute([$id]);
    }
}