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
});