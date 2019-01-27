$(function(){
    var loading    = $("[data-id='loading']");
    var loadingImg = $("[data-id='loading-img']", loading);

    $(document).ajaxStart(function(){
        loading.show();
        loadingImg.addClass('rotate');
    }).ajaxStop(function(){
        loading.hide();
        loadingImg.removeClass('rotate');
    }).ajaxError(function(event, xhr){
        //if (xhr.status === 403) {
        //    window.location.href = loading.data('url');
        //}
    });

    $(window).bind("load resize", function() {
        var topOffset = 50;
        var width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            topOffset = 100; // 2-row-menu
        }
        var height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 2;
        height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            $("#page-wrapper").css("min-height", (height) + "px");
        }
    });

});