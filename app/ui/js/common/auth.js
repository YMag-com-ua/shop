import {wait} from "../module/wait";
import $ from "jquery";
import {loader} from "../module/loader";

export const auth = new class {
  watch() {
    wait.on('[data-form-register]', (form) => $(form).on('submit', function () {

      $(form).find('.has-error').removeClass('has-error');
      $(form).find('[data-message]').addClass('d-n');

      loader.show();

      $.post('/auth/register', $(form).serialize())
        .then(() => {
          loader.hide();
          $(form).find('[data-message]').removeClass('d-n');
        })
        .catch(({responseJSON}) => {
          loader.hide();
          responseJSON.forEach((sel) => {
            $('[name="' + sel + '"]').parent().addClass('has-error');
          });
        });

      return false;
    }));

    wait.on('[data-form-auth]', (form) => $(form).on('submit', function () {
      $(form).find('[name="password"]').parent().removeClass('has-error');

      $.post('/auth', $(form).serialize())
        .then(() => window.location.href = '/profile')
        .catch(() => $(form).find('[name="password"]').parent().addClass('has-error'));

      return false;
    }));

    wait.on('[data-form-forgot-password]', (form) => $(form).on('submit', function () {
      $(form).find('[name="login"]').parent().removeClass('has-error');
      $(form).find('[data-message]').addClass('d-n');

      loader.show();

      $.post('/auth/forgot-password', $(form).serialize())
        .then(() => {
          loader.hide();
          $(form).find('[name="login"]').parent().removeClass('has-error');
          $(form).find('[data-message]').removeClass('d-n');
        })
        .catch(() => {
          loader.hide();
          $(form).find('[name="login"]').parent().addClass('has-error');
        });

      return false;
    }));
  }
};