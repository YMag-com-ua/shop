import $ from "jquery";
import {wait} from "../module/wait";
import {toast} from "../module/toast";
import {locale} from "../module/locale";
import {nav} from "../module/nav";

export const compare = new class {
  watch() {

    wait.on('[data-compare-add]', (btn) => $(btn).click((btn) => {
      $.get('/compare/add', {product: $(btn.currentTarget).data('compare-add')}, (compareCount) => {
        this.toggleState(btn.currentTarget, 'add', compareCount);
      });
    }));

    wait.on('[data-compare-remove]', (btn) => $(btn).click((btn) => {
      $.get('/compare/remove', {product: $(btn.currentTarget).data('compare-remove')}, (compareCount) => {
        this.toggleState(btn.currentTarget, 'remove', compareCount);
      });
    }));
  }

  toggleState(el, action, compareCount) {
    const container = $(el).closest('[data-compare]');

    if (action === 'add') {
      if (container.length) {
        $(container).find('[data-compare-add]').addClass('d-n');
        $(container).find('[data-compare-remove]').removeClass('d-n');
      }

      toast.show(locale.l('Чудово! Ви успішно додали товар до порівняння!'));

    } else if (action === 'remove') {
      if (container.length) {
        $(container).find('[data-compare-remove]').addClass('d-n');
        $(container).find('[data-compare-add]').removeClass('d-n');
      }

      toast.show(locale.l('Чудово! Ви успішно видалили товар із порівняння!'));

      if (location.pathname.search('/compare') !== -1) {
        nav.reload();
      }
    }

    $('[data-compare-badge]').attr('data-badge', compareCount.toString());
  }
};