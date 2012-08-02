// Keyboard shortcuts for browsing pages of lists
// $(document).keyup(handleKey);
// function handleKey(e){
//     var left_arrow = 37;
//     var right_arrow = 39;

//     if (e.target.localName == 'body' || e.target.localName == 'html') {
//             var code = e.which;
//       if (code == left_arrow)
//                 prevPage();
//       else if (code == right_arrow)
//                 nextPage();
//         }
// }

// var newLocation;

// function prevPage(){
//     var href = $('#key-Paginate ul li:first-child a').attr('href');
//     if (href && href != newLocation) {
//         document.location = href;
//         newLocation = href;
//     }
// }

// function nextPage(){
//     var href = $('#key-Paginate ul li:last-child a').attr('href');
//     if (href && href != newLocation) {
//         document.location = href;
//         newLocation = href;
//     }
// }


$(document).keyup(handleKey);
function handleKey(e){
    var left_arrow = 37;
    var right_arrow = 39;

    if (e.target.localName == 'body' || e.target.localName == 'html') {
        if (!e.ctrlKey && !e.altKey && !e.shiftKey && !e.metaKey) {
            var code = e.which;
      if (code == left_arrow)
                prevPage();
      else if (code == right_arrow)
                nextPage();
        }
    }
}

var newLocation;

function prevPage(){
    var href = $('ul.paginate li:first-child a').attr('href');
    if (href && href != newLocation) {
        document.location = href;
        newLocation = href;
    }
}

function nextPage(){
    var href = $('ul.paginate li:last-child a').attr('href');
    if (href && href != newLocation) {
        document.location = href;
        newLocation = href;
    }
}