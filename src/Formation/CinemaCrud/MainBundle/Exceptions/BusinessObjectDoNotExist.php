<?php

namespace Formation\CinemaCrud\MainBundle\Exceptions;

/**
 * Description of BusinessObjectDoNotExist
 *
 * @author Seme
 */
class BusinessObjectDoNotExist extends \Exception {

    public function __construct(string $message = "", int $code = 0,
            \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
