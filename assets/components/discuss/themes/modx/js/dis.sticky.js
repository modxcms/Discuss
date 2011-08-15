/*
* jQuery stickyBar Plugin
* Copyright (c) 2010 Brandon S. <antizeph@gmail.com>
* Version: 1.1.2 (09/14/2010)
* http://plugins.jquery.com/project/stickyBar
* 
* Usage (simple):      $.stickyBar(div);
* Usage (advanced):    $.stickyBar(divTarget, {'showClose' : true, 'divBase' : divBase});
* 
* Notes:    divTarget is the div you want to be stickied (and by default is also divBase).
*           divBase is the target to scroll past to invoke stickyBar.
*           showClose displays a small 'x' that closes stickyBar
*/
(function($){
    $.fn.stickyBar = function(o){
        $.stickyBar(o);
    }

    $.stickyBar = function(divTarget, options){
        var defaults = {
            'divBase'   : '',
            'showClose' : false
        };
        settings = $.extend(defaults, options);

        var wrapped = 0; //initial value
        
        //if divBase is a defined option, set the stickyBarTop value to it, otherwise, use divTarget
        divTargetBase = (settings.divBase) ? divTargetBase = settings.divBase : divTargetBase = divTarget;

        var stickyBarTop = $(divTargetBase).offset().top;
        $(window).scroll(function(){
            var scrollPos = $(window).scrollTop();

            if (scrollPos > stickyBarTop){
                if (wrapped == 0){                
                    $(divTarget).wrap('<div class="sticky">');
                    $(".sticky").css({
                                'position'    : "fixed",
                                'top'         : "0",
                                'left'        : "0",
                                'width'       : "100%",
                                'z-index'     : "9999"
                            });
                    wrapped = 1;

                    if (settings.showClose){
                        $(".sticky").append('<div class="stickyClose" style="left:95%;position:absolute;color:#fff;top:0;left:98%;cursor:pointer">x</div>');
                        $(".stickyClose").click(function(){
                            $(".sticky").slideUp();
                            setTimeout(function(){
                                $(divTarget).unwrap();
                                $(".stickyClose").remove();
                            },400);
                            wrapped = 2; //won't happen again on the page until a refresh
                        });
                    }

                }
            } else {
                if (wrapped == 1){
                    $(divTarget).unwrap();
                    $(".stickyClose").remove();
                    wrapped = 0;
                }
            }
        });
    };
}) (jQuery);