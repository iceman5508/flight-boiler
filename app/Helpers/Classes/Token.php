<?php
namespace App\Helpers\Classes;

/**
 * 
 * Create a random token that is secure to be used.
 * This token can be limited in length as well.
 *
 *
 * @author Isaac Parker
 */
class Token
{

    /** @var string the alphabet to work with*/
    protected $alphabet;

    /** @var int the alphabet length*/
    protected $alphabetLength;


    /**
     * Token constructor - The entry point into the class.
     * @param string $alphabet - The string to create token around
     */
    public function __construct(string $alphabet = ''){
        if ('' !== $alphabet) {
            $this->setAlphabet($alphabet);
        } else {
            $this->setAlphabet(
                implode(range('a', 'z'))
                . implode(range('A', 'Z'))
                . implode(range(0, 9))
            );
        }
    }

    public function __destruct(){
        unset($this->alphabet);
        unset($this->alphabetLength);
    }

    /**
     * Create hash
     * @param $string - The string to be hashed
     * @param string $algo - The hashing algorithm
     * @param string $salt - The salt value to use.
     * @return array|string
     */
    public function makeHash(string $string, string $algo='sha256', string $salt = '' ){
        if(strlen(trim($salt))<1){
            $salt = $this->generate(8);
            return [hash($algo, $string.$salt), $salt];
        }else
        return hash($algo, $string.$salt);
    }


    /**
     * Set the alphabet string to hash with
     * @param string $alphabet
     */
    public function setAlphabet(string $alphabet){
        $this->alphabet = $alphabet;
        $this->alphabetLength = strlen($alphabet);
    }


    /**
     * Generate a token
     * @param int $length - The length of the token
     * @return string
     */
    public function generate(int $length){
        $token = '';
        for ($i = 0; $i < $length; $i++) {
            $randomKey = $this->getRandomInteger(0, $this->alphabetLength);
            $token .= $this->alphabet[$randomKey];
        }

        return substr(uniqid($token), 0, $length);
    }

    /**
     * Return a random integer between two numbers
     * @param int $min - The minimum number to set the generated int between.
     * @param int $max - The maximum number to set the generated int between
     * @return int
     */
    private function getRandomInteger(int $min, int $max)
    {
        $range = ($max - $min);

        if ($range < 0) {
            // Not so random...
            return $min;
        }

        $log = log($range, 2);

        // Length in bytes.
        $bytes = (int) ($log / 8) + 1;

        // Length in bits.
        $bits = (int) $log + 1;

        // Set all lower bits to 1.
        $filter = (int) (1 << $bits) - 1;

        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));

            // Discard irrelevant bits.
            $rnd = $rnd & $filter;

        } while ($rnd >= $range);

        return ($min + $rnd);
    }




}


