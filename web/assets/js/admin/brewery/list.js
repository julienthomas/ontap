$(function(){
    initDatatable($('#breweries-list'));
});

/**
 * @param table
 */
function initDatatable(table)
{
    table.SearchableDatatable({
        columnDefs: [
            {"targets": 3, "orderable": false}
        ],
        responsive: true,
        serverSide: true,
        ajax: {
            url: table.data('url'),
            type: 'POST'
        }
    });
}
