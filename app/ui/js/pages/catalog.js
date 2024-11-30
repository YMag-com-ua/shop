import Swiper from "swiper";
import $ from "jquery";
import {wait} from "../module/wait";
import {nav} from "../module/nav";

wait.on('[data-swiper-catalog-banner]', (el) => {
  const swiper = new Swiper(el, {
    slidesPerView: 1,
    spaceBetween: 30,
    autoplay: {
      delay: 8000,
      disableOnInteraction: false
    }
  });

  $(el).find('[data-catalog-banner-nav-prev]').click(() => {
    swiper.slidePrev();
  });

  $(el).find('[data-catalog-banner-nav-next]').click(() => {
    swiper.slideNext();
  });
});

$(document).on('click', '[data-form-search] [data-submit]', function () {
  $(this).closest('[data-form-search]').submit();
});

$(document).on('submit', '[data-form-search]', function () {
  const search = $(this).find('[name=search]').val().trim();
  if (search.length > 2) {
    nav.go($(this).attr('action') + '?' + $(this).serialize());
  }
  return false;
});

$(document).on('click', '[data-catalog-sort]', function () {
  $('[name="sort"]').val($(this).data('catalog-sort'));
  $('[data-catalog-form]').submit();

  return false;
});

$(document).on('click', '[data-catalog-show]', function () {
  $('[name="show"]').val($(this).data('catalog-show'));
  $('[data-catalog-form]').submit();

  return false;
});

$(document).on('click', '[data-catalog-filter-change]', function () {
  $('[name="page"]').val('');

  $(this).find('input').prop('checked', !$(this).find('input').prop('checked'));

  if ($(this).find('input').prop('checked')) {
    $(this).addClass('c-primary');
  } else {
    $(this).removeClass('c-primary');
  }

  $('[data-catalog-form]').submit();

  return false;
});

$(document).on('click', '[data-catalog-remove-price-min]', function () {
  $('[name="page"]').val('');
  $('[name="priceMin"]').val('');
  $('[data-catalog-form]').submit();

  return false;
});

$(document).on('click', '[data-catalog-remove-price-max]', function () {
  $('[name="page"]').val('');
  $('[name="priceMax"]').val('');
  $('[data-catalog-form]').submit();

  return false;
});

$(document).on('click', '[data-catalog-remove-is-new]', function () {
  $('[name="page"]').val('');
  $('[name="new"]').prop('checked', false);
  $('[data-catalog-form]').submit();

  return false;
});

$(document).on('click', '[data-catalog-remove-is-sale]', function () {
  $('[name="page"]').val('');
  $('[name="sale"]').prop('checked', false);
  $('[data-catalog-form]').submit();

  return false;
});

$(document).on('click', '[data-catalog-remove-brand]', function () {
  $('[name="page"]').val('');
  $('[name="brands[]"][value="' + $(this).data('catalog-remove-brand') + '"]').prop('checked', false);
  $('[data-catalog-form]').submit();

  return false;
});

$(document).on('click', '[data-catalog-remove-country]', function () {
  $('[name="page"]').val('');
  $('[name="countries[]"][value="' + $(this).data('catalog-remove-country') + '"]').prop('checked', false);
  $('[data-catalog-form]').submit();

  return false;
});

$(document).on('click', '[data-catalog-remove-filter]', function () {
  $('[name="page"]').val('');
  const filter = $(this).data('catalog-remove-filter').split(':');
  const url = filter[0];
  const value = filter[1];

  $('[name="filters[' + url + '][]"][value="' + value + '"]').click();
  $('[data-catalog-form]').submit();

  return false;
});

$(document).on('click', '[data-catalog-page]', function () {
  $('[name="page"]').val($(this).data('catalog-page'));
  $('[data-catalog-form]').submit();

  return false;
});

$(document).on('submit', '[data-catalog-form]', function () {
  let params = [];
  $(this).serializeArray().forEach(({name, value}) => {
    if (value.length) {
      params.push(name + '=' + value);
    }
  });
  if (params.length) {
    params = '?' + params.join('&');
  }
  nav.go($(this).attr('action') + params);
  return false;
});