import $ from "jquery";
import {wait} from "../module/wait";
import {toast} from "../module/toast";
import {locale} from "../module/locale";

export const wishlist = new class {
  watch() {

    wait.on('[data-wishlist]', (el) => {

      $(el).find('[data-wishlist-add]').click((btn) => {
        $.get('/wishlist/add', {product: $(btn.currentTarget).data('wishlist-add')}, (wishlistCount) => {
          this.toggleState(el, 'add', wishlistCount);
        });
      });

      $(el).find('[data-wishlist-remove]').click((btn) => {
        $.get('/wishlist/remove', {product: $(btn.currentTarget).data('wishlist-remove')}, (wishlistCount) => {
          this.toggleState(el, 'remove', wishlistCount);
        });
      });
    });
  }

  toggleState(el, action, wishlistCount) {
    if (action === 'add') {
      $(el).find('[data-wishlist-add]').addClass('d-n');
      $(el).find('[data-wishlist-remove]').removeClass('d-n');

      toast.show(locale.l('Чудово! Ви успішно додали товар до обраного!'));

    } else if (action === 'remove') {
      $(el).find('[data-wishlist-remove]').addClass('d-n');
      $(el).find('[data-wishlist-add]').removeClass('d-n');

      toast.show(locale.l('Чудово! Ви успішно видалили товар з обраного!'));
    }

    $('[data-wishlist-badge]').attr('data-badge', wishlistCount.toString());
  }
};