<?php

declare(strict_types=1);

namespace App\Module\Ui\Controller;

use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Exception\DomainMustBeProvided;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use App\Model\LegalPrivacyPolicy;
use App\Model\LegalRefund;
use App\Model\LegalRules;
use App\Model\LegalWarranty;
use Exception;

class Legal extends Base
{
  /**
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws DomainMustBeProvided
   * @throws Exception
   */
  public function init(): void
  {
    parent::init();

    $page = match ($this->getRouter()->getAction()) {
      'warranty' => LegalWarranty::one(),
      'rules' => LegalRules::one(),
      'refund' => LegalRefund::one(),
      'privacyPolicy' => LegalPrivacyPolicy::one(),
    };

    $this->getView()->setMeta($page->meta);

    $this->getView()->setVars([
      'title' => $page->title,
      'richContent' => $page->richContent
    ]);

    $this->getView()->setScript('index/service-page');
  }

  public function warranty(): void
  {
  }

  public function rules(): void
  {
  }

  public function refund(): void
  {
  }

  public function privacyPolicy(): void
  {
  }
}