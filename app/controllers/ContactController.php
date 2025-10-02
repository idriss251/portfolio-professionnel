<?php
/**
 * Contrôleur pour la gestion des contacts
 * 
 * @author Keyne - Expert en IA & ML
 * @version 1.0.0
 */

require_once '../config/config.php';

class ContactController {
    private $db;
    private $contactModel;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->contactModel = new Contact();
    }
    
    /**
     * Traite l'envoi du formulaire de contact
     */
    public function sendMessage() {
        header('Content-Type: application/json');
        
        try {
            // Vérification de la méthode HTTP
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Méthode non autorisée');
            }
            
            // Validation et nettoyage des données
            $data = $this->validateAndSanitizeInput();
            
            // Vérification anti-spam
            if (!$this->checkAntiSpam($data)) {
                throw new Exception('Détection de spam');
            }
            
            // Sauvegarde en base de données
            $contactId = $this->saveContact($data);
            
            if (!$contactId) {
                throw new Exception('Erreur lors de la sauvegarde');
            }
            
            // Envoi de l'email
            $emailSent = $this->sendEmail($data, $contactId);
            
            // Réponse de succès
            echo json_encode([
                'success' => true,
                'message' => 'Votre message a été envoyé avec succès!',
                'contact_id' => $contactId
            ]);
            
        } catch (Exception $e) {
            error_log("Erreur ContactController: " . $e->getMessage());
            
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Valide et nettoie les données d'entrée
     */
    private function validateAndSanitizeInput() {
        $errors = [];
        
        // Nom
        if (empty($_POST['name'])) {
            $errors[] = 'Le nom est requis';
        }
        $name = trim(filter_var($_POST['name'], FILTER_SANITIZE_STRING));
        if (strlen($name) < 2 || strlen($name) > 100) {
            $errors[] = 'Le nom doit contenir entre 2 et 100 caractères';
        }
        
        // Email
        if (empty($_POST['email'])) {
            $errors[] = 'L\'email est requis';
        }
        $email = trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format d\'email invalide';
        }
        
        // Sujet
        if (empty($_POST['subject'])) {
            $errors[] = 'Le sujet est requis';
        }
        $subject = trim(filter_var($_POST['subject'], FILTER_SANITIZE_STRING));
        if (strlen($subject) < 5 || strlen($subject) > 200) {
            $errors[] = 'Le sujet doit contenir entre 5 et 200 caractères';
        }
        
        // Message
        if (empty($_POST['message'])) {
            $errors[] = 'Le message est requis';
        }
        $message = trim(htmlspecialchars($_POST['message']));
        if (strlen($message) < 10 || strlen($message) > 2000) {
            $errors[] = 'Le message doit contenir entre 10 et 2000 caractères';
        }
        
        if (!empty($errors)) {
            throw new Exception(implode(', ', $errors));
        }
        
        return [
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
            'ip_address' => $this->getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ];
    }
    
    /**
     * Vérifications anti-spam
     */
    private function checkAntiSpam($data) {
        // Vérifier le honeypot (champ caché)
        if (!empty($_POST['website'])) {
            return false;
        }
        
        // Vérifier la fréquence d'envoi depuis la même IP
        $recentMessages = $this->db->selectOne(
            "SELECT COUNT(*) as count FROM contacts WHERE ip_address = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)",
            [$data['ip_address']]
        );
        
        if ($recentMessages['count'] >= 3) {
            throw new Exception('Trop de messages envoyés. Veuillez patienter.');
        }
        
        // Vérifier les mots-clés de spam
        $spamKeywords = ['viagra', 'casino', 'lottery', 'winner', 'congratulations', 'click here', 'free money'];
        $content = strtolower($data['message'] . ' ' . $data['subject']);
        
        foreach ($spamKeywords as $keyword) {
            if (strpos($content, $keyword) !== false) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Sauvegarde le contact en base de données
     */
    private function saveContact($data) {
        $query = "INSERT INTO contacts (name, email, subject, message, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?)";
        
        return $this->db->insert($query, [
            $data['name'],
            $data['email'],
            $data['subject'],
            $data['message'],
            $data['ip_address'],
            $data['user_agent']
        ]);
    }
    
    /**
     * Envoie l'email de notification
     */
    private function sendEmail($data, $contactId) {
        try {
            // Configuration de l'email
            $to = SMTP_FROM_EMAIL;
            $subject = "[Portfolio] Nouveau message de " . $data['name'];
            
            // Corps de l'email en HTML
            $htmlBody = $this->generateEmailTemplate($data, $contactId);
            
            // Headers
            $headers = [
                'MIME-Version: 1.0',
                'Content-Type: text/html; charset=UTF-8',
                'From: ' . SMTP_FROM_NAME . ' <' . SMTP_FROM_EMAIL . '>',
                'Reply-To: ' . $data['email'],
                'X-Mailer: PHP/' . phpversion(),
                'X-Priority: 1'
            ];
            
            // Envoi de l'email
            $sent = mail($to, $subject, $htmlBody, implode("\r\n", $headers));
            
            if ($sent) {
                // Envoyer un email de confirmation à l'expéditeur
                $this->sendConfirmationEmail($data);
            }
            
            return $sent;
            
        } catch (Exception $e) {
            error_log("Erreur envoi email: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Génère le template HTML pour l'email
     */
    private function generateEmailTemplate($data, $contactId) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; padding: 20px; text-align: center; }
                .content { background: #f9fafb; padding: 30px; }
                .info-box { background: white; padding: 20px; margin: 15px 0; border-left: 4px solid #6366f1; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Nouveau message de contact</h1>
                    <p>Portfolio Keyne - Expert IA & ML</p>
                </div>
                
                <div class='content'>
                    <div class='info-box'>
                        <h3>Informations du contact</h3>
                        <p><strong>Nom:</strong> {$data['name']}</p>
                        <p><strong>Email:</strong> {$data['email']}</p>
                        <p><strong>Sujet:</strong> {$data['subject']}</p>
                        <p><strong>Date:</strong> " . date('d/m/Y à H:i') . "</p>
                        <p><strong>ID Contact:</strong> #{$contactId}</p>
                    </div>
                    
                    <div class='info-box'>
                        <h3>Message</h3>
                        <p>" . nl2br(htmlspecialchars($data['message'])) . "</p>
                    </div>
                    
                    <div class='info-box'>
                        <h3>Informations techniques</h3>
                        <p><strong>IP:</strong> {$data['ip_address']}</p>
                        <p><strong>User Agent:</strong> " . htmlspecialchars($data['user_agent']) . "</p>
                    </div>
                </div>
                
                <div class='footer'>
                    <p>Email automatique du portfolio de Keyne</p>
                    <p>Expert en Intelligence Artificielle & Machine Learning</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Envoie un email de confirmation à l'expéditeur
     */
    private function sendConfirmationEmail($data) {
        $subject = "Confirmation de réception - " . $data['subject'];
        
        $htmlBody = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; padding: 20px; text-align: center; }
                .content { background: #f9fafb; padding: 30px; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Merci pour votre message!</h1>
                    <p>Keyne - Expert IA & ML</p>
                </div>
                
                <div class='content'>
                    <p>Bonjour {$data['name']},</p>
                    
                    <p>Merci de m'avoir contacté. J'ai bien reçu votre message concernant <strong>{$data['subject']}</strong>.</p>
                    
                    <p>Je vous répondrai dans les plus brefs délais, généralement sous 24-48 heures.</p>
                    
                    <p>En attendant, n'hésitez pas à consulter mes projets et articles sur mon portfolio.</p>
                    
                    <p>Cordialement,<br>
                    <strong>Keyne</strong><br>
                    Expert en Intelligence Artificielle & Machine Learning</p>
                </div>
                
                <div class='footer'>
                    <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
                </div>
            </div>
        </body>
        </html>";
        
        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . SMTP_FROM_NAME . ' <' . SMTP_FROM_EMAIL . '>',
            'X-Mailer: PHP/' . phpversion()
        ];
        
        return mail($data['email'], $subject, $htmlBody, implode("\r\n", $headers));
    }
    
    /**
     * Récupère l'adresse IP du client
     */
    private function getClientIP() {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]);
                $ip = trim($ips[0]);
                
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Récupère tous les messages de contact (pour l'admin)
     */
    public function getAllMessages($limit = 50, $offset = 0) {
        $query = "SELECT * FROM contacts ORDER BY created_at DESC LIMIT ? OFFSET ?";
        return $this->db->select($query, [$limit, $offset]);
    }
    
    /**
     * Marque un message comme lu
     */
    public function markAsRead($contactId) {
        $query = "UPDATE contacts SET status = 'read' WHERE id = ?";
        return $this->db->update($query, [$contactId]);
    }
    
    /**
     * Supprime un message
     */
    public function deleteMessage($contactId) {
        $query = "DELETE FROM contacts WHERE id = ?";
        return $this->db->delete($query, [$contactId]);
    }
}

// Traitement de la requête si appelé directement
if (basename($_SERVER['PHP_SELF']) === 'ContactController.php') {
    $controller = new ContactController();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->sendMessage();
    } else {
        echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    }
}
?>
