<?php
namespace Media\Service;

use Exception;
use Throwable;

class MediaException extends Exception {
    public $associatedPath;

    /**
     * Construct the exception. Note: The message is NOT binary safe.
     * @link http://php.net/manual/en/exception.construct.php
     * @param int $code [optional] The Exception code.
     * @param string $associatedPath [optional]
     * @param Throwable $previous [optional] The previous throwable used for the exception chaining.
     * @since 5.1.0
     */
    public function __construct($code = 0, $associatedPath = '', Throwable $previous = null) {
        parent::__construct(ERROR_STRINGS[$code], $code, $previous);
        $this->associatedPath = $associatedPath;
    }

}
