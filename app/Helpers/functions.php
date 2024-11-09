<?php
/**
 * env call
 * @param string $key
 * @param string $default
 * @return mixed
 */
function env(string $key,string $default = null)
{
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../..');
    $dotenv->load();
    return $_ENV[$key] ?? $default;
}


/**
 * Create csrf Token
 * @param string $token_name the form field name
 * @return string
 */
function csrf(string $token_name): string {
    $token  = new \App\Helpers\Classes\Token();
    $token = $token->makeHash($token->generate(rand(4,16)));
    \App\Helpers\Classes\Session::set($token_name,  $token[0], true);
    return \App\Helpers\Classes\Session::get($token_name);
}

/**
 * Check the csrf token
 * @param string $token_name
 * @return bool
 */
function check_csrf_token(string $token_name): bool {
    if(\App\Helpers\Classes\Session::exists($token_name) &&   $_REQUEST[$token_name] === \App\Helpers\Classes\Session::get($token_name)){
        \App\Helpers\Classes\Session::delete($token_name);
        return true;
    }
    return false;

}