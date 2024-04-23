<?php

/*
 * Change toutes les erreurs en exceptions
 */

function exception_error_handler($severity, $message, $file, $line): void
{
    if (!(error_reporting() & $severity)) {
        // Ce code d'erreur n'est pas inclus dans error_reporting
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
