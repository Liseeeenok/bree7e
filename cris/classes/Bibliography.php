<?php namespace Bree7e\Cris\Classes;

use Seboettg\CiteProc\StyleSheet;
use Seboettg\CiteProc\CiteProc;

class Bibliography
{
    public static function main($data = null, $style = null, $lang = "ru-RU")
    {
        $data = file_get_contents(__DIR__ . "/data.json");
        $style = StyleSheet::loadStyleSheet("gost-r-7-0-5-2008");
        // $style = StyleSheet::loadStyleSheet("bibtex");
        $citeProc = new CiteProc($style, $lang);
        $publications = json_decode($data);
        echo $citeProc->render($publications, "bibliography");
    }
}