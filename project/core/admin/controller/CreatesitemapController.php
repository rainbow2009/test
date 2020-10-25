<?php


namespace admin\controller;


use base\controller\traits\BaseTrait;

class CreateSitemapController extends BaseAdmin
{

    use BaseTrait;

    protected $all_links = [];
    protected $temp_links = [];

    protected $maxLinks = 5000;
    protected $parsingLogFile = 'parsing_log.txt';
    protected $filesArr = ['jpg', 'png', 'gif', 'jpeg', 'xls', 'xlsx', 'pdf', 'mpeg', 'mp4', 'mp3'];
    protected $filterArr = [
        'url' => ['facebook', 'instagram'],
        'get' => ['fishop']
    ];

    protected function inputData($links_counter = 1)
    {
        if (!function_exists('curl_init')) {
            $this->cancel(0, 'нет библиотеки curl', '', true);

        }

        if (!$this->userId) $this->execBase();
        if (!$this->checkParsingTable()) {
            $this->cancel(0, 'проблемы с таблицей parsing_table в БД', '', true);

        }

        set_time_limit(0);
        $reserve = $this->model->get('parsing_table')[0];
        foreach ($reserve as $name => $item) {
            if ($item) $this->$name = json_decode($item);
            else $this->$name = [SITE_URL];
        }

        $this->maxLinks = (int)$links_counter > 1 ? ceil($this->maxLinks / $links_counter) : $this->maxLinks;

        while ($this->temp_links) {
            $temp_links_counter = count($this->temp_links);
            $links = $this->temp_links;
            $this->temp_links = [];

            if ($temp_links_counter > $this->maxLinks) {

                $links = array_chunk($links, ceil($temp_links_counter / $this->maxLinks));

                $count_chunks = count($links);

                for ($i = 0; $i < $count_chunks;$i++){
                    $this->parsing($links[$i]);
                    unset($links[$i]);
                    if($links){
                        $this->model->update('parsing_table',[
                            'fields' => [
                                'temp_links'=> json_encode(array_merge(...$links)),
                                'all_links' => json_encode($this->all_links)
                            ]
                        ]);
                    }
                }

            } else {
                $this->parsing($links);
            }

            $this->model->update('parsing_table',[
                'fields' => [
                    'temp_links'=> json_encode($this->temp_links),
                    'all_links' => json_encode($this->all_links)
                ]
            ]);

        }
        $this->model->update('parsing_table',[
            'fields' => [
                'temp_links'=> ' ',
                'all_links' => ' '
            ]
        ]);

        dd(202, $this->all_links);

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
        curl_setopt($curl, CURLOPT_TIMEOUT, 340);
        curl_setopt($curl, CURLOPT_RANGE, 0 - 4194304);

        $out = curl_exec($curl);
        curl_close($curl);

        if (!preg_match("/Content-Type:\s+text\/html/ui", $out)) {
            unset($this->all_links[$index]);
            $this->all_links = array_values($this->all_links);
            return;
        }

        if (!preg_match("/HTTP\/\d\.?\d?\s+20\d/ui", $out)) {

            $this->writeLog('не коректная ссылка при парсинге - ' . $url, $this->parsingLogFile);
            unset($this->all_links[$index]);
            $this->all_links = array_values($this->all_links);
            $_SESSION['res']['answer'][] .= '<div class="vg-element vg-padding-in-px" style="color: red">не коректная ссылка при парсинге - ' . $url . ' </div>';

            return;
        }

        preg_match_all('/<a\s*?[^>]*?href\s*?=\s*?(["\'])(.+?)\1[^>]*?>/ui', $out, $links);

        if ($links[2]) {
            foreach ($links[2] as $link) {

                if ($link === '/' || $link === SITE_URL . '/') {
                    continue;
                }

                foreach ($this->filesArr as $ext) {

                    if ($ext) {

                        $ext = addslashes($ext);
                        $ext = str_replace('.', '/.', $ext);

                        if (preg_match('/' . $ext . '(\s*?$|\?[^\/]*$)/ui', $link)) {
                            continue 2;
                        }
                    }
                }

                if (strpos($link, '/') === 0) {
                    $link = SITE_URL . $link;
                }

                $site_url = mb_str_replace('.','\.'
                    , mb_str_replace('/','\/',SITE_URL));

                if (!in_array($link, $this->all_links) &&  !preg_match('/^('.$site_url.')?\/?#[^\/]*?$/ui',$link)
                    && strpos($link, SITE_URL) === 0) {
                    if ($this->filter($link)) {
                        $this->all_links[] = $link;


                        $this->parsing($link, count($this->all_links) - 1);
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

                            if (preg_match('/^[^\?]*' . $item . '/ui', $link, $q)) {
                                return false;
                            }
                        }
                        if ($type === 'get') {
                            if (preg_match('/(\?|&amp;|=|&)' . $item . '(=|&amp;|&|$)/ui', $link, $q)) {
                                return false;
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

    protected function checkParsingTable()
    {
        $tables = $this->model->showTables();
        if (!in_array('parsing_table', $tables)) {
            $query = 'CREATE TABLE parsing_table (id int NOT NULL AUTO_INCREMENT,all_links text, temp_links text, PRIMARY KEY (id))';
            if (!$this->model->query($query, 'c') ||
                !$this->model->add('parsing_table', ['fields' => ['all_links' => ' ', 'temp_links' => ' ']])
            ) {
                return false;
            }
        }
        return true;
    }

    protected function cancel($success = 0, $message = '', $log_message = '', $exit = false)
    {
        $exitArr = [];
        $exitArr['success'] = $success;
        $exitArr['message'] = $message ? $message : 'ERROR PARSING';
        $log_message = $log_message ? $log_message : $exitArr['message'];
        $exitArr['log_message'] = $log_message;

        $class = 'success';

        if (!$exitArr['success']) {
            $class = 'error';
            $this->writeLog($log_message, 'parsing_log.txt');
        }
        if ($exit) {
            $exitArr['message'] = '<div class="' . $class . ' ">' . $exitArr['message'] . '</div>';
        }

    }


}