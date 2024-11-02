<?php


namespace App\Helpers\Classes;



/**
 * Class Session - This class handle session related functions.
 */
class Session
{

    /**
     * Start the session and check for timmed functions
     * @return void
     */
    public static function start(){
        session_start();
        //session_destroy();
        //var_dump($_SESSION);
        if(self::exists('timed_sessions')){
             self::checkTimedSessions();
        }

    }

    /**
     * Check the timer for timer sessions
     * @return void
     */
    public static function checkTimedSessions(){
        foreach (self::get('timed_sessions') as $session => $value){
           
            if(self::exists($session) ){
                $lastActivity = $value['last_active'];
                $currentTime = time();
                $timeSinceLastActivity = $currentTime - $lastActivity;

                if ($timeSinceLastActivity > $value['timer']) {
                    // Session expired, destroy the session
                    self::delete($session);

                    $timmed_sessions =  self::get('timed_sessions');
                    unset($timmed_sessions[$session]);

                    self::set('timed_sessions', $timmed_sessions);


                } else {
                    //var_dump(self::get('timed_sessions')[$session]);
                    // Update the last activity time
                    $value['last_active'] = $currentTime;
                    $timmed_sessions =  self::get('timed_sessions');
                    $timmed_sessions[$session] = $value;

                    self::set('timed_sessions', $timmed_sessions);
                    //var_dump($timmed_sessions);
                   // self::get('timed_sessions')[$session] = $value;
                }
               
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
                'last_active' => time()
            ];

            $timer = intval($expire_after) ?? 1800; //30mins by default

            $timmed['timer'] = $timer;

            if(self::exists('timed_sessions')){
                $_SESSION['timed_sessions'][$name] = $timmed;
            }else{

                $_SESSION['timed_sessions'] = [
                    $name => $timmed
                ];

            }
           
        }
    }

    /**
     *Get a session by name
     * @param $name - the name of the session
     * @return - value of the session
     */
    public static function get(string $name){
        return $_SESSION[$name] ?? null;

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