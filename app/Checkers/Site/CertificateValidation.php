<?php

namespace App\Checkers\Site;

use App\Checkers\SiteChecker;
use App\Site;
use Spatie\SslCertificate\SslCertificate;

class CertificateValidation extends SiteChecker
{
    public function check(Site $site)
    {
        $certificate = SslCertificate::createForHostName($site->host);

        if ( $certificate->isExpired() ) {
            // todo: trigger emergency
        }

        if ( $certificate->expirationDate()->diffInDays() < 7 ) {
            // todo: trigger critical
        }

        if ( $certificate->expirationDate()->diffInDays() < 14 ) {
            // todo: trigger warning
        }

        // todo: don't throw exception which indicates everything is ok
    }
}
