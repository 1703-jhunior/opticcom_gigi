<?php
// app/librerias/GreenApi.php
class GreenApi {
    private $idInstance = "7103539875"; 
    private $apiToken   = "6ce6c47ff5214b0bb6c8ec45ae82b2db166f911ef576429ebf";
    private $urlBase;

    public function __construct() {
        // CORRECCIÓN: La API oficial de Green API usa este formato:
        // https://api.green-api.com/waInstance{{idInstance}}
        $this->urlBase = "https://api.green-api.com/waInstance{$this->idInstance}";
    }

    public function enviarMensaje($numero, $mensaje) {
        $numero_limpio = preg_replace('/\D+/', '', $numero);
        if (strlen($numero_limpio) == 9) $numero_limpio = '51' . $numero_limpio;

        $url = "{$this->urlBase}/sendMessage/{$this->apiToken}";
        $data = ["chatId" => $numero_limpio . "@c.us", "message" => $mensaje];

        return $this->ejecutarPeticion($url, $data);
    }

    private function ejecutarPeticion($url, $data) {
        $payload = json_encode($data);
        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        $result = curl_exec($ch);
        
        // 🔹 AGREGA ESTO PARA DEPURAR
        error_log("GreenAPI URL: " . $url);
        error_log("GreenAPI Payload: " . $payload);
        error_log("GreenAPI Respuesta: " . $result); 
        // 🔹 FIN DE DEPURACIÓN
        
        curl_close($ch);
        return json_decode($result, true);
    }
}