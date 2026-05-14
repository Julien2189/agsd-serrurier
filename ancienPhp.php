<?php
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "success" => false,
        "message" => "Méthode non autorisée."
    ]);
    exit;
}

$to = "contact@jlqdeveloppement.fr";

$name = trim($_POST["from_name"] ?? "");
$email = trim($_POST["reply_to"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$subject = trim($_POST["subject"] ?? "");
$message = trim($_POST["message"] ?? "");

if ($name === "" || $email === "" || $subject === "" || $message === "") {
    echo json_encode([
        "success" => false,
        "message" => "Merci de remplir tous les champs obligatoires."
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "success" => false,
        "message" => "Adresse email invalide."
    ]);
    exit;
}

$safeName = htmlspecialchars($name, ENT_QUOTES, "UTF-8");
$safeEmail = htmlspecialchars($email, ENT_QUOTES, "UTF-8");
$safePhone = htmlspecialchars($phone, ENT_QUOTES, "UTF-8");
$safeSubject = htmlspecialchars($subject, ENT_QUOTES, "UTF-8");
$safeMessage = htmlspecialchars($message, ENT_QUOTES, "UTF-8");

$mailSubject = "Nouvelle demande depuis le site AGSD Serrurier";

$mailBody = "Nouvelle demande de contact depuis le site AGSD Serrurier\n\n";
$mailBody .= "Nom : " . $safeName . "\n";
$mailBody .= "Email : " . $safeEmail . "\n";
$mailBody .= "Téléphone : " . $safePhone . "\n";
$mailBody .= "Sujet : " . $safeSubject . "\n\n";
$mailBody .= "Message :\n" . $safeMessage . "\n";

$headers = [];
$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-Type: text/plain; charset=UTF-8";
$headers[] = "From: JLQDeveloppement <contact@jlqdeveloppement.fr>";
$headers[] = "Reply-To: " . $safeEmail;
$headers[] = "X-Mailer: PHP/" . phpversion();

$sent = mail($to, $mailSubject, $mailBody, implode("\r\n", $headers));

if ($sent) {
    echo json_encode([
        "success" => true,
        "message" => "Message envoyé avec succès."
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Erreur : le serveur n'a pas pu envoyer le message."
    ]);
}
?>