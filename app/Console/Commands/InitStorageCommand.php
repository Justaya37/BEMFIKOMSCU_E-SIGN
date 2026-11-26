<?php

namespace App\Console\Commands;

use App\Services\EsignAssetGenerator;
use Illuminate\Console\Command;

class InitStorageCommand extends Command
{
    protected $signature = 'esign:init-storage {--force : Overwrite existing certificates and assets}';
    protected $description = 'Initialize persistent storage volume directories and cryptographic assets for BEM FIKOM';

    public function handle(): int
    {
        $this->info('Memeriksa dan menyiapkan direktori serta aset volume penyimpanan persisten...');
        EsignAssetGenerator::generateAll($this->option('force'));
        $this->info('Direktori volume dan aset kriptografi berhasil diinisialisasi.');

        return self::SUCCESS;
    }
}
