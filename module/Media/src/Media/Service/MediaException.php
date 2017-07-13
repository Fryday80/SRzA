<?php
namespace Media\Service;

class MediaException {
    public $code;
    public $msg;
    public $path;
    function __construct($code, $path) {
        $this->msg = ERROR_STRINGS[$code];
        $this->code = $code;
        $this->path = $path;
    }
    public function getMsg() {
        return sprintf('%s. In: "%s" ', $this->msg, $this->path);
    }
}
