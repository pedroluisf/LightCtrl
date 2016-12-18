<?php

abstract class Command {

    protected $connection;
    protected $host;

    public function __construct() {
        $this->connection = Factory::getDefaultConnectionModel();
    }

    /**
     * Gives a readable description of what this command is
     * @return string
     */
    abstract public function commandDescription();

    /**
     * Adds a command to the queue. Some commands may try to execute thenselfs acording to it's importance
     * @param array $parameters
     * @return mixed
     */
    abstract public function addCommandToQueue(array $parameters);

    /**
     * This method avoids for creation of redundant requests on the queue.
     * Hash must be unique, and every command knows it's redundancy rules
     * @param array $parameters
     * @return mixed
     */
    abstract protected function createQueueHash(array $parameters);

    /**
     * Requested by the queue, this will trigger the actual command, using the parents function
     * @param $user_id
     * @param $ethernet_id
     * @param $cmd
     * @return string
     */
    abstract public function performCommand($user_id, $ethernet_id, $cmd);

    /**
     * This method common to all, send the command via the selected connection
     * @param $command
     * @return mixed
     */
    protected function sendCommand($command) {
        $this->connection->startConnection($this->host);

        $result = $this->connection->sendAndReceive($command);

        $this->connection->closeConnection();

        return $result;
    }

}