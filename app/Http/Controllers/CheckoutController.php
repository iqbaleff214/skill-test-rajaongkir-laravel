<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index(): Renderable
    {
        return view('checkout', [
            'provinces' => $this->get_provinces()->getData(),
            'couriers' => ['jne' => 'JNE', 'pos' => 'POS Indonesia', 'tiki' => 'TIKI'],
        ]);
    }

    public function get_provinces(): JsonResponse
    {
        return $this->rajaongkir("https://api.rajaongkir.com/" . env('RAJAONGKIR_PACKAGE') . "/province");
    }

    public function get_cities(int $province): JsonResponse
    {
        return $this->rajaongkir("https://api.rajaongkir.com/" . env('RAJAONGKIR_PACKAGE') . "/city?province=" . $province);
    }

    public function get_cost($courier, $weight, $destination): JsonResponse
    {
        return $this->rajaongkir("https://api.rajaongkir.com/" . env('RAJAONGKIR_PACKAGE') . "/cost", 'POST', [
            'destination' => $destination,
            'weight' => $weight,
            'courier' => $courier,
            'origin' => 501
        ]);
    }

    private function rajaongkir($url, $type = 'GET', $setup = []): JsonResponse
    {
        $curl = curl_init();
        $curl_setup = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $type,
            CURLOPT_HTTPHEADER => ["key: " . env('RAJAONGKIR_API_KEY')],
        ];
        if ($type == 'POST') {
            $curl_setup[CURLOPT_POSTFIELDS] = "origin={$setup['origin']}&destination={$setup['destination']}&weight={$setup['weight']}&courier={$setup['courier']}";
            $curl_setup[CURLOPT_HTTPHEADER][] = "content-type: application/x-www-form-urlencoded";
        }
        curl_setopt_array($curl, $curl_setup);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            $data = ['error' => "cURL Error #:" . $err];
        } else {
            $response=json_decode($response,true);
            $data = $response['rajaongkir']['results'];
        }
        return response()->json($data);
    }
}
