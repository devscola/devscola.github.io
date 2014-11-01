<?php
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$email = $_POST['email'];
$negocio = $_POST['negocio'];

$header = 'From: ' . $email . " \r\n";
$header .= "X-Mailer: PHP/" . phpversion() . " \r\n" . " \r\n";
$header .= 'MIME-Version: 1.0' . "\r\n";
$header .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

$mensaje = "Este mensaje fue enviado por " . "<b>" . $nombre . " " . $apellido . "</b>" . "<br>" . "<br>";

$mensaje .= "Su e-mail es: " . $email . "<br>" . "<br>" . "<br>";

$mensaje .= "Mensaje: " . "<br>" . $_POST['mensaje'] . "<br>" . "<br>" . "<br>";

$mensaje .= "Enviado el " . date('d/m/Y', time());

$para = 'devscola@devscola.com';

$asunto = "Solicitud de asesoramiento de - " . $nombre . " " . $apellido . " \r\n";

mail($para, $asunto, utf8_decode($mensaje), $header);

echo "<script>alert('El email fue enviado satisfactoriamente!');</script>";
echo "<script>document.location.href='/'</script>";
?>