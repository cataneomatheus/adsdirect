<?php
declare(strict_types=1);

/*
 * Redirecionador reservado para a futura oferta do Silvets.
 * A URL de afiliado e o repasse seguro de parâmetros serão configurados
 * quando os dados definitivos forem fornecidos.
 */

header('Cache-Control: no-store');
header('X-Robots-Tag: noindex, nofollow');
http_response_code(503);
header('Content-Type: text/plain; charset=UTF-8');
echo 'Redirecionamento Silvets ainda não configurado.';
