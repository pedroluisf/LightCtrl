<?php

class TcpConnection implements IConnection {

    const CONNECTION_TIMEOUT_IN_SECONDS = 30;
    const COMMUNICATION_TIMEOUT_IN_SECONDS = 20;
    const MAX_SIZE_IN_BYTES = 4096;

    /*
    * @var resource $_connection
    */
    protected $_connection;
    protected $_host;
    protected $_port;

    public function __construct(){
        $this->_port = Yii::app()->params['communications_port'];
    }

    public function __destruct(){
        $this->closeConnection();
    }

    /**
     * @param String $host
     * @return void
     * @throws Exception
     */
    public function startConnection($host) {
        if (is_resource($this->_connection)) {
            return; // Could be already open due to this being a follow-up message
        }

        if (empty($host)) {
            throw new Exception('Missing mandatory parameter «Host»');
        }

        $this->_host = $host;
        $this->_connection = @fsockopen($this->_host, $this->_port, $errorNumber, $errorString, self::CONNECTION_TIMEOUT_IN_SECONDS);

        if (!is_resource($this->_connection)) {
            throw new Exception ("Error connecting to $this->_host:$this->_port ($errorNumber) $errorString");
        }
    }

    public function closeConnection() {
        if (is_resource($this->_connection)) {
            fclose($this->_connection);
        }
    }

    public function sendAndReceive($command) {
        $logRequest = !preg_match('/^\{"'.StatusRequestCommand::CMD.'"/', $command);

        if (is_resource($this->_connection)) {
            if ($logRequest){
                Yii::log('Request sent: '.$command, 'info', 'request');
            }

            fwrite($this->_connection, $command);

            stream_set_blocking($this->_connection, true);
            stream_set_timeout($this->_connection,self::COMMUNICATION_TIMEOUT_IN_SECONDS);
            $info = stream_get_meta_data($this->_connection);

            $response = '';
            while (!feof($this->_connection) && !$info['timed_out']) {
                $response .= fgets($this->_connection, self::MAX_SIZE_IN_BYTES);
                $info = stream_get_meta_data($this->_connection);
            }

            if ($info['timed_out']) {
                throw new Exception ("Connection to host $this->_host:$this->_port timed out");
            }

            if ($logRequest) {
                Yii::log('Response received: ' . $response, 'info', 'request');
            }

            return $response;
        }
    }

}