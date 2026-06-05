<?php
// Carga manual de PHPMailer
require_once APP_ROOT . '/app/librerias/PHPMailer/Exception.php';
require_once APP_ROOT . '/app/librerias/PHPMailer/PHPMailer.php';
require_once APP_ROOT . '/app/librerias/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Correo {
    
    // Función estática para llamarla desde cualquier controlador sin instanciar
    public static function enviar($destinatario, $asunto, $cuerpoHTML, $adjunto_binario = null, $nombre_adjunto = '') {
        $mail = new PHPMailer(true);

        try {
            // 1. Configuración del Servidor SMTP (Hostinger)
            $mail->isSMTP();
            $mail->SMTPDebug  = 2; // <--- AÑADE ESTO TEMPORALMENTE (Nivel de depuración cliente/servidor)
            $mail->Debugoutput = 'error_log'; // <--- Imprime la charla SMTP en tu error_log de PHP
            $mail->Host       = 'smtp.hostinger.com'; 
            $mail->SMTPAuth   = true;
            $mail->Username   = 'notificaciones@opticcomperu.com'; 
            $mail->Password   = 'Optc2026@cr123'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL
            $mail->Port       = 465;

            // Evitar problemas con tildes y caracteres especiales
            $mail->CharSet = 'UTF-8';

            // 2. Remitente y Destinatario
            $mail->setFrom('notificaciones@opticcomperu.com', 'OPTICCOM S.A.C.');
            $mail->addAddress($destinatario);

            // 3. Procesamiento de Adjuntos en Memoria (Ej: El PDF de Dompdf)
            if ($adjunto_binario !== null && $nombre_adjunto !== '') {
                // addStringAttachment adjunta un archivo directamente desde la memoria RAM
                $mail->addStringAttachment($adjunto_binario, $nombre_adjunto);
            }

            // 4. Construcción del Correo
            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body    = static::generarPlantillaSaaS($asunto, $cuerpoHTML);
            $mail->AltBody = strip_tags($cuerpoHTML); // Texto plano para clientes de correo muy antiguos

            // 5. Enviar
            $mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Error de PHPMailer al enviar a $destinatario: {$mail->ErrorInfo}");
            return false;
        }
    }

    // Plantilla HTML con diseño oscuro corporativo (Glassmorphism)
    private static function generarPlantillaSaaS($titulo, $contenido) {
        return "
        <div style='font-family: \"Inter\", Helvetica, Arial, sans-serif; background-color: #020617; padding: 40px 20px; color: #ffffff;'>
            <div style='max-width: 600px; margin: 0 auto; background-color: #0f172a; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.5);'>
                <div style='background-color: #020617; padding: 25px; text-align: center; border-bottom: 2px solid #F28C28;'>
                    <h2 style='color: #F28C28; margin: 0; font-size: 26px; letter-spacing: 1px;'>OPTICCOM</h2>
                    <p style='color: #94a3b8; margin: 5px 0 0 0; font-size: 13px;'>Conectando tus sueños</p>
                </div>
                <div style='padding: 35px; color: #d7e2f0; line-height: 1.6; font-size: 15px;'>
                    <h3 style='color: #ffffff; margin-top: 0; font-size: 20px; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 10px;'>{$titulo}</h3>
                    {$contenido}
                </div>
                <div style='background-color: #020617; padding: 20px; text-align: center; font-size: 12px; color: #64748b;'>
                    &copy; " . date('Y') . " OPTICCOM S.A.C. Todos los derechos reservados.<br>
                    Este es un mensaje automático del sistema de facturación, por favor no responda.
                </div>
            </div>
        </div>";
    }
}
?>