<?php


namespace admin\controller;


use base\controller\traits\BaseTrait;

class CreateSitemapController extends BaseAdmin
{

    use BaseTrait;

    protected $linkArr = [];
    protected $parsingLogFile = 'parsing_log.txt';
    protected $filesArr = ['jpg', 'png', 'gif', 'jpeg', 'xls', 'xlsx', 'pdf', 'mpeg', 'mp4', 'mp3'];
    protected $filterArr = [
        'url' => [],
        'get' => []
    ];

    protected function inputData()
    {
        if (!function_exists('curl_init')) {
            $this->writeLog('нет библиотеки curl');
            $_SESSION['res']['answer'][] .= '<div class="vg-element vg-padding-in-px" style="color: red">нет библиотеки curl</div>';
            $this->redirect();
        }

        set_time_limit(0);

        if (file_exists($_SERVER['DOCUMENT_ROOT'] . PATH . 'log/' . $this->parsingLogFile)) {
            @unlink($_SERVER['DOCUMENT_ROOT'] . PATH . 'log/' . $this->parsingLogFile);
        }

        $this->parsing(SITE_URL);
        $this->createSitemap();

        !$_SESSION['res']['answer'] && $_SESSION['res']['answer'][] .= '<div class="vg-element vg-padding-in-px" style="color: green">Sitemap создан</div>';
        $this->redirect();

    }


    protected function parsing($url, $index = 0)
    {

        if ($url === '/' || $url === SITE_URL . '/') {
            return;
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 120);
        curl_setopt($curl, CURLOPT_RANGE, 0 - 4194304);

        $out = curl_exec($curl);
        curl_close($curl);

        if (!preg_match("/Content-Type:\s+text\/html/ui", $out)) {
            unset($this->linkArr[$index]);
            $this->linkArr = array_values($this->linkArr);
            return;
        }

        if (!preg_match("/HTTP\/\d\.?\d?\s+20\d/ui", $out)) {

            $this->writeLog('не коректная ссылка при парсинге - ' . $url, $this->parsingLogFile);
            unset($this->linkArr[$index]);
            $this->linkArr = array_values($this->linkArr);
            $_SESSION['res']['answer'][] .= '<div class="vg-element vg-padding-in-px" style="color: red">не коректная ссылка при парсинге - ' . $url . ' </div>';

            return;
        }

        preg_match_all('/<a\s*?[^>]*?href\s*?=(["\'])(.+?)\1[^>]*?>/ui', $out, $links);

        if ($links[2]) {
            foreach ($links[2] as $link) {

                foreach ($this->filesArr as $ext){
                    if($ext){

                    }
                }

            }
        }


    }

    protected function filter($link)
    {

    }

    protected function createSitemap()
    {

    }

}