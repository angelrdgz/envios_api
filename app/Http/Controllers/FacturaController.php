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
    { }

    public function invoices()
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, env('FACTURA_ENDPOINT') . "/cfdi33/list");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "F-PLUGIN: " . env('FACTURA_PLUGIN'),
            "F-Api-Key: " . env('FACTURA_API_KEY'),
            "F-Secret-Key: " . env('FACTURA_SECRET_KEY')
        ));

        $response = curl_exec($ch);

        return json_decode($response, true);

        curl_close($ch);
    }

    public function createInvoice($payment, $client)
    {

        for ($x = 1; $x <= 1; $x++) {
            $total = ($payment["total"] - ($payment["total"] * 0.16));
            $Conceptos[] = [
                'ClaveProdServ' => '81112107',
                'Cantidad' => '1',
                'ClaveUnidad' => 'E48',
                'Unidad' => 'Unidad de servicio',
                'ValorUnitario' => $total,
                'Descripcion' => 'Desarrollo a la medida',
                'Descuento' => '0',
                'Impuestos' => [
                    'Traslados' => [
                        ['Base' => $payment["total"], 'Impuesto' => '002', 'TipoFactor' => 'Tasa', 'TasaOCuota' => '0.160000', 'Importe' => ($payment["total"] * 0.16)],
                    ]
                ],
            ];
        }

        $ch = curl_init();
        $fields = [
            "Receptor" => ["UID" => $client["uid_factura"]],
            "TipoDocumento" => "factura",
            "UsoCFDI" => "G03",
            "Redondeo" => 2,
            "Conceptos" => $Conceptos,
            "FormaPago" => "01",
            "MetodoPago" => 'PUE',
            "Moneda" => "MXN",
            "CondicionesDePago" => "Pago en una sola exhibición",
            "Serie" => 4541,
            "EnviarCorreo" => 'true',
            "InvoiceComments" => ""
        ];

        $jsonfield = json_encode($fields);


        curl_setopt($ch, CURLOPT_URL, env('FACTURA_ENDPOINT') . "/cfdi33/create");
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

        return json_decode($response, true);

        curl_close($ch);
    }

    public function cancelInvoice($uid)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, env('FACTURA_ENDPOINT') . "/cfdi33/".$uid."/cancel");
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

        return json_decode($response, true);
    }

    public function invoice($uid, $type)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, env('FACTURA_ENDPOINT') ."/cfdi33/".$uid."/".$type);
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

    public function newCliente($data)
    {
        $ch = curl_init();
        $fields = [
            "nombre" => $data["name"],
            "apellidos" => $data["lastname"],
            "email" => $data["email"],
            "email2" => NULL,
            "email3" => NULL,
            "telefono" => $data["phone"],
            "razons" => $data["business_name"],
            "rfc" => $data["rfc"],
            "calle" => $data["address"],
            "numero_exterior" => $data["num_ext"],
            "numero_interior" => $data["num_int"],
            "codpos" => $data["zip_code"],
            "colonia" => $data["neight"],
            "estado" => $data["state"],
            "ciudad" => $data["city"],
            "delegacion" => '',
        ];

        $jsonfield = json_encode($fields);

        curl_setopt($ch, CURLOPT_URL, "http://devfactura.in/api/v1/clients/create");
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

        return json_decode($response, true);

        curl_close($ch);
    }
}
