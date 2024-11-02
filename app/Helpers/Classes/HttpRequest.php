<?php
namespace App\Helpers\Classes;

class HttpRequest
{
    /**
     * @var response:
     * Stores the response from the http request
     */
    protected $response;

    /**
     * holds the headers
     * @var array
     * 
     * ex Content-Type: application/x-www-form-urlencoded
     */
    protected array $headers; 

    /**
     * Add a header
     * @param string $header
     * @return void
     */
    public function addHeader(string $header){
        $this->headers[] = $header;
    }

    /**
     * Response: this method returns the http response
     * from the request.
     * @return response
     */
    public function response()
    {
        return $this->response;
    }

    /**
     * @param $url: The HTTP url to check
     * @return string The data that will be returned.
     */
    public function get($url, array $data=[])
    {
        $url=$url.'?';
        $i=0;
        foreach($data as $key => $value)
        {
            if($i==count($data)-1){
                $url .= "{$key}={$value}";
            }else{
                $url .= "{$key}={$value}&";
            }
           $i++;
        }

        $handle = fopen($url, "rb");
        $this->response = '';
        while (!feof($handle)) {
            $this->response .= fread($handle, 8192);
        }
        fclose($handle);

    }

    /**
     * Send a post request to the server.
     * @param $url: the url the resource is at.
     * @param $data: the data that will be sent
     * @throws Exception: sends a error if things go wrong.
     */
    public function post($url, array $data = [])
    {
        $content = "";

        // Add post data to request.
        foreach($data as $key => $value)
        {
            $content .= "{$key}={$value}&";
        }

        $request_settings = [
            'method' => 'POST',
            'content' => $content
        ];

        if(!empty($this->headers)){
            $request_settings['header'] = $this->headers;
        }


        $params = array('http' => $request_settings);

        $ctx = stream_context_create($params);
        $fp = fopen($url, 'rb', false, $ctx);

        if (!$fp) {
            throw new Exception("Connection problem, {$php_errormsg}");
        }

        $this->response = @stream_get_contents($fp);
        if ($this->response === false) {
            throw new Exception("Response error, {$php_errormsg}");
        }
    }

    /**
     * Send a patch request to the server.
     * @param $url: the url the resource is at.
     * @param $data: the data that will be sent
     * @throws Exception: sends a error if things go wrong.
     */
    public function patch($url, array $data = [])
    {
        $content = "";

        // Add post data to request.
        foreach($data as $key => $value)
        {
            $content .= "{$key}={$value}&";
        }

        $request_settings = [
            'method' => 'PATCH',
            'content' => $content
        ];

        if(!empty($this->headers)){
            $request_settings['header'] = $this->headers;
        }

        
        $params = array('http' => $request_settings);

        $ctx = stream_context_create($params);
        $fp = fopen($url, 'rb', false, $ctx);

        if (!$fp) {
            throw new Exception("Connection problem, {$php_errormsg}");
        }

        $this->response = @stream_get_contents($fp);
        if ($this->response === false) {
            throw new Exception("Response error, {$php_errormsg}");
        }
    }

    /**
     * Send a put request to the server.
     * @param $url: the url the resource is at.
     * @param $data: the data that will be sent
     * @throws Exception: sends a error if things go wrong.
     */
    public function put($url,array $data = [])
    {
        $content = "";

        // Add post data to request.
        foreach($data as $key => $value)
        {
            $content .= "{$key}={$value}&";
        }

        $request_settings = [
            'method' => 'PUT',
            'content' => $content
        ];

        if(!empty($this->headers)){
            $request_settings['header'] = $this->headers;
        }


        $params = array('http' => $request_settings);

        $ctx = stream_context_create($params);
        $fp = fopen($url, 'rb', false, $ctx);

        if (!$fp) {
            throw new Exception("Connection problem, {$php_errormsg}");
        }

        $this->response = @stream_get_contents($fp);
        if ($this->response === false) {
            throw new Exception("Response error, {$php_errormsg}");
        }
    }

    /**
     * Send a delete request to the server.
     * @param $url: the url the resource is at.
     * @param $data: the data that will be sent
     * @throws Exception: sends a error if things go wrong.
     */
    public function delete($url,array $data = [])
    {
        $content = "";

        // Add post data to request.
        foreach($data as $key => $value)
        {
            $content .= "{$key}={$value}&";
        }

        $request_settings = [
            'method' => 'DELETE',
            'content' => $content
        ];

        if(!empty($this->headers)){
            $request_settings['header'] = $this->headers;
        }
        
        $params = array('http' => $request_settings);

        $ctx = stream_context_create($params);
        $fp = fopen($url, 'rb', false, $ctx);

        if (!$fp) {
            throw new Exception("Connection problem, {$php_errormsg}");
        }

        $this->response = @stream_get_contents($fp);
        if ($this->response === false) {
            throw new Exception("Response error, {$php_errormsg}");
        }
    }


    public function __destruct()
    {
       unset($this->response);

    }


    /**
     * Check if a request or specific request type was made.
     * By default this is set to post
     * @param string $type - the request type being made
     * @return bool
     */
    public static function isRequest($type = 'post'){
        switch($type)
        {
            case 'post':
                return (!empty($_POST)? true: false);
                break;

            case 'get':
                return (!empty($_GET)? true: false);
                break;

            default:
                return false;
                break;
        }
    }

    /**
     * Return data from request if it exists
     * @param $item - the param name of the data
     * @return string
     */
    public static function getRequestData($item){
        if(isset($_POST[$item]))
        {

            return $_POST[$item];
        }
        else
            if(isset($_GET[$item]))
            {

                return $_GET[$item];
            }
        return NULL;
    }

}
