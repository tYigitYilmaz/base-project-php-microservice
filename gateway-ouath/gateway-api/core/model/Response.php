<?php
namespace Core\Model;


class Response {
	private $_success;
	private $_messages = array();
	private $_data;
	private $_httpStatusCode;
	private $_toCache = false;
	private $_responseData = array();

    /**
     * Response constructor.
     * @param $_success
     * @param array $_messages
     * @param $_data
     * @param $_httpStatusCode
     */
    public function __construct($_success, $_messages, $_data, $_httpStatusCode)
    {
        $this->_success = $_success;
        $this->_messages = $_messages;
        $this->_data = $_data;
        $this->_httpStatusCode = $_httpStatusCode;
    }


    public function setSuccess($success) {
		$this->_success = $success;
	}

	public function addMessage($message) {
		$this->_messages[] = $message;
	}

	public function setData($data) {
		$this->_data = $data;
	}

	public function setHttpStatusCode($httpStatusCode) {
		$this->_httpStatusCode = $httpStatusCode;
	}
	
	public function setCache($toCache) {
		$this->_toCache = $toCache;
	}

	public function send() {
		header('Content-type:application/json;charset=utf-8');
		
		if($this->_toCache == true) {
			header('Cache-Control: max-age=60');
		}
		else {
			header('Cache-Control: no-cache, no-store');
		}

		if(!is_numeric($this->_httpStatusCode) || ($this->_success !== false && $this->_success !== true )) {
			http_response_code(500);
			$this->_responseData['statusCode'] = 500;
			$this->_responseData['success'] = false;
			$this->addMessage("Response creation error");
			$this->_responseData['messages'] = $this->_messages;
		}
		else {
			http_response_code($this->_httpStatusCode);
			$this->_responseData['statusCode'] = $this->_httpStatusCode;
			$this->_responseData['success'] = $this->_success;
			$this->_responseData['messages'] = $this->_messages;
			if (is_string($this->_data)){
                $this->_responseData['data'] = json_decode($this->_data);//Response data gonderiminin string olarak algilanip json_encode'un duzgun calismasi icin decode edip gonderildi.
            } else {
                $this->_responseData['data'] = $this->_data;

            }
		}
		echo json_encode($this->_responseData);
	}

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->_data;
    }



}