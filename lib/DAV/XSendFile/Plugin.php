<?php

namespace Sabre\DAV\XSendFile;

use Sabre\DAV\Server;
use Sabre\DAV\ServerPlugin;
use Sabre\DAV\IPhysicalFile;
use Sabre\HTTP\RequestInterface;
use Sabre\HTTP\ResponseInterface;

class Plugin extends ServerPlugin {

    /**
     *@var string
     */
    private $manualMethod;

    /**
     *@var array
     */
    private $manualHelperParameters;
    
    /**
     * Reference to server object.
     *
     * @var Server
     */
    protected $server;

    function __construct($manualMethod = null, $manualHelperParameters = []) {
    
        if($manualMethod !== null) {
            $this->manualMethod = $manualMethod;
            $this->manualHelperParameters = $manualHelperParameters;
        }

    }

    /**
     * Sets up the plugin and registers events. 
     * 
     * @param Server $server 
     * @return void
     */
    public function initialize(Server $server) {

        $server->on('method:GET', [$this,'httpGet'], 90);
        $this->server = $server;

    }

    /**
     * Handles GET requests 
     * 
     * @return void
     */
    public function httpGet(RequestInterface $request, ResponseInterface $response) {

        $path = $request->getPath();
        $node = $this->server->tree->getNodeForPath($path,0);

        if (!$node instanceof IPhysicalFile) return;

        $physicalPath = $node->getPhysicalPath();

        $httpHeaders = $this->server->getHTTPHeaders($path);

        /* ContentType needs to get a default, because many webservers will otherwise
         * default to text/html, and we don't want this for security reasons.
         */
        if (!isset($httpHeaders['Content-Type'])) {
            $httpHeaders['Content-Type'] = 'application/octet-stream';
        }

        if($this->manualMethod === null) {
            if($this->server->emit('XSendFileAutodetect', [$physicalPath, $request, $response, $httpHeaders])) return;
        } else {
            if($this->server->emit('XSendFile:' . $this->manualMethod, [$physicalPath, $request, $response, $httpHeaders, $this->manualHelperParameters])) return; //throw something?
        }

        // Sending back false will interupt the event chain and tell the server
        // we've handled this method.
        return false;

    }

}
