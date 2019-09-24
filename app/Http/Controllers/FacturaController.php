<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class FacturaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    public function getInvoices()
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, env('FACTURA_ENDPOINT')."/cfdi33/list");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "F-PLUGIN: " . env('FACTURA_PLUGIN'),
            "F-Api-Key: " . env('FACTURA_API_KEY'),
            "F-Secret-Key: " . env('FACTURA_SECRET_KEY')
        ));

        $response = curl_exec($ch);

        return die($response);

        curl_close($ch);
    }

    public function createInvoice($data)
    {

        for ($x = 1; $x <= 1; $x++) {
            $Conceptos[] = [
                'ClaveProdServ' => '81112107',
                'Cantidad' => '1',
                'ClaveUnidad' => 'E48',
                'Unidad' => 'Unidad de servicio',
                'ValorUnitario' => '100',
                'Descripcion' => 'Desarrollo a la medida',
                'Descuento' => '0',
                'Impuestos' => [
                    'Traslados' => [
                        ['Base' => '100', 'Impuesto' => '002', 'TipoFactor' => 'Tasa', 'TasaOCuota' => '0.160000', 'Importe' => '16'],
                    ]
                ],
            ];
        }

        $ch = curl_init();
        $fields = [
            "Receptor" => ["UID" => "55c0fdc675XXX"],
            "TipoDocumento" => "factura",
            "UsoCFDI" => "P01",
            "Redondeo" => 2,
            "Conceptos" => $Conceptos,
            "FormaPago" => "01",
            "MetodoPago" => 'PUE',
            "Moneda" => "MXN",
            "CondicionesDePago" => "Pago en una sola exhibición",
            "Serie" => 1,
            "EnviarCorreo" => 'true',
            "InvoiceComments" => ""
        ];

        $jsonfield = json_encode($fields);


        curl_setopt($ch, CURLOPT_URL, env('FACTURA_ENDPOINT')."/cfdi33/create");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonfield);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "F-PLUGIN: " . env('FACTURA_PLUGIN'),
            "F-API-KEY: " . env('FACTURA_API_KEY'),
            "F-SECRET-KEY: " . env('FACTURA_SECRET_KEY')
        ));

        $response = curl_exec($ch);

        return die($response);

        curl_close($ch);
    }

    public function cancelInvoice($data)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, env('FACTURA_ENDPOINT')."/cfdi33/cfdi_uid/cancel");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "F-PLUGIN: " . env('FACTURA_PLUGIN'),
            "F-Api-Key: " . env('FACTURA_API_KEY'),
            "F-Secret-Key: " . env('FACTURA_SECRET_KEY')
        ));

        $response = curl_exec($ch);
        curl_close($ch);

        var_dump($response);
    }
}
