<?php

namespace Formation\CinemaCrud\MainBundle\Exceptions;

/**
 * Description of BadUserPassword
 *
 * @author Seme
 */
class BadUserPassword extends \Exception{
    public function __construct(string $message = "", int $code = 0,
            \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
