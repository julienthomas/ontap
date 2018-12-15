$(function(){
    initDatatable($('#beer-types-list'));
});

/**
 * @param table
 */
function initDatatable(table)
{
    table.SearchableDatatable({
        columnDefs: [
            {"targets": 2, "orderable": false}
        ],
        responsive: true,
        serverSide: true,
        ajax: {
            url: table.data('url'),
            type: 'POST'
        }
    });
}
