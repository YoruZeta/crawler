<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class WSController extends Controller
{
    public function index(){
    
        $url = 'https://news.ycombinator.com/';
        $xpath = self::extract_xpath($url);

        $titles = $xpath->evaluate('//tr[@class="athing"]//td[@class="title"]//a[@class="storylink"]');
        $ranks = $xpath->evaluate('//tr[@class="athing"]//td[@class="title"]//span[@class="rank"]');
        $points = $xpath->evaluate('//td[@class="subtext"]//span[@class="score"]');
        $commments = $xpath->evaluate('//td[@class="subtext"]//a[3]');
    
        foreach ($titles as $key => $title) {
            $result[$key] = [
                "rank" => $ranks[$key]->textContent,
                "title" => $title->textContent,
            ];
        }

        foreach ($points as $key => $point) {
            $result[$key] = [
                "points" => $points[$key]->textContent,
                "rank" => $result[$key]["rank"],
                "title" => $result[$key]["title"],
            ];
        }

        foreach ($commments as $key => $commment) {
            $result[$key] = [
                "comments" => $commments[$key]->textContent,
                "rank" => $result[$key]["rank"],
                "title" => $result[$key]["title"],
                "points" => $result[$key]["points"],
               
            ];
        }

        return view('crawler', ['result' => $result]);
    }

    public function home(){

        return View::make('crawler');

    }

    public function extract_xpath($url){
        $httpClient = new \GuzzleHttp\Client();
        $response = $httpClient->get($url);
        $htmlString = (string) $response->getBody();
        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($htmlString);
        $xpath = new \DOMXPath($doc);
        return $xpath;
    }



}
