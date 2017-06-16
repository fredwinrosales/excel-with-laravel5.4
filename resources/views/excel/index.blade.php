<?php
/**
 * Created by PhpStorm.
 * User: frosales <fredwinrosales@gmail.com>
 * Date: 06/06/2017
 * Time: 08:43 AM
 */
?>

@extends('layout.app')

@section('content')

    <h3 class="page-header">

        {{ config('app.name') }}

    </h3>

    <div class="row">

        <div class="col-md-6">

            <div id="msg_success" class="alert alert-info general-info">

                <span style="font-weight: bold">Los datos fueron cargados correctamente</span>
                <br>
                <a type="button" class="btn btn-primary" href="{{ route('index') }}">Cargar nuevo</a>

            </div>

        </div>

    </div>

@endsection

@section('javascript')

@endsection