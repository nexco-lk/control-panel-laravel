<?php

namespace App\Filament\Admin\Resources\EmailResource;

class PostfixConfigGenerator
{
    public function generate(string $email, string $password): string
    {
        $domain = explode('@', $email)[1];
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        return <<<EOT
# Virtual domain configuration
virtual_mailbox_domains = $domain
virtual_mailbox_base = /var/mail
virtual_mailbox_maps = hash:/etc/postfix/vmailbox
virtual_minimum_uid = 100
virtual_uid_maps = static:5000
virtual_gid_maps = static:5000

# SASL authentication
smtpd_sasl_type = dovecot
smtpd_sasl_path = private/auth
smtpd_sasl_auth_enable = yes
smtpd_recipient_restrictions =
    permit_sasl_authenticated,
    permit_mynetworks,
    reject_unauth_destination

# TLS parameters
smtpd_tls_cert_file = /etc/ssl/certs/ssl-cert-snakeoil.pem
smtpd_tls_key_file = /etc/ssl/private/ssl-cert-snakeoil.key
smtpd_use_tls = yes
smtpd_tls_auth_only = yes

# Virtual mailbox
$email $domain/$email/

# User authentication
$email:$hashedPassword
EOT;
    }
}