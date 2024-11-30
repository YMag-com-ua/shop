import {wait} from "../module/wait";
import $ from "jquery";
import {toast} from "../module/toast";
import {locale} from "../module/locale";
import {loader} from "../module/loader";
import {nav} from "../module/nav";
import {popup} from "../module/popup/popup";

export const profile = new class {
  watch() {
    wait.on('[data-form-personal-data]', (form) => $(form).on('submit', function () {
      $(form).find('.has-error').removeClass('has-error');

      $.post('/profile/edit', $(form).serialize())
        .then(() => toast.show(locale.l('Дані успішно збережені')))
        .catch(({responseJSON}) => responseJSON.forEach((sel) => {
          $('[name="' + sel + '"]').parent().addClass('has-error');
        }));

      return false;
    }));

    wait.on('[data-form-change-email]', (form) => $(form).on('submit', function () {
      $(form).find('.has-error').removeClass('has-error');
      loader.show();

      $.post('/profile/change-email-request', $(form).serialize())
        .then(() => {
          loader.hide();
          $(form)[0].reset();
          toast.show(locale.l('На новий емейл посилання відправлено'));
        })
        .catch(({responseJSON}) => {
          loader.hide();
          responseJSON.forEach((sel) => {
            $('[name="' + sel + '"]').parent().addClass('has-error');
          });
        });

      return false;
    }));

    wait.on('[data-form-change-password]', (form) => $(form).on('submit', function () {
      $(form).find('.has-error').removeClass('has-error');
      loader.show();

      $.post('/profile/change-password', $(form).serialize())
        .then(() => {
          loader.hide();
          $(form)[0].reset();
          toast.show(locale.l('Новий пароль збережено'));
        })
        .catch(({responseJSON}) => {
          loader.hide();
          responseJSON.forEach((sel) => {
            $('[name="' + sel + '"]').parent().addClass('has-error');
          });
        });

      return false;
    }));

    wait.on('[data-form-address]', (form) => $(form).on('submit', function () {
      $(form).find('.has-error').removeClass('has-error');

      $.post('/profile/addresses', $(form).serialize())
        .then(() => {
          toast.show(locale.l('Адреса збережена'));
          nav.reload();
        })
        .catch(({responseJSON}) => responseJSON.forEach((sel) => {
          $('[name="' + sel + '"]').parent().addClass('has-error');
        }));

      return false;
    }));

    wait.on('[data-remove-address]', (btn) => $(btn).click(function () {
      popup.confirm(locale.l('Видалити адресу?')).then(() => {
        $.post('/profile/remove-address', {address: $(this).data('remove-address')})
          .then(() => {
            toast.show(locale.l('Адреса видалена'));
            nav.go('/profile/addresses');
          });
      });
    }));
  }
};