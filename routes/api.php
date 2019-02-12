<?php

use Illuminate\Http\Request;
use App\Watson\Assistant;
use App\Sabor;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/dialog', function () {
    $message = request()->input('message');
    $context = request()->input('context');

    $assistant = app()->make(Assistant::class);
    $response = $assistant->dialog($message, $context);
    $response = json_decode($response, true);

    // if (isset($response['entities'][0]) and $response['entities'][0]['entity'] == 'identificacao') {
    //     if ($response['entities'][0]['value'] == 'email') {
    //         // lógica para buscar usuário por email
    //     }

    //     if ($response['entities'][0]['value'] == 'telefone') {
    //         // lógica para buscar usuário por telefone
    //     }
    //     $response['output']['text'] = 'consegui trocar a mensagem com base na identificação';
    // }

    $intent = (isset($response['intents'][0])) ? $response['intents'][0]['intent'] : null;

    if ($intent === 'pedido') {
        $tempo_de_entrega = '30 minutos';

        foreach ($response['output']['text'] as $key => $text) {
            $text = str_replace('{ tempo_entrega }', $tempo_de_entrega, $text);

            $context = $response['context'];

            if ($context['troco'] and $context['quantidade'] and $context['pizza']) {
                $pizza = Sabor::where('title', $context['pizza'])->first();
                $valor = $context['quantidade'] * $pizza->price;
                $troco = $context['troco'] - $valor;

                $text = str_replace('{ valor }', $valor . ' reais e ' . $troco . ' de troco',  $text);

                if ($troco < 0 ) {
                    $text = 'O valor informado é menor que  o do pedido, o pedido ficou em ' . $valor;
                }
            }

            $response['output']['text'][$key] = $text;
        }

        $response['output']['text'] = array_unique($response['output']['text']);
    }

    return $response;
});