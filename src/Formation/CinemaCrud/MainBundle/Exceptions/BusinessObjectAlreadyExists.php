<?php

namespace Formation\CinemaCrud\MainBundle\Exceptions;

/**
 * Description of BusinessObjectAlreadyExists
 *
 * @author Seme
 */
class BusinessObjectAlreadyExists extends \Exception {

    public function __construct(string $message = "", int $code = 0,
            \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
