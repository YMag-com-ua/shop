import $ from "jquery";
import {wait} from "./module/wait";
import {popup} from "./module/popup/popup";
import {offcanvas} from "./module/offcanvas";
import {nav} from "./module/nav";
import {tab} from "./module/tab";
import {badge} from "./module/badge";
import {bgImage} from "./module/bg-image";
import {async} from "./module/async";
import {timer} from "./module/timer";
import "./module/inputmask";

import {cart} from "./common/cart";
import {checkout} from "./common/checkout";
import "./common/header";
import "./common/gallery";
import "./common/widget";

import "./pages/catalog";
import "./pages/contact";
import {locale} from "./module/locale";
import {richRadio} from "./common/rich-radio";
import {swiperNav} from "./common/swiper";
import {wishlist} from "./common/wishlist";
import {compare} from "./common/compare";
import {auth} from "./common/auth";
import {profile} from "./common/profile";

nav.callback.push(() => init());
nav.beforeCallback.push(() => {
  offcanvas.close();
  popup.close();
});

nav.watch();
wait.watch();
tab.watch();
popup.watch();
badge.watch();
bgImage.watch();
async.watch();
offcanvas.watch();
richRadio.watch();
swiperNav.watch();
wishlist.watch();
compare.watch();
auth.watch();
profile.watch();

locale.ready(() => {
  cart.watch();
  checkout.watch();
})

const init = () => {
  const dropdowns = $('.dropdown-box');
  if ($('[data-welcome]').length) {
    $('[data-category-button]').addClass('show');
    dropdowns.attr('style', '');
  } else {
    $('[data-category-button]').removeClass('show');
    dropdowns.attr('style', 'display: none !important');
    timer(50, () => dropdowns.attr('style', ''));
  }

  $('[data-input-mask]').each(function () {
    $(this).mask($(this).data('input-mask'));
  });

  $('[role="tooltip"]').remove();
};

if ($(window).width() > 768) {
  wait.on('ul.categories', (el) => {
    $('ul.categories > li > ul').css('min-height', $(el).height() + "px");
  });
}

$('[data-switch-theme]').dblclick(() => switchTheme());

init();