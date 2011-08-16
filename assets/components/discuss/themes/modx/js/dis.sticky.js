function UpdateTableHeaders() {
   $("body").each(function() {

       var el             = $(this),
           offset         = el.offset(),
           scrollTop      = $(window).scrollTop(),
           floatingHeader = $(".floatingHeader", this)

       if ((scrollTop > offset.top) && (scrollTop < offset.top + el.height())) {
           floatingHeader.css({
            "visibility": "visible"
           });
       } else {
           floatingHeader.css({
            "visibility": "hidden"
           });
       };
   });
}

// DOM Ready
$(function() {

   var clonedHeaderRow;

   $("body").each(function() {
       clonedHeaderRow = $(".dis-sticky-actions", this);
       clonedHeaderRow
         .before(clonedHeaderRow.clone())
         .css("width", clonedHeaderRow.width())
         .addClass("floatingHeader");

   });

   $(window)
    .scroll(UpdateTableHeaders)
    .trigger("scroll");

});