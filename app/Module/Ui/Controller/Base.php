<?php

declare(strict_types=1);

namespace App\Module\Ui\Controller;

use Air\Core\Controller;
use Air\Core\Exception\ClassWasNotFound;
use Air\Core\Exception\DomainMustBeProvided;
use Air\Crud\Model\Language;
use Air\Model\Exception\CallUndefinedMethod;
use Air\Model\Exception\ConfigWasNotProvided;
use Air\Model\Exception\DriverClassDoesNotExists;
use Air\Model\Exception\DriverClassDoesNotExtendsFromDriverAbstract;
use Air\Model\Meta\Exception\PropertyWasNotFound;
use App\Helper\Route;
use App\Model\General;

abstract class Base extends Controller
{
  /**
   * @return void
   * @throws ClassWasNotFound
   * @throws CallUndefinedMethod
   * @throws ConfigWasNotProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws DomainMustBeProvided
   */
  public function init(): void
  {
    parent::init();

    if ($languageKey = $this->getParam('language')) {
      $language = Language::one([
        'key' => strtolower(trim($languageKey))
      ]);
      if ($language->isDefault) {
        $this->redirect(Route::currentUrlToLanguage($language));
      }
      Language::setDefaultLanguage($language);
    }
  }

  /**
   * @return void
   * @throws CallUndefinedMethod
   * @throws ClassWasNotFound
   * @throws ConfigWasNotProvided
   * @throws DomainMustBeProvided
   * @throws DriverClassDoesNotExists
   * @throws DriverClassDoesNotExtendsFromDriverAbstract
   * @throws PropertyWasNotFound
   */
  public function postRun(): void
  {
    parent::postRun();

    if (!$this->getView()->getDefaultMeta()) {
      $this->getView()->setDefaultMeta(function () {
        return General::one()->meta;
      });
    }

    if ($this->getRequest()->isAjax()) {
      $this->getView()->setLayoutEnabled(false);

      $this->getResponse()->setHeader('title', json_encode(
        $this->getView()->getMeta()->getComputedData()['title']
      ));

      $langLink = [];
      foreach (Language::all() as $lang) {
        $langLink[$lang->key] = Route::currentUrlToLanguage($lang);
      }
      $this->getResponse()->setHeader('language-links', json_encode($langLink));
    }
  }
}