<?php


namespace admin\controller;


use base\controller\traits\BaseTrait;

class CreateSitemapController extends BaseAdmin
{

    use BaseTrait;

    protected $all_links = [];
    protected $temp_links = [];
    protected $bad_links = [];

    protected $maxLinks = 5000;
    protected $parsingLogFile = 'parsing_log.txt';
    protected $filesArr = ['jpg', 'png', 'gif', 'jpeg', 'xls', 'xlsx', 'pdf', 'mpeg', 'mp4', 'mp3'];
    protected $filterArr = [
        'url' => ['facebook', 'instagram', 'wp-login'],
        'get' => ['fishop', 'add', 'remove_item', 'add-to-cart']
    ];

    public function inputData($links_counter = 1, $redirect = true)
    {

        $links_counter = $this->clearNum($links_counter);

        if (!function_exists('curl_init')) {
            $this->cancel(0, 'нет библиотеки curl', '', true);

        }

        if (!$this->userId) {
            $this->execBase();
        }
        if (!$this->checkParsingTable()) {
            $this->cancel(0, 'проблемы с таблицей parsing_table в БД', '', true);

        }

        set_time_limit(0);

        $reserve = $this->model->get('parsing_table', [
            'fields' => ['all_links', 'temp_links', 'bad_link']
        ])[0];

        $table_rows = [];

        foreach ($reserve as $name => $item) {

            $table_rows[$name] = '';

            if (!empty($item)) {
                $this->$name = json_decode($item);
            } elseif ($name === 'all_links' || $name === 'temp_links') {
                $this->$name = [SITE_URL];
            }
        }

        $this->maxLinks = (int)$links_counter > 1 ? ceil($this->maxLinks / $links_counter) : $this->maxLinks;

        while ($this->temp_links) {

            $temp_links_counter = count($this->temp_links);
            $links = $this->temp_links;
            $this->temp_links = [];

            if ($temp_links_counter > $this->maxLinks) {

                $links = array_chunk($links, ceil($temp_links_counter / $this->maxLinks));

                $count_chunks = count($links);

                for ($i = 0; $i < $count_chunks; $i++) {

                    $this->parsing($links[$i]);

                    unset($links[$i]);

                    if ($links) {

                        foreach ($table_rows as $name => $items) {
                            if ($name === 'temp_links') {
                                $table_rows[$name] = json_encode(array_merge(...$links));
                            } else {
                                $table_rows[$name] = json_encode($this->$name);
                            }
                        }

                        $this->model->update('parsing_table', [
                            'fields' => $table_rows
                        ]);

                    }
                }

            } else {
                $this->parsing($links);
            }

            foreach ($table_rows as $name => $items) {
                $table_rows[$name] = json_encode($this->$name);
            }

            $this->model->update('parsing_table', [
                'fields' => $table_rows
            ]);

        }
        foreach ($table_rows as $name => $items) {
            $table_rows[$name] = null;
        }

        $this->model->update('parsing_table', [
            'fields' => $table_rows
        ]);

//        if ($this->all_links) {
//            foreach ($this->all_links as $key => $val) {
//                if (!$this->filter($val) || in_array($val, $this->bad_links)) {
//                    unset($this->all_links[$key]);
//                }
//            }
//        }


        $this->createSitemap();

        if ($redirect) {
            !$_SESSION['res']['answer'] && $_SESSION['res']['answer'][] .= '<div class="vg-element vg-padding-in-px" style="color: green">Sitemap создан</div>';
            $this->redirect();

        } else {
            $this->cancel(1, 'SiteMap is Created! ' . count($this->all_links) . ' links', '', true);
        }

    }


    protected function parsing($urls)
    {
        if (!$urls) {
            return;
        }
        $curlMulty = curl_multi_init();
        $curl = [];
        foreach ($urls as $i => $url) {
            $curl[$i] = curl_init();
            curl_setopt($curl[$i], CURLOPT_URL, $url);
            curl_setopt($curl[$i], CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl[$i], CURLOPT_HEADER, true);
            curl_setopt($curl[$i], CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl[$i], CURLOPT_TIMEOUT, 340);
            curl_setopt($curl[$i], CURLOPT_ENCODING, 'gzip,deflate');

            curl_multi_add_handle($curlMulty, $curl[$i]);
        }

        do {
            $status = curl_multi_exec($curlMulty, $active);
            $info = curl_multi_info_read($curlMulty);

            if (!false !== $info) {
                if ($info['result'] !== 0) {
                    $i = array_search($info['handle'], $curl);
                    $error = curl_errno($curl[$i]);
                    $message = curl_error($curl[$i]);
                    $header = curl_getinfo($curl[$i]);
                    if ($error != 0) {
                        $this->cancel(0, 'Error loading ' . $header['url'] .
                            ' http code: ' . $header['http_code'] .
                            'error: ' . $error . 'message: ' . $message
                        );
                    }
                }
            }
            if ($status > 0) {
                $this->cancel(0, curl_multi_strerror($status));
            }
        } while ($status === CURLM_CALL_MULTI_PERFORM || $active);


        foreach ($urls as $i => $url) {

            $result[$i] = curl_multi_getcontent($curl[$i]);
            curl_multi_remove_handle($curlMulty, $curl[$i]);
            curl_close($curl[$i]);

            if (!preg_match("/Content-Type:\s+text\/html/ui", $result[$i])) {

                $this->bad_links[] = $url;
                $this->cancel(0, 'Incorrect content type ' . $url);

                continue;
            }
            if (!preg_match("/HTTP\/\d\.?\d?\s+20\d/ui", $result[$i])) {
                $this->bad_links[] = $url;

                $this->cancel(0, 'Incorrect server code ' . $url);


                continue;
            }

            $this->createLinks($result[$i]);


        }


        curl_multi_close($curlMulty);


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
        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;

        $root = $dom->createElement('urlset');
        $root->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $root->setAttribute('xmlns:xls', 'https://www.w3.org/2001/XMLSchema-instance');
        $root->setAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xds');
        $dom->appendChild($root);

        $sxe = simplexml_import_dom($dom);

        if ($this->all_links) {

            $date = (new \DateTime());
            $lastMod = $date->format('Y-m-d') . 'T' . $date->format('H:i:s+01:00');

            foreach ($this->all_links as $item) {

                $elem = trim(mb_substr($item, mb_strlen(SITE_URL)), '/');
                $elem = explode('/', $elem);

                if (empty($elem[0])) {
                    $priority = '1.0';
                } else {
                    $count = '0.' . (count($elem));
                    $priority = 1 - (float)$count;
                }

                $urlMain = $sxe->addChild('url');
                $urlMain->addChild('loc', htmlspecialchars($item));
                $urlMain->addChild('lastmod', $lastMod);
                $urlMain->addChild('changefreq', 'weekly');
                $urlMain->addChild('priority', $priority);
            }
        }
        $dom->save($_SERVER['DOCUMENT_ROOT'] . PATH . 'sitemap.xml');
    }

    protected function checkParsingTable()
    {
        $tables = $this->model->showTables();
        if (!in_array('parsing_table', $tables)) {
            $query = 'CREATE TABLE parsing_table (id int NOT NULL AUTO_INCREMENT,all_links longtext, temp_links longtext,bad_link longtext, PRIMARY KEY (id))';
            if (!$this->model->query($query, 'c') ||
                !$this->model->add('parsing_table', ['fields' => ['all_links' => null, 'temp_links' => null, 'bad_link' => null]])
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
            return exit($exitArr['message']);
        }
    }

    private function createLinks($content)
    {

        if ($content) {
            preg_match_all('/<a\s*?[^>]*?href\s*?=\s*?(["\'])(.+?)\1[^>]*?>/ui', $content, $links);
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

                    $site_url = mb_str_replace('.', '\.'
                        , mb_str_replace('/', '\/', SITE_URL));

                    if (!in_array($link, $this->bad_links)
                        && !preg_match('/^(' . $site_url . ')?\/?#[^\/]*?$/ui', $link)
                        && strpos($link, SITE_URL) === 0
                        && !in_array($link, $this->all_links)) {


                        if ($link) {
                            if (!$this->filter($link) || in_array($links, $this->bad_links)) {
                                unset($link);
                                return;
                            }
                        }

                        $this->all_links[] = $link;
                        $this->temp_links[] = $link;

                    }

                }

            }
        }
    }


}