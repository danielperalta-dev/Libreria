<?php
function paypal_token() {
    static $t = null;
    if ($t) return $t;
    $ch = curl_init(PAYPAL_API.'/v1/oauth2/token');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true,
        CURLOPT_USERPWD => PAYPAL_CLIENT_ID.':'.PAYPAL_SECRET,
        CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
        CURLOPT_SSL_VERIFYPEER => VERIFICAR_SSL,
    ]);
    $r = json_decode(curl_exec($ch), true);
    curl_close($ch);
    return $t = ($r['access_token'] ?? null);
}
function paypal_request($metodo, $ruta, $cuerpo = null) {
    $tk = paypal_token();
    if (!$tk) return ['error' => 'No se pudo autenticar con PayPal (revisa Client ID y Secret)'];
    $ch = curl_init(PAYPAL_API.$ruta);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true, CURLOPT_CUSTOMREQUEST => $metodo,
        CURLOPT_POSTFIELDS => $cuerpo === null ? '{}' : json_encode($cuerpo),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer '.$tk, 'PayPal-Request-Id: '.uniqid('', true)],
        CURLOPT_SSL_VERIFYPEER => VERIFICAR_SSL,
    ]);
    $r = json_decode(curl_exec($ch), true);
    curl_close($ch);
    return $r ?: ['error' => 'Sin respuesta de PayPal'];
}
