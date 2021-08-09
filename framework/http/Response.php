<?php
namespace Frame\http;


class Response
{
    /**
     * @var \Swoole\Http\Response
     */
    protected $swooleResponse;
    protected $body;

    /**
     * Response constructor.
     * @param $swooleResponse
     * @param $body
     */
    public function __construct($swooleResponse)
    {
        $this->swooleResponse = $swooleResponse;
        $this->setHeader("Content-Type", "text/plain;charset=utf-8");

    }

    public static function init(\Swoole\Http\Response $response){
        return new self($response);
    }

    /**
     * @return mixed
     */
    public function getSwooleResponse()
    {
        return $this->swooleResponse;
    }

    /**
     * @param mixed $swooleResponse
     */
    public function setSwooleResponse($swooleResponse): void
    {
        $this->swooleResponse = $swooleResponse;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body): void
    {
        $this->body = $body;
    }

    public function testWrite($data){
        $this->swooleResponse->write($data);

    }

    public function setHeader($key, $value){
        $this->swooleResponse->header($key, $value);
    }

    public function writeHttpStatus(int $code){
        $this->swooleResponse->status($code);
    }

    public function redirect($url, int $code=301){
//        $this->swooleResponse->redirect($url);
        $this->writeHttpStatus($code);
        $this->setHeader("Location",$url);
    }

    //输出
    public function end(){
        $json_convert = ['array','object'];
        $body = $this->getBody();
        $type = gettype($body);
        if(in_array($type, $json_convert)) {
            $this->swooleResponse->header('Content-type', "application/json;charset=utf-8");
            $this->swooleResponse->write(json_encode($this->getBody()));
        } else {
            if($this->getBody())
            $this->swooleResponse->write($this->getBody());
        }

        $this->swooleResponse->end();
    }
}