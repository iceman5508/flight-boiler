<?php


namespace App\Helpers\Classes;



/**
 * Class Session - This class handle session related functions.
 */
class Session
{
     public static $timed_sessions = [];


    /**
     * Start the session and check for timmed functions
     * @return void
     */
    public static function start(){
        session_start();
        self::checkTimmedSessions();

    }

    /**
     * Check the timer for timer sessions
     * @return void
     */
    public static function checkTimmedSessions(){
        $index = 0;
        foreach (self::$timed_sessions as $session){
            if(self::exists($session['name']) && self::exists($session['timer_session'])){
                $lastActivity = self::get($session['timer_session']);
                $currentTime = time();
                $timeSinceLastActivity = $currentTime - $lastActivity;

                if ($timeSinceLastActivity > $session['timer']) {
                    // Session expired, destroy the session
                    self::delete($session['name']);
                    self::delete($session['timer_session']);
                    unset(self::$timed_sessions[$index]);
                } else {
                    // Update the last activity time
                    self::set($session['timer_sesssion'], $currentTime);
                }
                $index+=1;
            }
        }
        
    }


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
    public static function set(string $name, $value, bool $timed = false, $expire_after = 1800){
        $_SESSION[$name] = $value;
        if($timed){
            $timmed = [
                'name' => $name,
                'timer_session' => $name.'_timer_session'
            ];

            $timer = intval($expire_after) ?? 1800; //30mins by default

            $timmed['timer'] = $timer;

            self::$timed_sessions[] = $timmed;
        }
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