<?php
/**
 * Contact Form Handler
 * Menangani pengiriman form kontak dari aplikasi
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ===== VALIDASI DAN SANITASI =====
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function send_response($success, $message, $code = 200, $data = []) {
    http_response_code($code);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

// ===== HANDLE REQUEST =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data JSON
    $input = json_decode(file_get_contents('php://input'), true);

    // Validasi form
    $errors = [];

    if (empty($input['name'])) {
        $errors['name'] = 'Nama harus diisi';
    }

    if (empty($input['email'])) {
        $errors['email'] = 'Email harus diisi';
    } elseif (!validate_email($input['email'])) {
        $errors['email'] = 'Format email tidak valid';
    }

    if (empty($input['phone'])) {
        $errors['phone'] = 'Nomor telepon harus diisi';
    }

    if (empty($input['subject'])) {
        $errors['subject'] = 'Subjek harus diisi';
    }

    if (empty($input['message'])) {
        $errors['message'] = 'Pesan harus diisi';
    } elseif (strlen($input['message']) < 10) {
        $errors['message'] = 'Pesan minimal 10 karakter';
    }

    // Jika ada error
    if (!empty($errors)) {
        send_response(false, 'Validasi gagal', 422, $errors);
    }

    // Sanitasi data
    $name = sanitize_input($input['name']);
    $email = sanitize_input($input['email']);
    $phone = sanitize_input($input['phone']);
    $subject = sanitize_input($input['subject']);
    $message_text = sanitize_input($input['message']);

    // Siapkan email admin
    $admin_email = 'admin@example.com';
    $headers = "From: " . $email . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    // Isi email
    $email_body = "
    <html>
        <head>
            <title>Pesan Baru dari Portofolio</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #2563eb; color: white; padding: 20px; border-radius: 5px; }
                .content { background-color: #f5f5f5; padding: 20px; border-radius: 5px; margin-top: 20px; }
                .field { margin: 15px 0; }
                .label { font-weight: bold; color: #2563eb; }
                .footer { text-align: center; margin-top: 30px; color: #999; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Pesan Baru dari Portofolio</h2>
                </div>
                <div class='content'>
                    <div class='field'>
                        <span class='label'>Nama:</span> $name
                    </div>
                    <div class='field'>
                        <span class='label'>Email:</span> $email
                    </div>
                    <div class='field'>
                        <span class='label'>Telepon:</span> $phone
                    </div>
                    <div class='field'>
                        <span class='label'>Subjek:</span> $subject
                    </div>
                    <div class='field'>
                        <span class='label'>Pesan:</span><br>
                        <p>" . nl2br($message_text) . "</p>
                    </div>
                </div>
                <div class='footer'>
                    <p>Pesan ini dikirim dari formulir kontak portofolio Anda</p>
                </div>
            </div>
        </body>
    </html>
    ";

    // Kirim email (commented karena testing)
    // mail($admin_email, 'Pesan Baru: ' . $subject, $email_body, $headers);

    // Simpan ke file (simulasi database)
    $contact_data = [
        'id' => uniqid(),
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'subject' => $subject,
        'message' => $message_text,
        'timestamp' => date('Y-m-d H:i:s'),
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'status' => 'new'
    ];

    $contacts_file = 'contacts.json';
    $contacts = file_exists($contacts_file) ? json_decode(file_get_contents($contacts_file), true) : [];
    $contacts[] = $contact_data;
    file_put_contents($contacts_file, json_encode($contacts, JSON_PRETTY_PRINT));

    send_response(true, 'Pesan berhasil dikirim! Kami akan segera merespon.', 201, $contact_data);
} else {
    send_response(false, 'Method not allowed', 405);
}
?>