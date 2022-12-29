
/**
 * Check if values in Object are empty or not
 * 
 * @requires Object
 * @returns Boolean
 */
function objectIsEmpty(values) {
    for (const i of Object.keys(values)) {
        if (values[i] == '' || values[i] == null || values[i] == NaN || values[i] == undefined) {
            return true;
        }
    }
    return false;
}

