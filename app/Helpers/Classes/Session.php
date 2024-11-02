<?php


namespace App\Helpers\Classes;



/**
 * Class Session - This class handle session related functions.
 */
class Session
{

    /**
     * Check if a specific session name exists
     */
    public static function exists(string $name){

        return (isset($_SESSION[$name])? true: false);

    }

    /**
     * set a session
     * @param $name - the name of the session
     * @param $value - the value of the session
     */
    public static function set(string $name, $value){
        $_SESSION[$name] = $value;
    }

    /**
     *Get a session by name
     * @param $name - the name of the session
     * @return the value of the session
     */
    public static function get(string $name){
        return $_SESSION[$name];

    }

    /**
    *delete a session
     * @param $name - The name of the session to remove
     */
    public static function delete(string $name){
        if(self::exists($name)) {
            unset($_SESSION[$name]);
        }
    }

    /**
     * Flash a session the delete it
     * @param $name - the name of the session
     * @param string $string - the value to flash
     * @return the session to flash
     */
    public static function flash(string $name, $string = ' '){
        if(self::exists($name)) {
            $session = self::get($name);
            self::delete($name);
            return $session;
        } else {
            self::set($name , $string);
        }
    }

}