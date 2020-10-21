<?php


namespace admin\controller;


use base\controller\traits\BaseTrait;

class CreateSitemapController
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
            $_SERVER['res']['answer'] = '<div class="vg-element vg-padding-in-px" style="color: green">нет библиотеки curl</div>';
            $this->redirect();
        }

        set_time_limit(0);

        if (file_exists($_SERVER['DOCUMENT_ROOT'] . PATH . 'log/' . $this->parsingLogFile)) {
            @unlink($_SERVER['DOCUMENT_ROOT'] . PATH . 'log/' . $this->parsingLogFile);
        }

        $this->parsing(SITE_URL);
        $this->createSitemap();

        !$_SERVER['res']['answer'] && $_SERVER['res']['answer'] = '<div class="vg-element vg-padding-in-px" style="color: green">Sitemap создан</div>';
        $this->redirect();

    }


    protected function parsing($url, $index = 0)
    {

    }

    protected function filter($link)
    {

    }

    protected function createSitemap()
    {

    }

}