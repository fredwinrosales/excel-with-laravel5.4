/**
 * Created by frosales <fredwinrosales@gmail.com> on 06/06/2017.
 */
var Excel = (function () {

    /**
     * Contructor de la clase
     *
     * @param url
     * @constructor
     */
    function Excel(url) {
        this.url = url;
    };

    var key_field,
        url_base,
        element,
        excel,
        class_,
        row;

    /**
     * Setea valor de url base
     *
     */
    Excel.prototype.set_url = function() {

        url_base = this.url;

    };

    /**
     * Metodo publico para formulario en ventana modal
     *
     */
    Excel.prototype.do_dialog = function() {

        if($('tbody tr').length) {

            next_field();

        } else {

            $('#general-info-info').show();

        }

        $("td.alert-danger, td.alert-warning").on("click", function () {
            select_field($(this));
        }).keypress(function(e) {
            if(e.which == 13) {
                select_field($(this));
            }
        });

        $('#myModal').on('hidden.bs.modal', function (event) {

            if(element.hasClass('alert-danger')) {

                element.focus();

            } else {

                next_field();

            }

        });

        $("#form_corrector").submit(function(event){
            $("#alert-info").hide();
            check_field();
            event.preventDefault();
        });

        $('[name=form_exportar]').submit(function(){

            get_excel();

            if($('#general-info-warning').is(":visible") && !continuar) {

                $('#myModal02').modal('show');

                $('#obs_continuar').on('click', function(){

                    continuar = true;
                    $('[name=form_exportar]').submit();

                });

                return false;

            }

        });

    };

    /**
     * Selecciona campo de la tabla
     *
     * @param field
     */
    function select_field(field){
        element = field;
        $("#alert-info").html(element.attr('data-content')).show();

        if(element.hasClass('alert-danger')){

            $("#alert-info").removeClass('alert-warning');
            $("#alert-info").addClass('alert-danger');

        } else
            {

            $("#alert-info").removeClass('alert-danger');
            $("#alert-info").addClass('alert-warning');

        }

        $('#field').val(clean_html_text(element));
        get_key_field();
        $('#myModal').modal('show');
        console.log(element.html());
    }

    /**
     * Obtiene nombre de la colunma relacionada al campo
     *
     */
    function get_key_field() {
        key_field = element.attr('class').split(' ')[0];

    };

    /**
     * Valida campo relizando una peticion POST al servidor
     *
     */
    function check_field() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: url_base+'/excel/check_field',
            data: {
                'key_field':key_field,
                'field':$('#field').val()
            },
            success: function(data){
                if(typeof data == 'undefined'){
                    console.log('Error al intenter obtener data');
                } else {
                    data = data.replace( /&quot;/g, '"' );
                    data = JSON.parse(data);

                    if(data.messages == null){

                        element.html(
                            (data.html)?data.html:data.text
                        );

                        element
                            .popover('destroy')
                            .removeClass('alert-danger')
                            .removeClass('alert-warning')
                            .addClass('alert-success');

                        $('#myModal').modal("hide");

                        next_field();

                    } else {

                        /*element
                            .removeClass('alert-success')
                            .addClass('alert-danger')
                            .attr('data-content', '<ul><li>'+data.messages.join("</li><li>")+'</li></ul>');*/

                        $("#alert-info")
                            .html('<ul><li>'+data.messages.join('</li><li>')+'</li></ul>')
                            .addClass('alert-danger')
                            .show();

                    }
                }
            }
        });
    };

    /**
     * Eliminacion de etiquetas html desde un texto especifico
     *
     * @param element
     * @returns {XML|string|void|*}
     */
    function clean_html_text(e) {

        return e.html().replace(/<[^>]*>?/g, '');

    }

    function next_field() {
        var success=true;
        $('tr .alert-danger').each(function(){
            success=false;
            scroll_position($(this).position().top);
            return false;

        });
        if(success) {

            $('#general-info-danger').hide();
            $('#msg_success').show();
            $('#msg_success [type=submit]').focus();

            scroll_position($('#msg_success [type=submit]').position().top);

        } else {

            $('#msg_success').hide();
            $('#general-info-danger').show();

        }
        success=true;
        $('tr .alert-warning').each(function(){

            success=false;
            return false;

        });
        if(success) {

            $('#general-info-warning').hide();

        } else {

            $('#general-info-warning').show();

        }

        $('tr .alert-danger').each(function(){
            $(this).focus();
            return false;

        });

    }

    /**
     * Obtiene datos de excel y los coloca en campo de formulario en formato json string
     *
     */
    function get_excel() {

        excel = [];
        class_ = '';

        $('tbody tr').each(function() {
            row = {};
            $('#'+this.id+' td').each(function() {

                class_ = $(this).attr('class').split(' ')[0];
                row[class_] = clean_html_text($(this));

            });
            excel.push(row);
        });

        excel = JSON.stringify(excel);
        $('#excel').val(excel);

    }

    /**
     * Aplica scroll en la ubicacion del elemento seleccionado
     *
     * @param pos
     */
    function scroll_position(pos) {

        $('body,html').stop(true,true).animate({
            scrollTop: pos - 150
        },0);

    }

    return Excel;

})();