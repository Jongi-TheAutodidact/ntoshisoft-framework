<?php

/**
 * Mailer Model class
 */

defined('ROOTPATH') or exit('Access Denied!');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    protected $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        // SMTP config
        $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;

        $this->mail->Debugoutput = function ($str) {
            error_log($str); // Logs to PHP error log instead of outputting
        };

        $this->mail->isSMTP();
        $this->mail->Host       = MAIL_HOST;
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = USERNAME;
        $this->mail->Password   = PWD;
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL
        $this->mail->Port       = PORT;
    }

    public function sendResetLink(string $toEmail, string $token): void
    {
        // use your ROOT constant
        $resetUrl = rtrim(ROOT, '/') . "/reset-password/{$token}";

        $this->mail->addAddress($toEmail);
        $this->mail->isHTML(true);
        $this->mail->Subject = 'Password Reset Request';
        $this->mail->Body    = "
        <p>Hello,</p>
        <p>You requested a password reset. Click the link below:</p>
        <p><a href=\"{$resetUrl}\">Reset your password</a></p>
        <p>If you didn’t request this, just ignore this email.</p>
    ";
        $this->mail->send();
    }

    public function sendOtp(string $toEmail, string $otp): void
    {
        $this->mail->clearAddresses();
        $this->mail->addAddress($toEmail);
        $this->mail->isHTML(true);
        $this->mail->Subject = 'Your Policy Application OTP Code from ' . APP_NAME;

        // Company logo URL
        $logoUrl = ROOT . '/uploads/logo/1754992023_bleki-and-blackie-logo-white-bg.png';
        // Privacy page URL
        $privacyUrl = ROOT . '/privacy';
        // POPIA page URL
        $popiaUrl = ROOT . '/popia';

        $this->mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>OTP Verification - " . APP_NAME . "</title>
            <style>
                /* Basic email reset */
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
                    background: linear-gradient(135deg, #6c6b1aff 0%, #b0680aff 100%);
                    padding: 25px 0;
                    text-align: center;
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
                                <img src='$logoUrl' alt='" . APP_NAME . " Logo' width='180' style='max-width: 100%; height: auto;'>
                            </div>
                            
                            <!-- Content Section -->
                            <div class='content'>
                                <h1 style='color: #1a202c; margin-top: 0;'>Secure Verification</h1>
                                
                                <p style='font-size: 16px;'>Thank you for applying for a policy with <strong>" . APP_NAME . "</strong>. To complete your application, please use the verification code below:</p>
                                
                                <div class='otp-box'>
                                    <p style='margin: 0 0 12px; color: #4a5568; font-size: 16px;'>Your One-Time Password</p>
                                    <h2 style='font-size: 36px; margin: 0; color: #2c5282; letter-spacing: 8px; font-weight: 700;'>{$otp}</h2>
                                </div>
                                
                                <p style='font-size: 15px;'>
                                    <strong>Important:</strong> This code expires in <span style='color: #e53e3e; font-weight: 600;'>10 minutes</span>. 
                                    Please enter it in your application to verify your identity.
                                </p>
                                
                                <p style='font-size: 15px; margin-top: 25px; padding-top: 20px; border-top: 1px solid #edf2f7;'>
                                    If you didn't request this code, please contact our support team immediately at 
                                    <a href='mailto:support@" . APP_DOMAIN . "' style='color: #2c5282; text-decoration: none;'>support@" . APP_DOMAIN . "</a>
                                </p>
                            </div>
                            
                            <!-- Footer -->
                            <div class='footer'>
                                <p style='margin: 0 0 12px;'>
                                    <strong>" . APP_NAME . "</strong><br>
                                    Making insurance simple and secure
                                </p>
                                <p style='margin: 0; font-size: 12px; line-height: 1.5;'>
                                    This is an automated message, please do not reply directly.<br>
                                    © " . date('Y') . " " . APP_NAME . ". All rights reserved.<br>
                                    <a href='$privacyUrl' style='color: #4a5568; text-decoration: underline;'>Privacy Policy</a> | 
                                    <a href='$popiaUrl' style='color: #4a5568; text-decoration: underline;'>Terms of Service</a>
                                </p>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ";

        $this->mail->send();
    }
    public function sendStaffUserOtp(string $toEmail, string $otp): void
    {
        $this->mail->clearAddresses();
        $this->mail->addAddress($toEmail);
        $this->mail->isHTML(true);
        $this->mail->Subject = 'Your Staff User Credentials OTP Code from ' . APP_NAME;

        // Company logo URL
        $logoUrl = ROOT . '/uploads/logo/1754992023_bleki-and-blackie-logo-white-bg.png';
        // Privacy page URL
        $privacyUrl = ROOT . '/privacy';
        // POPIA page URL
        $popiaUrl = ROOT . '/popia';

        $this->mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>OTP Verification - " . APP_NAME . "</title>
            <style>
                /* Basic email reset */
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
                    background: linear-gradient(135deg, #6c6b1aff 0%, #b0680aff 100%);
                    padding: 25px 0;
                    text-align: center;
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
                                <img src='$logoUrl' alt='" . APP_NAME . " Logo' width='180' style='max-width: 100%; height: auto;'>
                            </div>
                            
                            <!-- Content Section -->
                            <div class='content'>
                                <h1 style='color: #1a202c; margin-top: 0;'>Secure Verification</h1>
                                
                                <p style='font-size: 16px;'> Dear B & B Staff Member <br> Thank you for registering as a user of <strong>" . APP_NAME . "</strong> System. To complete your user registration, please use the verification code below:</p>
                                
                                <div class='otp-box'>
                                    <p style='margin: 0 0 12px; color: #4a5568; font-size: 16px;'>Your One-Time Password</p>
                                    <h2 style='font-size: 36px; margin: 0; color: #c27d07ff; letter-spacing: 8px; font-weight: 700;'>{$otp}</h2>
                                </div>
                                
                                <p style='font-size: 15px;'>
                                    <strong>Important:</strong> This code expires in <span style='color: #e53e3e; font-weight: 600;'>10 minutes</span>. 
                                    Please enter it in your application to verify your identity.
                                </p>
                                
                                <p style='font-size: 15px; margin-top: 25px; padding-top: 20px; border-top: 1px solid #edf2f7;'>
                                    If you didn't request this code, please contact your System Administrator immediately at 
                                    <a href='mailto:freelancer@" . APP_DOMAIN . "' style='color: #2c5282; text-decoration: none;'>freelancer@" . APP_DOMAIN . "</a>
                                </p>
                            </div>
                            
                            <!-- Footer -->
                            <div class='footer'>
                                <p style='margin: 0 0 12px;'>
                                    <strong>" . APP_NAME . "</strong><br>
                                    Making insurance simple and secure
                                </p>
                                <p style='margin: 0; font-size: 12px; line-height: 1.5;'>
                                    This is an automated message, please do not reply directly.<br>
                                    © " . date('Y') . " " . APP_NAME . ". All rights reserved.<br>
                                    <a href='$privacyUrl' style='color: #4a5568; text-decoration: underline;'>Privacy Policy</a> | 
                                    <a href='$popiaUrl' style='color: #4a5568; text-decoration: underline;'>Terms of Service</a>
                                    <a href='$privacyUrl' style='color: #4a5568; text-decoration: underline;'>Privacy Policy</a> | 
                                    <a href='https://techsolutions.jongibrandz.co.za' target='_blank' style='color: #4a5568; text-decoration: underline;'>Jongi Brands Tech Solutions</a>
                                </p>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ";

        $this->mail->send();
    }

    public function sendPolicyAppConfirmation(string $toEmail): void
    {
        $this->mail->clearAddresses();
        $this->mail->addAddress($toEmail);
        $this->mail->isHTML(true);
        $this->mail->Subject = 'Your Policy At ' . APP_NAME ;

        // Company logo URL
        $logoUrl = ROOT . '/uploads/logo/1754992023_bleki-and-blackie-logo-white-bg.png';
        // Privacy page URL
        $privacyUrl = ROOT . '/privacy';
        // POPIA page URL
        $popiaUrl = ROOT . '/popia';

        $this->mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>OTP Verification - " . APP_NAME . "</title>
            <style>
                /* Basic email reset */
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
                    background: linear-gradient(135deg, #6c6b1aff 0%, #b0680aff 100%);
                    padding: 25px 0;
                    text-align: center;
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
                                <img src='$logoUrl' alt='" . APP_NAME . " Logo' width='100%' style='height: auto;'>
                            </div>
                            
                            <!-- Content Section -->
                            <div class='content'>
                                <h1 style='color: #1a202c; margin-top: 0;'>Policy Approved</h1>
                                
                                <p style='font-size: 16px;'>Thank you once again for applying for a policy with <strong>" . APP_NAME . "</strong>. <br> This email serves to inform you that you application was approved and is now active/in force. <br> <span class='text-center'><h2>Welcome On Board!</h2></span> </p>
                                
                                <p style='font-size: 15px; margin-top: 25px; padding-top: 20px; border-top: 1px solid #edf2f7;'>
                                    If you didn't request this code, please contact our support team immediately at 
                                    <a href='mailto:support@" . APP_DOMAIN . "' style='color: #2c5282; text-decoration: none;'>support@" . APP_DOMAIN . "</a>
                                </p>
                            </div>
                            
                            <!-- Footer -->
                            <div class='footer'>
                                <p style='margin: 0 0 12px;'>
                                    <strong>" . APP_NAME . "</strong><br>
                                    Making insurance simple and secure
                                </p>
                                <p style='margin: 0; font-size: 12px; line-height: 1.5;'>
                                    This is an automated message, please do not reply directly.<br>
                                    © " . date('Y') . " " . APP_NAME . ". All rights reserved.<br>
                                    <a href='$privacyUrl' style='color: #4a5568; text-decoration: underline;'>Privacy Policy</a> | 
                                    <a href='$popiaUrl' style='color: #4a5568; text-decoration: underline;'>Terms of Service</a>
                                </p>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ";

        $this->mail->send();
    }
}
