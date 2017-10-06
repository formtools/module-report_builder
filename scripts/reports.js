$(function() {
  $(".rb_form_section_heading").bind("click", function() {
    var report_section = $(this).parent().find(".rb_report_section");
    var display = report_section.css("display")

    if (display == "none") {
      report_section.slideDown();
    } else {
      report_section.slideUp();
    }
  });

  $("#rb_expand_contract").bind("click", function() {
    if (reports_ns.all_expanded) {
      $(".rb_report_section").slideUp();
      this.innerHTML = $("#rb_expand_label").html();
    } else {
      $(".rb_report_section").slideDown();
      this.innerHTML = $("#rb_contract_label").html();
    }
    reports_ns.all_expanded = !reports_ns.all_expanded;
  });

  reports_ns.all_expanded = ($("#rb_expand_contract").html == $("#rb_expand_label").html()) ? false : true;
});


var reports_ns = {
  all_expanded: false
};
