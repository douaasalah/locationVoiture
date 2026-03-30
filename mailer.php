<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function envoyerEmail($destinataire, $sujet, $contenu) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'saklyimen24@gmail.com';  
        $mail->Password   = 'npvg iboc wwix qawp';  
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('saklyimen24@gmail.com', 'GoRent');
        $mail->addAddress($destinataire);
        $mail->isHTML(true);
        $mail->Subject = $sujet;
        $mail->Body    = $contenu;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>