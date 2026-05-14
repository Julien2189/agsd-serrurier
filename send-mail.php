<?php
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "success" => false,
        "message" => "Méthode non autorisée."
    ]);
    exit;
}

$to = "contact@agsd-serrurier.fr";

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

/* Protection simple contre l'injection dans les headers */
function clean_header($value) {
    return str_replace(["\r", "\n"], "", trim($value));
}

/* Nettoyage texte simple */
function clean_text($value) {
    return trim(strip_tags($value));
}

$safeName = clean_text($name);
$safeEmail = clean_header($email);
$safePhone = clean_text($phone);
$safeSubject = clean_text($subject);
$safeMessage = clean_text($message);

$mailSubject = "Nouvelle demande depuis le site AGSD Serrurier";

$mailBody = "Nouvelle demande de contact depuis le site AGSD Serrurier\n\n";
$mailBody .= "Nom : " . $safeName . "\n";
$mailBody .= "Email : " . $safeEmail . "\n";
$mailBody .= "Téléphone : " . ($safePhone !== "" ? $safePhone : "Non renseigné") . "\n";
$mailBody .= "Sujet : " . $safeSubject . "\n\n";
$mailBody .= "Message :\n" . $safeMessage . "\n";

$headers = [];
$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-Type: text/plain; charset=UTF-8";
$headers[] = "From: AGSD Serrurier <contact@agsd-serrurier.fr>";
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