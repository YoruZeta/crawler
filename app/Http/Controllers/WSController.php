<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WSController extends Controller
{
    public function index(){
    
        $httpClient = new \GuzzleHttp\Client();
        $response = $httpClient->get('https://news.ycombinator.com/');
        $htmlString = (string) $response->getBody();
        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($htmlString);
        $xpath = new \DOMXPath($doc);
        $titles = $xpath->evaluate('//tr[@class="athing"]//td[@class="title"]//a[@class="storylink"]');
        $ranks = $xpath->evaluate('//tr[@class="athing"]//td[@class="title"]//span[@class="rank"]');
        $points = $xpath->evaluate('//td[@class="subtext"]//span[@class="score"]');

        $count = 0;
        $result = [];
        foreach ($titles as $key => $title) {
            $result[$count] = [
                "rank" => $ranks[$key]->textContent,
                "title" => $title->textContent,
            ];
            $count++;
        }

        $count = 0;
 
        foreach ($points as $key => $point) {
          
            $result[$count] = [
                "points" => $points[$key]->textContent,
                "rank" => $result[$count]["rank"],
                "title" => $result[$count]["title"],
            ];
            $count++;
  
        }

        echo("<pre>");
        var_dump($result );
        echo("</pre>");
        
        return json_encode($result);

    }

}
