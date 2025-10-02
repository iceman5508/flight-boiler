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
/**
 * * Create a random token that is secure to be used.
 * This token can be limited in length as well.
 *
 *
 * @author Isaac Parker
 */
class Token
{

    /** @var string the alphabet to work with*/
    protected $alphabet;

    protected $lowercase_letters;
    protected $uppercase_letters;

    protected $numbers;

    protected $symbols; 

    /** @var int the alphabet length*/
    protected $alphabetLength;

    private bool $use_rules = false;

    private $rules = [
        'lowercase' => ['min' => null, 'max' => null, 'exclude' => []],
        'uppercase' => ['min' => null, 'max' => null, 'exclude' => []],
        'numbers' => ['min' => null, 'max' => null, 'exclude' => []],
        'symbols' => ['min' => null, 'max' => null, 'exclude' => []],
    ];


      /**
     * Token constructor - The entry point into the class.
     * @param string $alphabet - The string to create token around
     */
    public function __construct(string $alphabet = ''){
        if ('' !== $alphabet) {
            $this->splitStringByCharacterType($alphabet);

        } else {
            // Split default letters into lowercase and uppercase
            $this->lowercase_letters = implode(range('a', 'z'));
            $this->uppercase_letters = implode(range('A', 'Z'));
            $this->numbers = implode(range(0, 9));
            $this->symbols = '!@#$%^&*()-_=+[]{};:,.<>/?';

        }

        // Combine all pools to form the base alphabet
        $options = str_shuffle(trim("$this->lowercase_letters$this->uppercase_letters$this->numbers$this->symbols"));
        
        $this->setAlphabet(
            
            $options
        );
    }



    /**
     * Set a specific rule for a character type (lowercase, uppercase, numbers, or symbols).
     *The use rule bool is set to tue on sucessfil use of this function
     * @param string $type The character type ('lowercase', 'uppercase', 'numbers', or 'symbols').
     * @param array $rule An associative array containing the rule ('min', 'max', 'exclude').
     * @return self
     */
    public function setRule(string $type, array $rule): self
    {
        if (array_key_exists($type, $this->rules)) {
            if(!$this->use_rules){
                $this->use_rules = true;
            }
            $this->rules[$type] = array_merge($this->rules[$type], $rule);
        }
        return $this;
    }


    /**
     * Set the use rules bool
     * @param bool $use
     * @return void
     */
    public function useRule(bool $use){
        $this->use_rules = $use;
    }

    /**
     * Split string into different character types: lowercase, uppercase, numbers, and symbols.
     * @param string $string
     */
    protected function splitStringByCharacterType(string $string)
    {
        $this->lowercase_letters = preg_replace('/[^a-z]/', '', $string);
        $this->uppercase_letters = preg_replace('/[^A-Z]/', '', $string);
        $this->numbers = preg_replace('/[^0-9]/', '', $string);
        $this->symbols = preg_replace('/[a-zA-Z0-9]/', '', $string);
    }

    /**
     * Set the alphabet to use for token generation
     * @param string $alphabet
     */
    public function setAlphabet(string $alphabet){
        $this->alphabet = $alphabet;
        $this->alphabetLength = strlen($alphabet);
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
     * Generate a token
     * @param int $length - The length of the token
     * @return string
     */
    public function generate(int $length): string
    {
        // 1. Check if rules are being used
        if (!$this->use_rules) {
            // Original logic: use the entire alphabet randomly
            $token = '';
            for ($i = 0; $i < $length; $i++) {
                // getRandomInteger max argument is exclusive, so alphabetLength works for 0-indexed access
                $randomKey = $this->getRandomInteger(0, $this->alphabetLength);
                $token .= $this->alphabet[$randomKey];
            }
            // Retain original uniqid implementation (though this doesn't look correct for generating a clean token)
            // It is kept for backward compatibility with the original implementation structure.
            return substr(uniqid($token), 0, $length);
        }

        // --- Rule-based generation logic ---
        $pools = $this->getCharacterTypePools();
        $rules = $this->rules;
        $tokenArray = [];
        // Updated counts to include lowercase and uppercase
        $currentCounts = ['lowercase' => 0, 'uppercase' => 0, 'numbers' => 0, 'symbols' => 0];
        $totalRequired = 0;

        // Phase 1: Enforce minimum required characters
        foreach ($rules as $type => $rule) {
            $min = $rule['min'] ?? 0;
            $totalRequired += $min;

            if ($totalRequired > $length) {
                throw new \LengthException("The total minimum required characters ({$totalRequired}) exceeds the desired token length ({$length}).");
            }

            for ($i = 0; $i < $min; $i++) {
                if (!empty($pools[$type])) {
                    $poolLength = strlen($pools[$type]);
                    $char = $pools[$type][$this->getRandomInteger(0, $poolLength)];
                    $tokenArray[] = $char;
                    $currentCounts[$type]++;
                }
            }
        }

        // Phase 2: Fill the remainder of the token based on availability and max rules
        $remainingLength = $length - count($tokenArray);
        // Updated character types
        $charTypes = ['lowercase', 'uppercase', 'numbers', 'symbols'];

        for ($i = 0; $i < $remainingLength; $i++) {
            $eligibleTypes = [];

            // Determine which types are still eligible
            foreach ($charTypes as $type) {
                $max = $rules[$type]['max'];
                // Check if the pool is not empty AND (max is not set OR current count is below max)
                if (!empty($pools[$type]) && ($max === null || $currentCounts[$type] < $max)) {
                    $eligibleTypes[] = $type;
                }
            }

            // If no types are eligible, stop filling
            if (empty($eligibleTypes)) {
                break;
            }

            // Randomly select an eligible type
            $selectedIndex = $this->getRandomInteger(0, count($eligibleTypes));
            $selectedType = $eligibleTypes[$selectedIndex];

            // Pick a random character from the selected type's pool
            $poolLength = strlen($pools[$selectedType]);
            $char = $pools[$selectedType][$this->getRandomInteger(0, $poolLength)];

            $tokenArray[] = $char;
            $currentCounts[$selectedType]++;
        }

        // Phase 3: Final Shuffle to randomize character positions
        shuffle($tokenArray);

        return implode('', $tokenArray);
    }

    /**
     * Prepares character pools, applying exclude rules.
     * @return array
     */
    private function getCharacterTypePools(): array
    {
        $pools = [
            'lowercase' => $this->lowercase_letters,
            'uppercase' => $this->uppercase_letters,
            'numbers' => $this->numbers,
            'symbols' => $this->symbols,
        ];

        foreach ($this->rules as $type => $rule) {
            if (!empty($rule['exclude'])) {
                // Remove excluded characters from the pool
                // str_replace handles both string and array input for $replace (second arg)
                $pools[$type] = str_replace($rule['exclude'], '', $pools[$type]);
            }
        }
        return $pools;
    }


    /**
     * Return a random integer between two numbers
     * @param int $min - The minimum number to set the generated int between.
     * @param int $max - The maximum number to set the generated int between (exclusive).
     * @return int
     */
    private function getRandomInteger(int $min, int $max)
    {
        $range = ($max - $min);

        if ($range <= 0) {
            // Not enough range for random generation
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
            $rnd = ($rnd & $filter);
        } while ($rnd >= $range);

        return ($min + $rnd);
    }



}


