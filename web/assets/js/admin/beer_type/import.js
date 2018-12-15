$(function(){
    var input = $("[data-id='import-input']");

    $("[data-id='import-button']").click(function(){
        input.click();
    });
    input.change(function(){
        showTypes();
    });
    $("[data-id='import-process']").click(function(){
        importTypes();
    });

    $("[data-role='import-error-close']").click(function(){
        $(this).parent("[data-role='import-error']").fadeOut();
    });
});

/**
 * Extract and display types from csv
 */
function showTypes()
{
    var input  = $("[data-id='import-input']");
    var inputDOM     = input.get(0);

    if (!inputDOM.files || !inputDOM.files[0]) {
        return;
    }

    var file          = input.get(0).files[0];
    var reader        = new FileReader();
    var fileError     = $("[data-id='import-file-error']");
    var importProcess = $("[data-id='import-process']");

    resetList();
    importProcess.hide();
    if (file.type !== 'text/csv') {
        fileError.show();
        return;
    }
    reader.onload = function() {
        var types = reader.result.split("\n");

        $.each(types, function(index, value){
            types[index] = value.split(";")[0]
        });
        if (types[0] !== 'name') {
            fileError.show();
            return;
        }
        types.splice(0, 1);
        if (types.length == 0) {
            return;
        }
        importProcess.show();
        $.each(types, function(index, value){
            createTypePreview(value);
        });
    };
    reader.readAsBinaryString(file);
}

/**
 * Call type import action
 */
function importTypes()
{
    var url          = $("[data-id='import-process']").data('url');
    var data         = new FormData();
    var input        = $("[data-id='import-input']");
    var inputDOM     = input.get(0);
    var fileError    = $("[data-id='import-file-error']");
    var importError  = $("[data-id='import-error']");

    if (!inputDOM.files || !inputDOM.files[0]) {
        return;
    }
    var file = input.get(0).files[0];

    data.append('file', file);

    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        cache: false,
        contentType: false,
        processData: false,
        success: function(data){
            resetList();
            $.each(data, function(index, obj){
                createTypePreview(obj.name, obj.error, true);
            })
        },
        error: function(xhr){
            resetList();
            if (xhr.status === 400) {
                fileError.show();
            } else {
                importError.show();
            }
        }
    });
}

/**
 * Reset type list and hide errors
 */
function resetList()
{
    var fileError     = $("[data-id='import-file-error']");
    var importError   = $("[data-id='import-error']");
    var typesList     = $("[data-id='types']");

    typesList.html(null);
    importError.hide();
    fileError.hide();
}

/**
 * @param name
 * @param error
 * @param useColor
 */
function createTypePreview(name, error, useColor)
{
    var typesList    = $("[data-id='types']");
    var typeTemplate = $("[data-id='type-template']");
    var newType      = typeTemplate.clone();
    var labelClass   = useColor ? 'label-success' : 'label-default';
    var typeLabel    = $("[data-role='type-name']", newType);

    if (error) {
        if (useColor) {
            labelClass = 'label-danger';
        }
        $("[data-role='type-error']", newType).text(error);
    }
    typeLabel.text(name);
    typeLabel.addClass(labelClass);
    newType.removeAttr('data-id');
    typesList.append(newType);
    newType.show();
}