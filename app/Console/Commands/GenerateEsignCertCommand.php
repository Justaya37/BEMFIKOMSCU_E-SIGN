<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateEsignCertCommand extends Command
{
    protected $signature = 'esign:generate-cert {--force : Overwrite existing certificates}';
    protected $description = 'Generate self-signed X.509 digital signature certificate for BEM FIKOM';

    public function handle(): int
    {
        $certDir = storage_path('app/private/certs');
        File::ensureDirectoryExists($certDir);

        $certPath = $certDir . '/bem_fikom.crt';
        $keyPath = $certDir . '/bem_fikom.key';
        $cnfPath = $certDir . '/openssl.cnf';

        if (File::exists($certPath) && File::exists($keyPath) && !$this->option('force')) {
            $this->info("Certificate already exists at {$certPath}. Use --force to regenerate.");
            return self::SUCCESS;
        }

        // Create standard minimal openssl.cnf to ensure compatibility across Windows/Laragon/Herd/Linux
        $minimalCnf = <<<CONF
[ req ]
default_bits = 2048
default_md = sha256
distinguished_name = req_distinguished_name
x509_extensions = v3_ca
prompt = no

[ req_distinguished_name ]
C = ID
ST = DKI Jakarta
L = Jakarta
O = BEM Fakultas Ilmu Komputer
OU = Biro Kesekretariatan
CN = BEM FIKOM E-Sign Authority
emailAddress = esign@bemfikom.ac.id

[ v3_ca ]
subjectKeyIdentifier = hash
authorityKeyIdentifier = keyid:always,issuer
basicConstraints = critical, CA:true
keyUsage = critical, digitalSignature, cRLSign, keyCertSign
CONF;

        File::put($cnfPath, $minimalCnf);

        $configArgs = [
            'config' => $cnfPath,
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
            'digest_alg' => 'sha256',
        ];

        // 1. Generate Private Key
        $privateKey = openssl_pkey_new($configArgs);
        if (!$privateKey) {
            $err = openssl_error_string();
            // Try system fallback paths
            $systemCnf = dirname(PHP_BINARY) . '/extras/ssl/openssl.cnf';
            if (File::exists($systemCnf)) {
                $configArgs['config'] = $systemCnf;
                $privateKey = openssl_pkey_new($configArgs);
            }
        }

        if (!$privateKey) {
            $this->error('Failed to create private key: ' . openssl_error_string());
            return self::FAILURE;
        }

        // Export private key to file
        openssl_pkey_export_to_file($privateKey, $keyPath, null, $configArgs);

        // 2. Generate CSR (Certificate Signing Request)
        $dn = [
            'countryName' => 'ID',
            'stateOrProvinceName' => 'DKI Jakarta',
            'localityName' => 'Jakarta',
            'organizationName' => 'BEM Fakultas Ilmu Komputer',
            'organizationalUnitName' => 'Biro Kesekretariatan',
            'commonName' => 'BEM FIKOM E-Sign Authority',
            'emailAddress' => 'esign@bemfikom.ac.id',
        ];

        $csr = openssl_csr_new($dn, $privateKey, $configArgs);
        if (!$csr) {
            $this->error('Failed to create CSR: ' . openssl_error_string());
            return self::FAILURE;
        }

        // 3. Self-sign certificate (valid for 5 years = 1825 days)
        $cert = openssl_csr_sign($csr, null, $privateKey, 1825, $configArgs);
        if (!$cert) {
            $this->error('Failed to sign certificate: ' . openssl_error_string());
            return self::FAILURE;
        }

        openssl_x509_export_to_file($cert, $certPath);

        $this->info("Successfully generated X.509 digital certificate:");
        $this->line(" - Certificate: {$certPath}");
        $this->line(" - Private Key: {$keyPath}");

        return self::SUCCESS;
    }
}
