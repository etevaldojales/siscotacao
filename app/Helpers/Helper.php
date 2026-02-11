<?php

namespace App\Helpers;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Timezone;
use PhpParser\Builder\Class_;


class Helper
{
    public static function get_url($url)
    {
        return URL::to($url);
    }
    public static function get_route($route)
    {
        return route($route);
    }
    public static function get_lang($key)
    {
        return Lang::get($key);
    }
    public static function get_date($date)
    {
        return Date::parse($date)->
            format('Y-m-d');
    }
    public static function get_time($time)
    {
        return Date::parse($time)->
            format('H:i:s');
    }

    public static function traduzirLabelPermission($label)
    {
        $param = explode(".", $label);
        $ret = "";
        switch ($param[1]) {
            case 'index':
                $ret = $param[0] . '.visualizar';
                break;
            case 'store':
                $ret = $param[0] . '.salvar';
                break;
            case 'delete':
                $ret = $param[0] . '.excluir';
                break;
        }
        return $ret;
    }

    public static function getStatusCotacao($param)
    {
        $ret = "";
        switch ($param) {
            case 1:
                $ret = "Em aberto";
                break;
            case 2:
                $ret = "Programado";
                break;
            case 3:
                $ret = "Encerrado";
                break;
            case 4:
                $ret = "Cancelado";
                break;
            case 5:
                $ret = "Finalizado";
                break;
                case 6:
                    $ret = "Aprovada";
        }
        return $ret;
    }

    public static function getStatusEnvioCotacao($param)
    {
        $ret = "";
        switch ($param) {
            case 1:
                $ret = "Não Enviado";
                break;
            case 2:
                $ret = "Enviado";
                break;
        }
        return $ret;
    }

    public static function getUnidadeItem($v)
    {
        switch ($v) {
            case 1:
                return 'Kg';
            case 2:
                return 'Cx';
            case 3:
                return 'Unid';
            case 4:
                return 'Saco';
            case 5:
                return 'Metro';
        }
    }}

