<?php

namespace Sabre\DAV\XSendFile;

use Sabre\DAV\Server;
use Sabre\DAV\ServerPlugin;
use Sabre\HTTP\RequestInterface;
use Sabre\HTTP\ResponseInterface;

class Apache extends ServerPlugin {

    /**
     * Sets up the plugin and registers events. 
     * 
     * @param Server $server 
     * @return void
     */
    public function initialize(Server $server) {

        $server->on('XSendFileAutodetect', [$this, 'autodetect']);
        $server->on('XSendFile:Apache', [$this, 'helper']);

    }

    public function autodetect($physicalPath, RequestInterface $request, ResponseInterface $response, $httpHeaders) {

        if(!is_null($request->getRawServerValue("MOD_X_SENDFILE_ENABLED"))) {
            $this->helper($physicalPath, $request, $response, $httpHeaders, []);
            return false;
        }

    }

    public function helper($physicalPath, RequestInterface $request, ResponseInterface $response, $httpHeaders, $customData) {

        $httpHeaders["X-Sendfile"] = $physicalPath;
        $response->addHeaders($httpHeaders);
        $response->setStatus(200);
        return false;

    }

}
