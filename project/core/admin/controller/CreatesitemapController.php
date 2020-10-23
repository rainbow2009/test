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
        'url' => ['facebook', 'instagram'],
        'get' => ['fishop']
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
        dd(202,$this->linkArr);

        $this->createSitemap();
        !$_SESSION['res']['answer'] && $_SESSION['res']['answer'][] .= '<div class="vg-element vg-padding-in-px" style="color: green">Sitemap создан</div>';
        $this->redirect();

    }


    protected function parsing($url, $index = 0)
    {


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

        preg_match_all('/<a\s*?[^>]*?href\s*?=\s*?(["\'])(.+?)\1[^>]*?>/ui', $out, $links);

        if ($links[2]) {
            foreach ($links[2] as $link) {

                if ($link === '/' || $link === SITE_URL . '/') {
                    dd(1,$link);

                    continue;
                }
                foreach ($this->filesArr as $ext) {

                    if ($ext) {

                        $ext = addslashes($ext);
                        $ext = str_replace('.', '/.', $ext);

                        if (preg_match('/' . $ext . '\s*?$/ui', $link)) {
                            dd(2,$link);

                            continue 2;
                        }
                    }
                }

                if (strpos($link, '/') === 0) {
                    $link = SITE_URL . $link;
                }
                if (!in_array($link, $this->linkArr) && $link !== '#' && strpos($link, SITE_URL) === 0) {
                    if ($this->filter($link)) {
                        $this->linkArr[] = $link;


                        $this->parsing($link, count($this->linkArr) - 1);
                    }


                }

            }

        }


    }

    protected function filter($link)
    {

        if ($this->filesArr) {
            foreach ($this->filterArr as $type => $values) {
                if ($type) {
                    foreach ($values as $item) {
                        $item = str_replace('/', '\/', addslashes($item));

                        if ($type === 'url') {

                            if (preg_match(  '/^[^\?]*' . $item . '.*(\?|$)/ui', $link,$q)) {
                              return  false;
                            }
                        }
                        if ($type === 'get') {
                            if (preg_match('/(\?|&amp;|=|&)' . $item . '(=|&amp;|&|$)/ui', $link,$q)) {
                                return  false;
                            }

                        }

                    }
                }
            }

        }

        return true;

    }

    protected function createSitemap()
    {
        return true;
    }

}