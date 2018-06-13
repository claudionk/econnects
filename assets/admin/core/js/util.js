/**
 * Created by Leonardo Lazarini on 12/01/2017.
 */

/**
 * Verifica se está no Array por uma chave
 * @param array
 * @param search_key
 * @param key
 * @returns {boolean}
 */
function inArray(array, search_key, key)
{
    var in_array = false;
    array.forEach(function(obj)
    {
        if(obj[search_key] == key)
        {
            in_array = true;
        }
    })
    return in_array;
}


function formatMoeda(num){
    if((typeof num == typeof undefined) || (typeof num == typeof null)){
        num = 0;
    }
    num = num.toString().replace(/\$|\,/g, '');
    if (isNaN(num))
    {
        num = "0";
    }

    sign = (num == (num = Math.abs(num)));
    num = Math.floor(num * 100 + 0.50000000001);
    cents = num % 100;
    num = Math.floor(num / 100).toString();

    if (cents < 10)
    {
        cents = "0" + cents;
    }
    for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++)
    {
        num = num.substring(0, num.length - (4 * i + 3)) + '.' + num.substring(num.length - (4 * i + 3));
    }

    return (((sign) ? '' : '-') + num + ',' + cents)
}

function kendo_get_class_true_false(id)
{
    var s = '';
	if(id > 0){
		s = 'SIM';
	}else{
		s = 'NÃO';
	}
		
	return s;	
}