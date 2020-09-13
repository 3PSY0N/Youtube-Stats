<?php

namespace App\Controllers;

use App\Core\Twig;

class Youtube
{
    private $apiKey;
    private $videoId;
    private $videoUrl;
    private $statusMsg;
    private $twig;
    private $jsonData;
    
    /**
     * Youtube constructor.
     */
    public function __construct() {
        $this->setApiKey('youtube API Key');
        $this->twig = new Twig();
        $this->statusMsg = new StatusMsg();
    }
    
    public function hook()
    {
        $msg = "test webhook";
        $curl = curl_init('');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(["content" => $msg, "username" => "webhoook"]));
        return curl_exec($curl);
    }
    
    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        $data = [];
    
        if (isset($_POST) && !empty($_POST)) {
        
            $this->videoUrl = $_POST['statsUrl'];
        
            if (empty($this->getVideoUrl())) {
                $data['statusMsg']['fieldEmpty'] = $this->statusMsg->show('danger', 'Youtube Url must be filled !');
            } else {
                
                if (null !== $this->checkYtUrl($this->videoUrl)) {
                    $this->videoId = $this->parseUrl($this->videoUrl);

                    dump($this->checkYtUrl($this->videoUrl));
                    $this->jsonData = $this->getJsonData($this->videoId);
                
                    if ($this->jsonData !== null) {
                        $data['youtube'] = $this->getJsonData($this->videoId);
                    } else {
                        $data['statusMsg']['fieldEmpty'] = $this->statusMsg->show('danger', 'Youtube Url does not exist !');
                    }
                    
                } else {
                    $data['statusMsg']['fieldEmpty'] = $this->statusMsg->show('danger', 'Wrong Youtube Url !');
                }
            }
        }
    
        echo $this->twig->render('index.html.twig', [
            'data' => $data
        ]);
    }
    
    /**
     * @param $url
     * @return bool
     */
    public function checkYtUrl($url)
    {
        $re = '/^((?:https?:)?\/\/)?((?:www|m)\.)?((?:youtube\.com|youtu.be))(\/(?:[\w\-]+\?v=|embed\/|v\/)?)([\w\-]+)(\S+)?$/m';
        if (preg_match($re, $url)) {
            return true;
        }
    }
    
    /**
     * @param $apiKey
     */
    public function setApiKey($apiKey): void
    {
        $this->apiKey = $apiKey;
    }
    
    /**
     * @param $videoId
     * @return mixed
     */
    public function getJsonData($videoId)
    {
        $apiUrl = 'https://www.googleapis.com/youtube/v3/videos?part=snippet%2CcontentDetails%2Cstatistics&id=' . $videoId . '&key=' . $this->apiKey;
        dump($apiUrl);
        if (!empty($this->videoId)) {
            if (json_decode(file_get_contents($apiUrl))->pageInfo->totalResults > 0) {
                return json_decode(file_get_contents($apiUrl))->items[0];
            }
        }
    }
    
    /**
     * @return mixed
     */
    public function getVideoUrl()
    {
        return $this->videoUrl;
    }
    
    /**
     * @param $url
     * @return mixed|null
     */
    public function parseUrl($url)
    {
        $queryString = parse_url($url, PHP_URL_QUERY);
        parse_str($queryString, $params);
        
        if (isset($params['v']) && strlen($params['v']) > 0) {
            return $this->videoId = $params['v'];
        } else {
            return $this->videoId = null;
        }
    }
    
}


