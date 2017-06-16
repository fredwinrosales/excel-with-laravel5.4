@php
/**
 * Created by PhpStorm.
 * User: fredwinrosales
 * Date: 6/8/17
 * Time: 8:41 p.m.
 */
@endphp
<!doctype html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ config('app.name') }}</title>

    <!-- Styles -->
    <link href="{{ asset('public/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('public/css/style.css') }}" rel="stylesheet">
</head>
<body>

    <div class="container-fluid">

        @yield('content')

    </div>

        <!-- Modal -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <form id="form_corrector">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="myModalLabel">Corregir observaci&oacute;n</h4>
                        </div>
                        <div class="modal-body">
                                <div class="form-group">
                                    <div id="alert-info" class="alert"role="alert"></div>
                                    <div class="form-group">
                                        <input type="text" name="field" id="field" tabindex="0"
                                               class="form-control">
                                    </div>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="myModal02" tabindex="-1" role="dialog" aria-labelledby="myModalLabel02">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">Observaciones pendientes</h4>
                    </div>
                    <div class="modal-body">
                        <div id="general-info-warning" class="alert alert-warning general-info" role="alert">
                            Algunas observaciones fueron ignoradas, seguro desea continuar con
                            el proceso de exportaci&oacute;n.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button id="obs_continuar" type="button" class="btn btn-primary">Continuar</button>
                    </div>
                </div>
            </div>
        </div>

    <!-- Scripts -->
    <script src="{{ asset('public/js/app.js') }}"></script>

    @yield('javascript')

</body>
</html>