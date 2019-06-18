(function($){
    jQuery.fn.mi_tooltip = function(opciones) {
        var element = document.createElement("div");
        $(element).addClass(opciones.tooltipcss).hide();
        document.body.appendChild(element);
        return this.each(function() {
            $(this).hover(function() {
                $(element).show();
                $(element).html($(this).attr("data-cell"));
                $(this).mousemove(function(e) {
                    $(element).css({
                        "position": "absolute",
                        "top": e.pageY + opciones.offsetY,
                        "left": e.pageX + opciones.offsetX
                    });
                });
            }, function() {
            $(element).hide()
            });
        });
    };
})(jQuery)