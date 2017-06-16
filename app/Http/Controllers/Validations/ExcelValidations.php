<?php
/**
 * Created by PhpStorm.
 * User: frosales <fredwinrosales@gmail.com>
 * Date: 06/06/2017
 * Time: 10:46 AM
 */
namespace App\Http\Controllers\Validations;

use DateTime;
use Exception;

/**
 * Class ExcelValidations
 *
 * @package App\Http\Controllers\Validations
 */
trait ExcelValidations
{
    private $cols;
    private $rows;
    private $validation;
    protected $field_html;
    protected $messages;
    private $warning;
    protected $one_time;

    /**
     * Validacion de excel
     *
     * @param $sheets
     * @param $rules
     * @return object
     */
    protected function do_validation($sheets, $rules){
        $count = 0;
        $items = 0;
        foreach ($sheets as $key_sheet => $sheet){
            foreach ($sheet as $key_rows => $rows){
                $count++;
                foreach ($rows as $key_field => $field){
                    if($this->in_rule(trim($key_field), $rules)) {
                        $items++;
                        $this->field_html = null;
                        $this->warning = false;
                        $this->validation_messages($field, $rules[trim($key_field)]);
                        $this->validation[$key_field] = (object)[
                            'text'=>trim($field),
                            'messages'=>$this->messages,
                            'html'=>$this->field_html,
                            'style'=>$this->get_style(),
                            'error'=>count($this->messages),
                            'warning'=>$this->warning
                        ];
                    }
                }
                $data[$key_sheet][$key_rows] = (object) $this->validation;
                /*if($count == 1){
                    break;
                }*/
            }
        }
        $data = $this->clean_rows_empty($data);
        if($items){
            return (object) $data;
        } else {
            return [];
        }
    }

    /**
     * Obtiene estilos segun validacion
     *
     * @return string
     */
    protected function get_style() {
        $style = ($this->messages != null) ? trim('background-color:#FCF8E3; cursor: pointer') : '';
        return $style;
    }

    /**
     * Obtiene mensajes para tooltip
     *
     * @return string
     */
    protected function get_title() {
        $title = ($this->messages != null) ? trim(implode(' | ', $this->messages)) : '';
        return $title;
    }

    /**
     * Validaciones para determinar mensajes
     *
     * @param $field
     * @param $rules
     * @return null
     */
    protected function validation_messages($field, $rules) {

        $this->messages = null;

        foreach($rules as $key=>$value) {
            $message = $this->validation_field($field, $key, $value);
            if($message){
                $this->messages[] = $message;
            }
        }
        return $this->messages;

    }

    /**
     * Validacion por campo especifico
     *
     * @param $field
     * @param $key
     * @param $value
     * @return null|string
     */
    private function validation_field($field, $key, $value) {

        $message = null;

        switch ($key) {
            case 'type':

                if(!is_numeric($field) && ($value === 'numeric')) {
                    $message = 'El valor debe ser de tipo num&eacute;rico';
                }
                if($value === 'date') {
                    if($this->date_valida($field) != null) {

                        $message = $this->date_valida($field);

                    }
                }
                break;
            case 'max':
                if(mb_strlen($field, 'UTF-8') > $value) {
                    $message = '
                    El valor debe tener una longitud m&aacute;xima de '.$value.' caracteres. 
                    El resto de caracteres ser&aacute;n desechados
                    ';
                    $this->set_html($field, $value);
                }
                break;
            case 'between':

                if(
                    mb_strlen($field, 'UTF-8') > $value['max'] ||
                    mb_strlen($field, 'UTF-8') < $value['min']
                ) {
                    $message = '
                    El campo debe tener una longitud m&aacute;xima de '.$value['max'].' y minima de '.$value['min'];
                }
                break;
            case 'required':

                if($value && ($field == null || empty($field))) {
                    $message = 'El campo no puede ser vacio';
                }
                break;
            // Deprecade
            case 'format':

                $message = $this->validation_format($field, $value);
                break;

        }
        return $message;
    }

    /**
     * Validacion de formato de campo
     *
     * @param $field
     * @param $format
     * @return null|string
     */
    private function validation_format($field, $format) {

        $message = null;

        switch ($format) {
            case 'dd/MM/yyyy hh:mm':

                // Deprecade
                if(!$this->validateDateTime(trim($field), 'd/m/Y H:i')){
                    $message = 'El valor no tiene el formato válido de fecha dd/MM/yyyy hh:mm';
                }
                break;
        }
        return $message;

    }

    /**
     * Señala en color el exceso de caracteres tras validacion de campo
     *
     * @param $field
     * @param $max
     */
    private function set_html($field, $max) {

        for ($i=0; $i<mb_strlen($field, 'UTF-8'); $i++) {

            if($i == $max) {
                $this->field_html.='<span style="color: #FF0000; font-weight: bold">'.$field[$i];
            } else
            {
                $this->field_html.=$field[$i];
            }

        }
        $this->field_html.='</span>';

    }

    /**
     * Formato de fecha para insertar en base de datos
     *
     * @param $key
     * @param $i
     * @return false|string
     */
    protected function get_value($key, $i) {

        switch ($key) {
            case 'fecha':
                $date = explode('/', explode(' ',$i)[0]);
                $date = date_create($date[2]."-".$date[1]."-".$date[0]." ".explode(' ',$i)[1]);
                return date_format($date,"Y-m-d H:i");
                break;
            default:
                return $i;
                break;
        }
        return 'value';

    }

    private function date_valida($value) {
        if($value) {
            $date = explode('/', explode(' ', $value)[0]);
            try {
                $date = date_create($date[2] . "-" . $date[1] . "-" . $date[0] . " " . explode(' ', $value)[1]);
                if(date_format($date, 'H:i') == '00:00' && $this->one_time === true) {

                    $this->warning = true;
                    return '
                        Se aplico formato fecha (dd/MM/yyyy hh:mm) pero el valor podr&iacute;a ser incorrecto.
                        Por favor verificar.
                        ';

                }

            } catch (Exception $e) {
                return '
                        El valor no tiene el formato v&aacute;lido de fecha dd/MM/yyyy hh:mm
                        ';
            }
        } else {
            return null;
        }
    }

    /**
     * Eliminacion de lineas que estan totalmente vacias
     *
     * @param $sheets
     * @return array
     */
    private function clean_rows_empty($sheets){
        $result = [];
        foreach ($sheets as $key_sheet => $sheet) {
            foreach ($sheet as $key_rows => $rows) {
                $is_empty = 0;
                foreach ($rows as $key_field => $field) {
                    if($field->text == null){
                        $is_empty++;
                    }
                }
                if($is_empty < 9){
                    $result[$key_sheet][$key_rows] = $rows;
                }
            }
        }
        return $result;
    }

    /**
     * Validacion de colunmas que esten en el listado de reglas
     *
     * @param $key_field
     * @param $rules
     * @return bool
     */
    private function in_rule($key_field, $rules){
        $in_rule = false;
        foreach ($rules as $key=>$rule){
            if($key == $key_field){
                $in_rule = true;
            }
        }
        return $in_rule;
    }

    /**
     * Validacion de formato fecha
     *
     * @param $dateStr
     * @param $format
     * @return bool
     */
    public function validateDateTime($dateStr, $format)
    {
        //date_default_timezone_set('UTC');
        $date = DateTime::createFromFormat($format, $dateStr);
        return $date && ($date->format($format) === $dateStr);
    }

}