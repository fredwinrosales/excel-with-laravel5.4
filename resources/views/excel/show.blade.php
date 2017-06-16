@php
/**
 * Created by PhpStorm.
 * User: frosales <fredwinrosales@gmail.com>
 * Date: 06/06/2017
 * Time: 09:46 AM
 */
@endphp

@extends('layout.app')

@section('content')

    <h3 class="page-header">{{ config('app.name') }}</h3>

    <div id="msg_error" class="alert alert-danger" role="alert" style="display: none">
        Se encontraron errores en validaci&oacute;n de datos sobre el archivo excel,
        para continuar con el proceso debe realizar las correcciones haciendo clic sobre
        los elementos se&ntilde;alados
    </div>

    <div id="msg_success" class="alert alert-success general-info" role="alert" style="display: none">

        <span style="font-weight: bold">&iexcl;Correcciones completadas!</span>
        <br>
        Los datos fueron corregidos completamente, ahora puede almacenar la informaci&oacute;n en el sistema
        haciendo clic en el bot&oacute;n exportar.
        <br>
        {{ Form::open(array('url'=>route('excel-store'),'files'=>true, 'name'=>'form_exportar')) }}
        {{ Form::hidden('file', $file) }}
        {{ Form::hidden('excel', '', ['id'=>'excel']) }}
        {{ Form::submit('Exportar',  ['class'=>'btn btn-success']) }}
        {{ Form::close() }}

    </div>

    <div id="general-info-warning" class="alert alert-warning general-info" role="alert" style="display: none">
        Se aplic&oacute; formato fecha (dd/MM/yyyy hh:mm) pero los valores pudieron ser afectados,
        por favor verificar.
    </div>
    <div id="general-info-danger" class="alert alert-danger general-info" role="alert" style="display: none">
        Se encontraron errores al realizar la validaci&oacute;n de los datos.
    </div>
    <div id="general-info-info" class="alert alert-info general-info" role="alert" style="display: none">
        No se encontraron registros.
        <br>
        <a type="button" class="btn btn-primary" href="{{ route('index') }}">Cargar nuevo</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
            @foreach ($rules as $key_rule => $rule)
                <th bgcolor="white" style="text-transform: uppercase">{{ $key_rule }}</th>
            @endforeach
            </thead>
            <tbody>
            @foreach ($sheets as $rows)
                @foreach ($rows as $index => $items)
                    <tr id="{{ $index }}">
                        @php
                        $tabindex=0;
                        @endphp
                        @foreach ($items as $key => $item)
                            @php
                                $text=($item->html)?$item->html:$item->text;
                                $class=null;
                                $popover=null;
                                $style=null;
                                if($item->error>0) {
                                    if($item->warning) {
                                        $class='alert-warning';
                                    } else {
                                        $class='alert-danger';
                                        $tabindex++;
                                    }
                                    $popover='
                                        data-toggle="popover"
                                        title="Observaciones"
                                        data-content="
                                            <ul>
                                                <li>'.implode("</li><li>", $item->messages).'</li>
                                            </ul>
                                        "
                                    ';
                                }
                            @endphp

                            <td class="{{ $key }} field {{ ($class===null)?'':$class }}"
                                {!! ($popover===null)?'':$popover  !!}
                                {!! ($class==='alert-danger')?'tabindex="'.$tabindex.'"':'' !!}>{!! $text  !!}</td>
                        @endforeach
                    </tr>
                @endforeach
            @endforeach
            </tbody>
        </table>
    </div>

@endsection

@section('javascript')
    <script src="{{ asset('public/js/modal.js') }}"></script>
    <script src="{{ asset('public/js/tooltip.js') }}"></script>
    <script src="{{ asset('public/js/popover.js') }}"></script>
    <script src="{{ asset('/resources/assets/js/excel.js') }}"></script>
    <script>

        var excel = new Excel('{{ $url }}');

        var continuar = false;

        $(document).ready(function(){

            $('[data-toggle="popover"]').popover({
                trigger: "hover focus",
                placement: "auto",
                container: "body",
                html: true
            });

            excel.set_url();

            excel.do_dialog();

        });
    </script>

@endsection