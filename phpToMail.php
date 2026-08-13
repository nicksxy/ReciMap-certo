<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
 
require 'vendor/autoload.php';
 
function phpToMail($destinatario, $assunto, $mensagem) {
    $mail = new PHPMailer(true);
 
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'recimap25@gmail.com'; // seu e-mail
        $mail->Password = 'rvba frdj hlsx svsv';       // senha do app (não sua senha normal)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
 
        $mail->setFrom('recimap25@gmail.com', 'ReciMap - Suporte');
        $mail->addAddress($destinatario);
 
        $mail->isHTML(false);
        $mail->Subject = $assunto;
        $mail->Body = $mensagem;
 
        $mail->send();
        return true;
 
    } catch (Exception $e) {
        echo "Erro ao enviar: {$mail->ErrorInfo}";
        return false;
    }
}
 
 
 