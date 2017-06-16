<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Validations\ExcelValidations;
use Excel;
use App\Encargo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\UrlGenerator;
use DateTime;

/**
 * Class ExcelController
 *
 * @package App\Http\Controllers
 */
class ExcelController extends Controller
{
    use ExcelValidations;
    private $results;
    private $rules;
    private $path;
    private $uploaded;
    private $url;

    /**
     * ExcelController constructor.
     *
     * @param UrlGenerator $url
     */
    public function __construct(UrlGenerator $url) {

        $this->url = $url->to('/');
        $this->path = 'app\\excel\\';
        $this->uploaded = '_uploaded';
        $this->rules = [
            'albaran' => [
                'type' => 'numeric',
                'max' => 10,
                'required' => true
            ],
            'destinatario' => [
                'type' => 'string',
                'max' => 28,
                'required' => true
            ],
            'direccion' => [
                'type' => 'string',
                'max' => 250,
                'required' => true
            ],
            'poblacion' => [
                'type' => 'string',
                'max' => 10,
                'required' => false
            ],
            'cp' => [
                'type' => 'string',
                'between' => [
                    'min' => 5,
                    'max' => 5,
                ],
                'required' => true
            ],
            'provincia' => [
                'type' => 'string',
                'max' => 20,
                'required' => true
            ],
            'telefono' => [
                'type' => 'string',
                'max' => 10,
                'required' => true
            ],
            'observaciones' => [
                'type' => 'string',
                'max' => 500,
                'required' => false
            ],
            'fecha' => [
                'type' => 'date',
                'format' => 'dd/MM/yyyy hh:mm',
                'required' => true
            ]
        ];

    }

    /**
     * Almacena datos de excel en base de datos
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        $data = json_decode($request->input('excel'));

        $test = [];

        foreach ($data as $x) {

            foreach ($x as $key=>$i) {

                $arr[$key] = $this->get_value($key, $i);
                //$arr[$key] = $i;

            }
            $test[] = $arr;
        }

        Encargo::insert($test);

        Storage::move(
            'excel/'.$request->input('file'),
            'excel/'.$request->input('file').$this->uploaded
        );

        return redirect(route('excel-show', ['id'=>$request->input('file')]));

    }

    /**
     * Muestra datos de excel para analisis y correccion
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|
     * \Illuminate\Http\RedirectResponse|
     * \Illuminate\Routing\Redirector|
     * \Illuminate\View\View|
     * string
     */
    public function show($id)
    {

        $uploaded = Storage::disk('excel')
            ->exists($id.$this->uploaded);

        if($uploaded) {

            return $this->success();

        }

        $file = storage_path('app/excel/'.$id);

        if(!file_exists($file)){
            return redirect('/');
        }

        Excel::load($file, function($reader) {
            $reader->formatDates(true, 'd/m/Y H:i');
            $this->results = $reader->get();
        });

        $this->one_time = true;
        $this->results = $this->do_validation($this->results, $this->rules);

        return view('excel.show', [
            'file' => $id,
            'sheets' => $this->results,
            'rules' => $this->rules,
            'url' => $this->url
        ]);
    }

    /**
     * Validacion de campo especifico
     *
     * @param Request $request
     * @return string
     */
    public function check_field(Request $request) {

        $this->field_html = null;

        $this->one_time = false;

        $this->validation_messages(
            $request->input('field'),
            $this->rules[$request->input('key_field')]
        );

        return json_encode((object)[
            'text'=>trim($request->input('field')),
            'messages'=>$this->messages,
            'html'=>$this->field_html,
            'style'=>$this->get_style(),
            'title'=>$this->get_title()
        ]);

    }

    public function success() {

        return view('excel.index');

    }

}
