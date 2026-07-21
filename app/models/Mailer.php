<?php

/**
 * Mailer Model class
 *
 * Provides email sending capabilities using PHPMailer.
 * Designed to be generic and reusable across different business domains.
 */

defined('ROOTPATH') or exit('Access Denied!');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    /**
     * @var PHPMailer $mail PHPMailer instance
     */
    protected PHPMailer $mail;

    /**
     * @var string $logoUrl Default logo URL (can be overridden per method)
     */
    protected string $logoUrl;

    /**
     * @var string $privacyUrl Privacy policy URL
     */
    protected string $privacyUrl;

    /**
     * @var string $termsUrl Terms of service URL
     */
    protected string $termsUrl;

    /**
     * Constructor – initializes PHPMailer with SMTP settings from constants.
     */
    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        // SMTP debug logging (logs to PHP error log instead of output)
        $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $this->mail->Debugoutput = function ($str) {
            error_log($str);
        };

        $this->mail->isSMTP();
        $this->mail->Host       = MAIL_HOST;
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = USERNAME;
        $this->mail->Password   = PWD;
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $this->mail->Port       = PORT;

        // Set default URLs (can be overridden in child classes or per method)
        $this->logoUrl    = rtrim(ROOT, '/') . '/assets/img/logo.png';
        $this->privacyUrl = rtrim(ROOT, '/') . '/privacy';
        $this->termsUrl   = rtrim(ROOT, '/') . '/terms';
    }

    /**
     * Send a password reset link to a user.
     *
     * @param string $toEmail Recipient email address
     * @param string $token   Password reset token
     * @throws Exception
     */
    public function sendResetLink(string $toEmail, string $token): void
    {
        $resetUrl = rtrim(ROOT, '/') . "/reset-password/{$token}";

        $this->mail->clearAddresses();
        $this->mail->addAddress($toEmail);
        $this->mail->isHTML(true);
        $this->mail->Subject = 'Password Reset Request';

        $this->mail->Body = $this->buildEmailTemplate(
            'Password Reset Request',
            "<p>Hello,</p>
             <p>You requested a password reset. Click the link below:</p>
             <p><a href=\"{$resetUrl}\">Reset your password</a></p>
             <p>If you didn't request this, please ignore this email.</p>"
        );

        $this->mail->send();
    }

    /**
     * Send a one‑time password (OTP) for general user verification.
     *
     * @param string $toEmail Recipient email address
     * @param string $otp     The OTP code
     * @throws Exception
     */
    public function sendOtp(string $toEmail, string $otp): void
    {
        $this->mail->clearAddresses();
        $this->mail->addAddress($toEmail);
        $this->mail->isHTML(true);
        $this->mail->Subject = 'Your Verification Code from ' . APP_NAME;

        $body = "
            <h1 style='color: #1a202c; margin-top: 0;'>Secure Verification</h1>
            <p style='font-size: 16px;'>
                Thank you for using <strong>" . APP_NAME . "</strong>. 
                To complete your action, please use the verification code below:
            </p>
            <div class='otp-box'>
                <p style='margin: 0 0 12px; color: #4a5568; font-size: 16px;'>Your One-Time Password</p>
                <h2 style='font-size: 36px; margin: 0; color: #2c5282; letter-spacing: 8px; font-weight: 700;'>{$otp}</h2>
            </div>
            <p style='font-size: 15px;'>
                <strong>Important:</strong> This code expires in <span style='color: #e53e3e; font-weight: 600;'>10 minutes</span>.
                Please enter it to verify your identity.
            </p>
            <p style='font-size: 15px; margin-top: 25px; padding-top: 20px; border-top: 1px solid #edf2f7;'>
                If you didn't request this code, please contact our support team immediately at
                <a href='mailto:support@" . APP_DOMAIN . "' style='color: #2c5282; text-decoration: none;'>support@" . APP_DOMAIN . "</a>
            </p>
        ";

        $this->mail->Body = $this->buildEmailTemplate('Secure Verification', $body);
        $this->mail->send();
    }

    /**
     * Send a one‑time password (OTP) for staff user registration.
     * Generic version – no company‑specific branding.
     *
     * @param string $toEmail Recipient email address
     * @param string $otp     The OTP code
     * @throws Exception
     */
    public function sendStaffUserOtp(string $toEmail, string $otp): void
    {
        $this->mail->clearAddresses();
        $this->mail->addAddress($toEmail);
        $this->mail->isHTML(true);
        $this->mail->Subject = 'Staff Registration Verification from ' . APP_NAME;

        $body = "
            <h1 style='color: #1a202c; margin-top: 0;'>Staff Verification</h1>
            <p style='font-size: 16px;'>
                Dear Staff Member,<br>
                Thank you for registering as a user of the <strong>" . APP_NAME . "</strong> system.
                To complete your registration, please use the verification code below:
            </p>
            <div class='otp-box'>
                <p style='margin: 0 0 12px; color: #4a5568; font-size: 16px;'>Your One-Time Password</p>
                <h2 style='font-size: 36px; margin: 0; color: #c27d07ff; letter-spacing: 8px; font-weight: 700;'>{$otp}</h2>
            </div>
            <p style='font-size: 15px;'>
                <strong>Important:</strong> This code expires in <span style='color: #e53e3e; font-weight: 600;'>10 minutes</span>.
                Please enter it to verify your identity.
            </p>
            <p style='font-size: 15px; margin-top: 25px; padding-top: 20px; border-top: 1px solid #edf2f7;'>
                If you didn't request this code, please contact your system administrator immediately at
                <a href='mailto:admin@" . APP_DOMAIN . "' style='color: #2c5282; text-decoration: none;'>admin@" . APP_DOMAIN . "</a>
            </p>
        ";

        $this->mail->Body = $this->buildEmailTemplate('Staff Verification', $body);
        $this->mail->send();
    }

    /**
     * Send a confirmation email for a successful application (generic).
     *
     * @param string $toEmail Recipient email address
     * @throws Exception
     */
    public function sendApplicationConfirmation(string $toEmail): void
    {
        $this->mail->clearAddresses();
        $this->mail->addAddress($toEmail);
        $this->mail->isHTML(true);
        $this->mail->Subject = 'Your Application at ' . APP_NAME;

        $body = "
            <h1 style='color: #1a202c; margin-top: 0;'>Application Confirmed</h1>
            <p style='font-size: 16px;'>
                Thank you for your submission to <strong>" . APP_NAME . "</strong>.<br>
                This email confirms that your application has been received and is now being processed.
            </p>
            <p style='font-size: 15px; margin-top: 25px; padding-top: 20px; border-top: 1px solid #edf2f7;'>
                If you have any questions, please contact our support team at
                <a href='mailto:support@" . APP_DOMAIN . "' style='color: #2c5282; text-decoration: none;'>support@" . APP_DOMAIN . "</a>
            </p>
        ";

        $this->mail->Body = $this->buildEmailTemplate('Application Confirmed', $body);
        $this->mail->send();
    }

    /**
     * Build a complete HTML email template with header, content, and footer.
     *
     * @param string $title   Email title (used in subject and header)
     * @param string $content The main HTML content of the email
     * @return string Complete HTML email body
     */
    protected function buildEmailTemplate(string $title, string $content): string
    {
        $logoUrl    = $this->logoUrl;
        $privacyUrl = $this->privacyUrl;
        $termsUrl   = $this->termsUrl;
        $appName    = APP_NAME;
        $year       = date('Y');

        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>{$title} - {$appName}</title>
            <style>
                body, table, td, div, p {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
                    line-height: 1.6;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: #ffffff;
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
                }
                .header {
                    background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
                    padding: 25px 0;
                    text-align: center;
                }
                .header img {
                    max-width: 100%;
                    height: auto;
                    width: 180px;
                }
                .content {
                    padding: 35px 30px;
                    color: #4a5568;
                }
                .otp-box {
                    background: linear-gradient(to right, #f0f9ff, #e0f2fe);
                    border: 1px solid #bae6fd;
                    border-radius: 12px;
                    padding: 22px;
                    margin: 25px 0;
                    text-align: center;
                }
                .footer {
                    background-color: #f8fafc;
                    padding: 20px 30px;
                    color: #718096;
                    font-size: 13px;
                    border-top: 1px solid #edf2f7;
                }
                .footer a {
                    color: #4a5568;
                    text-decoration: underline;
                }
                @media (max-width: 600px) {
                    .container { width: 100% !important; }
                    .content, .footer { padding: 25px !important; }
                }
            </style>
        </head>
        <body style='margin: 0; padding: 20px 0; background-color: #f1f5f9;'>
            <table role='presentation' width='100%' cellpadding='0' cellspacing='0'>
                <tr>
                    <td align='center'>
                        <div class='container'>
                            <!-- Header with Logo -->
                            <div class='header'>
                                <img src='{$logoUrl}' alt='{$appName} Logo' width='180'>
                            </div>

                            <!-- Content Section -->
                            <div class='content'>
                                {$content}
                            </div>

                            <!-- Footer -->
                            <div class='footer'>
                                <p style='margin: 0 0 12px;'>
                                    <strong>{$appName}</strong><br>
                                    Reliable services for your business
                                </p>
                                <p style='margin: 0; font-size: 12px; line-height: 1.5;'>
                                    This is an automated message, please do not reply directly.<br>
                                    &copy; {$year} {$appName}. All rights reserved.<br>
                                    <a href='{$privacyUrl}'>Privacy Policy</a> |
                                    <a href='{$termsUrl}'>Terms of Service</a>
                                </p>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        HTML;
    }
}