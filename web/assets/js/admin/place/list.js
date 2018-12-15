$(function(){
    initPlacesDatatable($('#places-new-list'));
    initPlacesDatatable($('#places-list'));
});

function initPlacesDatatable(table)
{
    table.SearchableDatatable({
        columnDefs: [
            {"targets": 3, "orderable": false}
        ],
        responsive: true,
        serverSide: true,
        ajax: {
            url: table.data('url')
        }
    });
}
