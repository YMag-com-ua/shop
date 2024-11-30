import $ from "jquery";

export const loader = new class {
  element = '<div id="loader" class="bg-blr-50 p-f z-1000 t-0 wdv-100 hdv-100 fade d-f ai-c jc-c">' +
    '<i class="fa-sharp-duotone fa-solid fa-spinner-third fa-spin fs-50 c-primary"></i>'
    '</div>';

  timeout = null;

  loaderElement() {
    return $('body > #loader');
  }

  show() {
    if (!this.loaderElement().length) {
      $('body').append(this.element);
    }
    this.timeout = setTimeout(() => this.loaderElement().addClass('show'), 300);
  }

  hide() {
    clearTimeout(this.timeout);
    if (this.loaderElement().length) {
      this.loaderElement().removeClass('show');
      setTimeout(() => {
        this.loaderElement().remove();
      }, 200);
    }
  }
};