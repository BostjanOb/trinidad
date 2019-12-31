<?php

namespace App\Checkers\Site;

use App\Checkers\Checker;
use Carbon\Carbon;
use Spatie\SslCertificate\SslCertificate;

class CertificateValidation implements Checker
{
    public function check($model, array $arguments)
    {
        $certificate = SslCertificate::createForHostName($site->host);

        if ($certificate->isExpired()) {
            // todo: trigger emergency
        }

        if ($certificate->expirationDate()->diffInDays() < 7) {
            // todo: trigger critical
        }

        if ($certificate->expirationDate()->diffInDays() < 14) {
            // todo: trigger warning
        }
        // todo: don't throw exception which indicates everything is ok
    }

    public function nextRun(): ?Carbon
    {
        // TODO: Implement nextRun() method.
    }


}
