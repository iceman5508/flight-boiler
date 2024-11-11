<?php
namespace App\Helpers\Classes;

class HttpRequestC
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
     * the custom body of the request
     * @var 
     */
    protected $body = [];


    protected $url = '';

    private $is_json = false;


    private $error; 

    private $httpCode;
    
   
    public function __construct(string $url, array $data = [])
    {
        $this->url = $url;
        $this->body = $data;
    }

    /**
     * get the httpCode
     * @return mixed
     */
    public function getHttpCode(){
        return $this->httpCode;
    }


    public function getError(){
        return $this->error;
    }

    /**
     * json encode the data being sent
     * @return 
     */
    public function jsonRequest(): HttpRequestC {
        $this->body = json_encode($this->body);
        $this->headers[] = "Content-Type: application/json";
        $this->is_json = true;
        return $this;
    }

    /**
     * return json verson of response
     * @return mixed
     */
    public function jsonResponse() {
        return json_decode($this->response);
    }

    /**
     * Add a header
     * @param string $header
     * @return HttpRequestC
     */
    public function addHeader(string $header):HttpRequestC {
        $this->headers[] = $header;
        return $this;
    }

    /**
     * Response: this method returns the http response
     * from the request.
     */
    public function response()
    {
        return $this->response;
    }


    /**
    *send Get request to server
     */
    public function get(): HttpRequestC
    {
        if(!empty($this->body)) {
            $this->url .= '?';
            $i=0;
            foreach($this->body as $key => $value)
            {
                if($i==count($this->body)-1){
                    $this->url .= "{$key}={$value}";
                }else{
                    $this->url .= "{$key}={$value}&";
                }
               $i++;
            }
        }
      
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        if(!empty($this->headers)){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
        }

        $this->response = curl_exec($curl);

        $this->httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        
        if(curl_errno($curl)) {

            $this->error = curl_error($curl);

        }
        
        curl_close($curl);
        return $this;

    }

    /**
     * Send a post request to the server.
     */
    public function post():HttpRequestC
    {
        if(!$this->is_json){
           $this->queryBody();
        }

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $this->body);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        if(!empty($this->headers)){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
        }

        $this->response = curl_exec($curl);

        $this->httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);


        if(curl_errno($curl)) {

            $this->error = curl_error($curl);

        }
        
        curl_close($curl);
        return $this;
    }

    /**
     * Send a patch request to the server.
     */
    public function patch():HttpRequestC
    {
        if(!$this->is_json){
            $this->queryBody();
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $this->body);
        if(!empty($this->headers)){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
        }
        $this->response = curl_exec($curl);

        $this->httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if(curl_errno($curl)) {

            $this->error = curl_error($curl);

        }
        curl_close($curl);
        return $this;
    }

    /**
     * change the body to query type
     * @return void
     */
    private function queryBody(){
       $this->body =  http_build_query($this->body);
    }

    /**
     * Send a put request to the server.
     */
    public function put():HttpRequestC
    {
        if(!$this->is_json){
            $this->queryBody();
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $this->body);
        if(!empty($this->headers)){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
        }
        $this->response = curl_exec($curl);

        $this->httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if(curl_errno($curl)) {

            $this->error = curl_error($curl);

        }
        curl_close($curl);
        return $this;
    }

    /**
     * Send a delete request to the server.
     */
    public function delete($url,array $data = [])
    {
         if(!$this->is_json){
            $this->queryBody();
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $this->body);
        if(!empty($this->headers)){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
        }
        $this->response = curl_exec($curl);

        $this->httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if(curl_errno($curl)) {

            $this->error = curl_error($curl);

        }
        curl_close($curl);
        return $this;
    }


    public function __destruct()
    {
       unset($this->response);

    }


}
