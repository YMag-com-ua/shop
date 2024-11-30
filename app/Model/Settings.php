<?php

declare(strict_types=1);

namespace App\Model;

use Air\Model\ModelAbstract;

/**
 * @collection Settings
 *
 * @property string $id
 *
 * @property boolean $googleOAuthEnabled
 * @property string $googleOAuthClientId
 * @property string $googleOAuthClientSecret
 *
 * @property string $smtpCharSet
 * @property string $smtpEncryption
 * @property integer $smtpPort
 * @property string $smtpHost
 * @property string $smtpUsername
 * @property string $smtpPassword
 * @property string $smtpFromName
 * @property string $smtpFromEmail
 * @property boolean $smtpLogEnabled
 */
class Settings extends ModelAbstract
{
  const string SMTP_CHARSET_ASCII = 'us-ascii';
  const string SMTP_CHARSET_ISO88591 = 'iso-8859-1';
  const string SMTP_CHARSET_UTF8 = 'utf-8';

  const string SMTP_ENCRYPTION_TLS = 'tls';
  const string SMTP_ENCRYPTION_SSL = 'ssl';
}