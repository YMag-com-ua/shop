<?php

return [
  '/' => [],

  '/payment-delivery' => ['action' => 'paymentDelivery'],
  '/about' => ['action' => 'about'],
  '/contact' => ['action' => 'contact'],

  '/legal/warranty' => ['controller' => 'legal', 'action' => 'warranty'],
  '/legal/rules' => ['controller' => 'legal', 'action' => 'rules'],
  '/legal/refund' => ['controller' => 'legal', 'action' => 'refund'],
  '/legal/privacy-policy' => ['controller' => 'legal', 'action' => 'privacyPolicy'],

  '/async/categories' => ['controller' => 'async', 'action' => 'categories'],
  '/async/mobile-menu' => ['controller' => 'async', 'action' => 'mobileMenu'],
  '/async/phrases' => ['controller' => 'async', 'action' => 'phrases'],

  '/catalog' => ['controller' => 'catalog'],
  '/product/:product' => ['controller' => 'product', 'action' => 'index'],
  '/productQuickView/:product' => ['controller' => 'product', 'action' => 'quickView'],

  '/cart/preview' => ['controller' => 'cart', 'action' => 'preview'],
  '/cart/add' => ['controller' => 'cart', 'action' => 'add',],
  '/cart/remove' => ['controller' => 'cart', 'action' => 'remove',],

  '/cart/plus' => ['controller' => 'cart', 'action' => 'minusPlus',],
  '/cart/minus' => ['controller' => 'cart', 'action' => 'minusPlus',],

  '/cart/coupon' => ['controller' => 'cart', 'action' => 'coupon',],
  '/cart/remove-coupon' => ['controller' => 'cart', 'action' => 'removeCoupon',],
  '/cart/warehouses' => ['controller' => 'cart', 'action' => 'warehouses',],
  '/cart/saveOrderData' => ['controller' => 'cart', 'action' => 'saveOrderData',],

  '/cart' => ['controller' => 'cart',],
  '/cart/checkout' => ['controller' => 'cart', 'action' => 'checkout'],
  '/cart/checkout/callback' => ['controller' => 'cart', 'action' => 'checkoutCallback'],
  '/cart/checkout/thank-you' => ['controller' => 'cart', 'action' => 'checkoutThankYou'],

  '/blog' => ['controller' => 'blog'],
  '/article/:article' => ['controller' => 'blog', 'action' => 'article'],

  '/wishlist' => ['controller' => 'wishlist'],
  '/wishlist/add' => ['controller' => 'wishlist', 'action' => 'add'],
  '/wishlist/remove' => ['controller' => 'wishlist', 'action' => 'remove'],

  '/compare' => ['controller' => 'compare'],
  '/compare/add' => ['controller' => 'compare', 'action' => 'add'],
  '/compare/remove' => ['controller' => 'compare', 'action' => 'remove'],

  '/auth' => ['controller' => 'auth'],
  '/auth/google' => ['controller' => 'auth', 'action' => 'google'],
  '/auth/register' => ['controller' => 'auth', 'action' => 'register'],
  '/auth/confirm' => ['controller' => 'auth', 'action' => 'confirm'],
  '/auth/forgot-password' => ['controller' => 'auth', 'action' => 'forgotPassword'],

  '/profile' => ['controller' => 'profile'],
  '/profile/edit' => ['controller' => 'profile', 'action' => 'edit'],

  '/profile/change-email-request' => ['controller' => 'profile', 'action' => 'changeEmailRequest'],
  '/profile/change-email' => ['controller' => 'profile', 'action' => 'changeEmail'],

  '/profile/change-password' => ['controller' => 'profile', 'action' => 'changePassword'],

  '/profile/orders' => ['controller' => 'profile', 'action' => 'orders'],
  '/profile/addresses' => ['controller' => 'profile', 'action' => 'addresses'],
  '/profile/remove-address' => ['controller' => 'profile', 'action' => 'removeAddress'],

  '/profile/wishlist' => ['controller' => 'profile', 'action' => 'wishList'],
  '/profile/viewed-products' => ['controller' => 'profile', 'action' => 'viewedProducts'],
  '/profile/logout' => ['controller' => 'profile', 'action' => 'logout'],
];