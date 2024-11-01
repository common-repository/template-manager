function uniqid() {
  var ts = String(new Date().getTime()), i = 0, out = '';
  for (i = 0; i < ts.length; i += 2) {
    out += Number(ts.substr(i, 2)).toString(36);
  }
  return ('gtm' + out);
}

jQuery(document).on("click", ".repeatable-add", function () {
  field = jQuery(this).closest('td').find('.custom_repeatable li:last').clone(true);
  fieldLocation = jQuery(this).closest('td').find('.custom_repeatable li:last');
  jQuery('input.repeatable_heading', field).val('').attr('name', function (index, name) {
    return name.replace(/(\d+)/, function (fullMatch, n) {
      return Number(n) + 1;
    });
  });
  jQuery('input.repeatable_uniqid', field).val(uniqid()).attr('name', function (index, name) {
    return name.replace(/(\d+)/, function (fullMatch, n) {
      return Number(n) + 1;
    });
  });
  field.insertAfter(fieldLocation, jQuery(this).closest('td'));
  return false;
});

jQuery(document).on("click", ".repeatable-remove", function () {
  if ($('.repeatable-remove').length > 1) {
    jQuery(this).parent().remove();
  } else {
    alert('At least one block needed.');
  }
  return false;
});
jQuery(document).ready(function ($) {
  jQuery('.custom_repeatable').sortable({
    opacity: 0.6,
    //revert: true,
    cursor: 'move',
    handle: '.sort',
    connectWith: '.custom_repeatable'
  });
  jQuery('.custom_repeatable').disableSelection();
});
