<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

class UploadController extends Controller
{

    public function index()
    {
        //
    }

    public function create()
    {

        return view('excel.create');
    }

    public function store(Request $request)
    {

        $messages = [
            'required' => 'Requiere un archivo de tipo XLS o XLSX.',
            'mimes' => 'Requiere un archivo de tipo XLS o XLSX.'
        ];

        $this->validate($request, [
            'file' => 'required|mimes:xls,xlsx'
        ], $messages);

        $file=$request->file('file');

        $name =
            uniqid()
            .'-'.
            str_replace(
                ' ', '-', str_replace(
                    '-', '',
                strtolower($file->getClientOriginalName()
                )
            )
            );

        $file->storeAs('excel', $name);

        return redirect(route('excel-show', ['id'=>$name]));
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
