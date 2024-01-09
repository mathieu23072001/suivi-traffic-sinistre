<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class VoieService
{

    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getPointGeographique($idOSM) : array {
     $way_id = $idOSM;
        $query = "[out:json];way($way_id);(._;>;);out;";
        $url = "http://overpass-api.de/api/interpreter?data=" . urlencode($query);
     $response = $this->client->request(
         'GET',
         $url
     );
     $data = null;
     $statusCode = $response->getStatusCode();
     if($statusCode!= 200){
         return  [];
     }
     $content = $response->getContent();
     $data = json_decode($content, true);

// extraire les points géographiques
        $points = array();
        foreach ($data["elements"] as $element) {
            if ($element["type"] == "node") {
                $points[] = array(
                    "lat" => $element["lat"],
                    "lon" => $element["lon"]
                );
            }
        }
        return $points ;
 }
}