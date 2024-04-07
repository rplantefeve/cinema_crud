<?php

/*
 * Change toutes les erreurs en exceptions
 */
function exception_error_handler($severity, $message, $file, $line)
{
    if ((error_reporting() && $severity) === false) {
        // Ce code d'erreur n'est pas inclu dans error_reporting
        return;
    }

    throw new ErrorException(
        $message,
        0,
        $severity,
        $file,
        $line
    );
}
