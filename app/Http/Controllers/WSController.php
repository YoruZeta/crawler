<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class WSController extends Controller
{
    public function index()
    {

        $result = self::get_path();
        return view('crawler', ['result' => $result]);

    }

    public function extract_xpath($url)
    {

        $httpClient = new \GuzzleHttp\Client();
        $response = $httpClient->get($url);
        $htmlString = (string) $response->getBody();
        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($htmlString);
        $xpath = new \DOMXPath($doc);
        return $xpath;

    }

    public function search(Request $request)
    {

        switch ($request->submitbutton) {
            case 'more':
                $case = 'more';
                break;
            case 'less':
                $case = 'less';
                break;
            case 'all':
                $case = 'all';
                break;
        }
        $result = ($case == 'all') ? self::get_path() : self::order($case);
        return view('crawler', ['result' => $result]);

    }

    public function order($case)
    {

        $result = self::get_path();

        foreach ($result as $key => $obj) {

            if (count(explode(" ", $obj['title'])) > 5) {
                $more[$key] = $obj;
            }
            if (count(explode(" ", $obj['title'])) <= 5) {
                $less[$key] = $obj;
            }
        }

        $result = ($case == "more") ? $more : $less;
        $colum = ($case == "more") ? "comments" : "points";

        $result = self::array_sort_by_column($result, $colum);

        return $result;

    }

    public function get_path()
    {

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

        $resultpoints = self::clean_label($result, "points");
        $resultpointscomments = self::clean_label($resultpoints, "comments");

        return $resultpointscomments;

    }

    public function clean_label($array, $colum) {

        foreach($array as $key => $obj){
            if(isset($obj[$colum])){
                $points_format = explode(" ", (string)$obj[$colum]);
                $array[$key][$colum] = $points_format[0];
            }
            else{
                $array[$key][$colum] = 0;
            }
        }
        return $array;
 
    }

    public function array_sort_by_column($myArray, $colum){
        foreach ($myArray as $key => $row) {
            $filter[$key] = $row[$colum];
        }
        array_multisort($filter, SORT_DESC, $myArray);
        return $myArray;

    }
  
}
