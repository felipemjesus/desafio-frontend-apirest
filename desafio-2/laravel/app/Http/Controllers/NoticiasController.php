<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class NoticiasController extends Controller
{
    public function index(Request $request)
    {
        $url = 'http://www.marcha.cnm.org.br/webservice/noticias?';

        $pagina = $request->get('pagina');
        if ($pagina) {
            $url .= 'page=' . $pagina;
        }

        $busca = $request->get('busca');
        if ($busca) {
            $url .= $pagina ? '&' : '';
            $url .= 'pesquisa=' . $busca;
        }

        $resultado = (new Client())->get($url);
        $resultado = json_decode($resultado->getBody(), true);

        $noticias = $this->formatarNoticia($resultado['noticias']);

        $numeroDePaginas = intdiv($resultado['total_noticias'], 15);

        return view('noticias.index', compact('noticias', 'numeroDePaginas', 'pagina', 'busca'));
    }

    public function formatarNoticia($noticias)
    {
        foreach ($noticias as &$noticia) {
            $data = \DateTime::createFromFormat('d/m/Y', $noticia['data_formatada']);
            $noticia['data_formatada'] = $data->format('l, d \d\e F \d\e Y');

            $texto = strip_tags($noticia['texto']);
            $noticia['texto'] = substr($texto, 0, strrpos(substr($texto, 0, 300), ' '));
        }

        return $noticias;
    }
}
