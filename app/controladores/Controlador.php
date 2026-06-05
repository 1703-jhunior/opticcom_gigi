<?php
class Controlador {
    public function modelo($modelo){
        require_once __DIR__ . '/../modelos/' . $modelo . '.php';
        return new $modelo();
    }

public function vista($vista, $datos = []) {
        // Chequear si el archivo vista existe usando ruta absoluta
        if (file_exists(__DIR__ . '/../vistas/' . $vista . '.php')) {
            require_once __DIR__ . '/../vistas/' . $vista . '.php';
        } else {
            // Este es el mensaje que estás viendo ahora mismo
            die('Error Crítico: La vista no existe.');
        }
    }
}
