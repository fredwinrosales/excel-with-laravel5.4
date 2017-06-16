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

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{ Form::open(array('url'=>route('upload-store'),'files'=>true)) }}

        <div class="row">

            <div class="col-md-12">

                <div class="form-group">

                    {{ Form::label('file','Archivo') }}

                    {{ Form::file('file',array('class'=>'form-control')) }}

                </div>

            </div>

        </div>

        <div class="row">

            <div class="col-md-12">

                {{ Form::submit('Cargar archivo', ['class'=>'btn btn-primary']) }}

                {{ Form::reset('Cancelar', ['class'=>'btn btn-default']) }}

            </div>

        </div>

    {{ Form::close() }}

        </div>
    </div>

@endsection

@section('javascript')

@endsection