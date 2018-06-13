var last_cep = 0;
var address;
var lat;
var lng;
var wsconf;
function wscep(conf)
{
    //parametros padrao true
    if(!conf){
        conf = {
            'auto': true,
            'map' : '',
            'wsmap' : ''
        };
    }
    wsconf = conf;
    //evento keyup no campo cep opcional
    if(wsconf.auto == true){
        $('#cep').live('keyup',function(){
            var cep = $.trim($('#cep').val()).replace('_','');
            if(cep.length >= 9){
                if(cep != last_cep){
                    busca();
                }
            }
        });         
    }else{
        var btn_busca = '<div class="input-append input-prepend"><span class="add-on">CEP</span>';
        btn_busca += '<input id="cep" name="cep" style="width:139px!important" type="text" maxlength="9" placeholder="Informe o CEP" />';
        btn_busca += '<button class="btn btn_handler" type="button">Busca</button></div>';
        $('.cep-label').replaceWith(btn_busca)
        $('.btn_handler').live('click',function(){
            busca();
        })
    }    
}
//busca o cep
function busca(){
    var cep = $.trim($('#cep').val());
    var url = 'http://clareslab.com.br/ws/cep/json/'+cep+'/';    
    if ($.browser.msie) {
        var url = 'ie.php';    
    }
    if(cep.length != 0) {
        $.post(url, {
                cep: cep
            },
            function (rs) {
                console.log('CEP:', rs);
                if (rs != 0) {
                    rs = $.parseJSON(rs);
                    address = rs.endereco.toUpperCase() + ', ' + rs.bairro.toUpperCase() + ', ' + rs.cidade.toUpperCase() + ', ' + ', ' + rs.uf.toUpperCase();
                    if (wsconf.map != '') {
                        setMap(wsconf.map);
                    }
                    $('#endereco').val(rs.endereco.toUpperCase());
                    $('#bairro').val(rs.bairro.toUpperCase());

                    $("#estados option").each(function () {
                        if ($(this).text().toUpperCase() == rs.uf.toUpperCase())
                            $(this).attr('selected', true);
                    });

                    var cont = 0;
                    if (!buscaCidades($('#estados').val(), base_url, 0, rs.cidade.toUpperCase())) {
                        $('#localidade_cidade_id option').each(function () {
                            $(this).attr('selected', false);
                            if ($(this).text().toUpperCase() == rs.cidade.toUpperCase()) {
                                $(this).attr('selected', true);

                            }
                        })
                    }

                    $('#cep').removeClass('inputError');
                    if ($('#numero').val().length == 0) {
                        $('#numero').focus();
                    }
                    $('#numero').live('change', function () {
                        address = rs.endereco.toUpperCase() + ', ' + $('#num').val() + ', ' + rs.bairro.toUpperCase() + ', ' + rs.cidade.toUpperCase() + ', ' + ', ' + rs.uf.toUpperCase();
                        if (wsconf.map != '') {
                            setMap(wsconf.map);
                        }
                    })
                    last_cep = cep;
                }
                else {
                    $('#cep').addClass('inputError');
                    $('#cep').focus();
                    last_cep = 0;
                }

            });
    }
}
 
function wsmap(cep,num,elm)
{
    var url = 'http://clareslab.com.br/ws/cep/json/'+cep+'/';    
    if ($.browser.msie) {
        var url = 'ie.php';    
    }    
    $.post(url,{
        cep:cep
    },
    function (rs) {
        if(rs != 0){
        rs = $.parseJSON(rs);
            address = rs.endereco + ', ' + num + ', ' + rs.bairro + ', ' + rs.cidade + ', ' + ', ' + rs.uf;
            setMap(elm);
        }
    })
}
function setMap(elm)
{
    GMaps.geocode({
        address: address,
        callback: function(results, status) {            
            if (status == 'OK') {
                //console.log(elm);
                $('#'+elm).show();
                var latlng = results[0].geometry.location;
                lat = latlng.lat();
                lng = latlng.lng()
                map = new GMaps({
                    div: elm,
                    lat: lat,
                    lng: lng,
                    scrollwheel: false,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    streetViewControl: true,
                    zoom: 14
                })
                map.addMarker({
                    lat: lat,
                    lng: lng,
                    title: address,
                    infoWindow: {
                        content: '<p>'+address+'</p>'
                    }
                });
                map.setCenter(lat, lng);
            }
        }
    });   
     
}
