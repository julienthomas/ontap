/**
 * @param options
 */
function sortOptions(options)
{
    options.sort(function(option1, option2) {
        if ($(option1).val() == null || $(option2).val() == null) {
            return 0;
        }
        return sort(option1.text, option2.text);
    });
}

/**
 * @param optGroups
 */
function sortOptGroups(optGroups)
{
    optGroups.sort(function(optGroup1, optGroup2) {
        return sort($(optGroup1).attr('label'), $(optGroup2).attr('label'));
    });
}

/**
 * @param text1
 * @param text2
 * @returns {number}
 */
function sort(text1, text2)
{
    for (var i = 0; i < text1.length; i++) {
        if (i >= text2.length || text1[i] > text2[i]) {
            return 1;
        } else if (text1[i] < text2[i]) {
            return -1;
        }
    }
    return 0;
}