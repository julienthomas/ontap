// In case of 403 response
$.fn.dataTable.ext.errMode = 'none';

(function ($){
    $.fn.SearchableDatatable = function(options){
        var defaultOptions = {
            orderCellsTop: true,
            dom: 'ltipr',
            ajax: {
                type: 'POST'
            },
            error: function(xhr) {
                console.log(xhr.status);
            }
        };
        var table = $(this);
        var datatableOptions = $.extend(true, defaultOptions, options);

        buildFilters(table);
        table.DataTable(datatableOptions);

        $("[data-role='datatable-filter-apply']", table).click(function(){
            applyFilters(table);
        });
        $("[data-role='datatable-filter']", table).keyup(function(event){
            var code = event.keyCode || event.which;
            if (code == 13) {
                applyFilters(table);
            }
        });
    };

    /**
     * Set the filters on the second header row
     */
    function buildFilters(table)
    {
        $("[data-id='datatable-filters'] th", table).each(function(){
            if ($(this).data('type')) {
                var type = $(this).data('type');
                if (type == 'text') {
                    buildInputFilter($(this));
                } else if (type == 'choice') {
                    buildSelectFilter($(this))
                } else if (type == 'search') {
                    buildSubmitButton($(this));
                }
            }
        });
    }

    /**
     * Build an input filter
     *
     * @param cell
     */
    function buildInputFilter(cell)
    {
        var input = $("<input type='text' data-role='datatable-filter' class='form-control' style='width: 100%;' placeholder='" + cell.text() + "'>");
        cell.html(input);
    }

    /**
     * Build a select filter
     *
     * @param cell
     */
    function buildSelectFilter(cell)
    {
        var select  = $("<select data-role='datatable-filter' class='form-control' style='width: 100%;'>");
        var choices = cell.data('choices');
        var option = $('<option selected>');
        option.text();
        select.append(option);
        $.each(choices, function(id, name){
            var option = $("<option value='" + id + "'>");
            option.text(name);
            select.append(option);
        });
        cell.html(select);
    }

    /**
     * Build a search button
     *
     * @param cell
     */
    function buildSubmitButton(cell)
    {
        var button = $("<button type='button' data-role='datatable-filter-apply' class='btn btn-default pull-right'>");
        button.html(cell.html());
        cell.html(button);
    }

    /**
     * Apply serach value for each column and redraw the table
     */
    function applyFilters(table)
    {
        $("[data-type]", table).each(function(index) {
            var val = null;
            if ($(this).data('type') == 'text') {
                val = $('input', this).val();
                if (val !== null) {
                    table.DataTable().columns(index).search(val);
                }
            }
        });
        table.DataTable().draw();
    }
})(jQuery);
