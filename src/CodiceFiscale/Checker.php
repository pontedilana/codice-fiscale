<?php

namespace CodiceFiscale;

/**
 * Class to check if italian fiscal's code (codice fiscale) is formally Correct.
 *
 * @author SimoneNigro
 */
class Checker
{
    // fiscal's code regex
    public const REGEX_CODICEFISCALE = '/^[A-Z]{6}[0-9LMNPQRSTUV]{2}[ABCDEHLMPRST]{1}[0-9LMNPQRSTUV]{2}[A-Z]{1}[0-9LMNPQRSTUV]{3}[A-Z]{1}$/';

    // women char
    public const CHR_WOMEN = 'F';

    // male char
    public const CHR_MALE = 'M';

    /**
     * is Valid.
     */
    private bool $isValid = false;

    /**
     * Sex.
     */
    private ?string $sex = null;

    /**
     * Country Birth.
     */
    private ?string $countryBirth = null;

    /**
     * Day Birth.
     */
    private ?string $dayBirth = null;

    /**
     * Month Birth.
     */
    private ?string $monthBirth = null;

    /**
     * Year Birth.
     */
    private ?string $yearBirth = null;

    /**
     * Error.
     */
    private ?string $error = null;

    /**
     * List replace omocodia.
     *
     * @var array<string, string>
     */
    private array $listDecOmocodia = ['A' => '!', 'B' => '!', 'C' => '!', 'D' => '!', 'E' => '!', 'F' => '!', 'G' => '!', 'H' => '!', 'I' => '!', 'J' => '!', 'K' => '!', 'L' => '0', 'M' => '1', 'N' => '2', 'O' => '!', 'P' => '3', 'Q' => '4', 'R' => '5', 'S' => '6', 'T' => '7', 'U' => '8', 'V' => '9', 'W' => '!', 'X' => '!', 'Y' => '!', 'Z' => '!'];

    /**
     * Positions affected characters to alteration of coding in the case of omocodia.
     *
     * @var array<int>
     */
    private array $listSostOmocodia = [6, 7, 9, 10, 12, 13, 14];

    /**
     * Weight even char.
     *
     * @var array<int|string, int>
     */
    private array $listEvenChar = ['0' => 0, '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9, 'A' => 0, 'B' => 1, 'C' => 2, 'D' => 3, 'E' => 4, 'F' => 5, 'G' => 6, 'H' => 7, 'I' => 8, 'J' => 9, 'K' => 10, 'L' => 11, 'M' => 12, 'N' => 13, 'O' => 14, 'P' => 15, 'Q' => 16, 'R' => 17, 'S' => 18, 'T' => 19, 'U' => 20, 'V' => 21, 'W' => 22, 'X' => 23, 'Y' => 24, 'Z' => 25];

    /**
     * Weight odd char.
     *
     * @var array<int|string, int>
     */
    private array $listOddChar = ['0' => 1, '1' => 0, '2' => 5, '3' => 7, '4' => 9, '5' => 13, '6' => 15, '7' => 17, '8' => 19, '9' => 21, 'A' => 1, 'B' => 0, 'C' => 5, 'D' => 7, 'E' => 9, 'F' => 13, 'G' => 15, 'H' => 17, 'I' => 19, 'J' => 21, 'K' => 2, 'L' => 4, 'M' => 18, 'N' => 20, 'O' => 11, 'P' => 3, 'Q' => 6, 'R' => 8, 'S' => 12, 'T' => 14, 'U' => 16, 'V' => 10, 'W' => 22, 'X' => 25, 'Y' => 24, 'Z' => 23];

    /**
     * Control code (char 16).
     *
     * @var array<int, string>
     */
    private array $listCtrlCode = [0 => 'A', 1 => 'B', 2 => 'C', 3 => 'D', 4 => 'E', 5 => 'F', 6 => 'G', 7 => 'H', 8 => 'I', 9 => 'J', 10 => 'K', 11 => 'L', 12 => 'M', 13 => 'N', 14 => 'O', 15 => 'P', 16 => 'Q', 17 => 'R', 18 => 'S', 19 => 'T', 20 => 'U', 21 => 'V', 22 => 'W', 23 => 'X', 24 => 'Y', 25 => 'Z'];

    /**
     * Month code.
     *
     * @var array<string, string>
     */
    private array $listDecMonth = ['A' => '01', 'B' => '02', 'C' => '03', 'D' => '04', 'E' => '05', 'H' => '06', 'L' => '07', 'M' => '08', 'P' => '09', 'R' => '10', 'S' => '11', 'T' => '12'];

    /**
     * Error list.
     *
     * @var string[]
     */
    private array $listError = [0 => 'Empty code', 1 => 'Length error', 2 => 'Code with wrong char', 3 => 'Code with wrong char in omocodia', 4 => 'Wrong code'];

    /**
     * Getter isValid.
     */
    public function getIsValid(): bool
    {
        return $this->isValid;
    }

    /**
     * Getter Error.
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * Getter Sex.
     */
    public function getSex(): ?string
    {
        return $this->sex;
    }

    /**
     * Getter CountryBirth.
     */
    public function getCountryBirth(): ?string
    {
        return $this->countryBirth;
    }

    public function getYearBirth(): ?string
    {
        return $this->yearBirth;
    }

    /**
     * Getter MonthBirth.
     */
    public function getMonthBirth(): ?string
    {
        return $this->monthBirth;
    }

    /**
     * Getter DayBirth.
     */
    public function getDayBirth(): ?string
    {
        return $this->dayBirth;
    }

    /**
     * Check Codice Fiscale.
     */
    public function isFormallyCorrect(string $codiceFiscale): bool
    {
        $this->resetProperties();

        try {
            // 1. Check empty
            if (trim($codiceFiscale) === '') {
                $this->raiseException(0);
            }

            // 2. Check length
            if (strlen($codiceFiscale) !== 16) {
                $this->raiseException(1);
            }

            $codiceFiscale = strtoupper($codiceFiscale);

            // 3. Check general pattern
            if (1 !== preg_match(self::REGEX_CODICEFISCALE, $codiceFiscale)) {
                $this->raiseException(2);
            }

            $CFCharList = str_split($codiceFiscale);

            // 4. Explicitly check omocodia characters
            foreach ($this->listSostOmocodia as $pos) {
                $char = $CFCharList[$pos];

                // Se è un numero, va bene
                if (is_numeric($char)) {
                    continue;
                }

                // Se è una lettera, verifica che sia mappata correttamente per l'omocodia
                if (!isset($this->listDecOmocodia[$char]) || $this->listDecOmocodia[$char] === '!') {
                    $this->raiseException(3); // ERRORE DI OMOCODIA
                }
            }

            // Se ha superato il controllo omocodia, prosegue col checksum
            $pari = 0;
            $dispari = $this->listOddChar[$CFCharList[14]];

            // 5. Calculate checksum
            for ($i = 0; $i < 13; $i += 2) {
                $dispari += $this->listOddChar[$CFCharList[$i]];
                $pari += $this->listEvenChar[$CFCharList[$i + 1]];
            }

            // 6. Verify checksum
            if (!($this->listCtrlCode[($pari + $dispari) % 26] === $CFCharList[15])) {
                $this->raiseException(4);
            }

            // replace "omocodie"
            foreach ($this->listSostOmocodia as $item) {
                if (!is_numeric($CFCharList[$item])) {
                    $CFCharList[$item] = $this->listDecOmocodia[$CFCharList[$item]];
                }
            }

            $codiceFiscaleAdattato = implode('', $CFCharList);

            // get fiscal code data
            $this->sex = (((int) substr($codiceFiscaleAdattato, 9, 2) > 40) ? self::CHR_WOMEN : self::CHR_MALE);
            $this->countryBirth = substr($codiceFiscaleAdattato, 11, 4);
            $this->yearBirth = substr($codiceFiscaleAdattato, 6, 2);
            $this->dayBirth = substr($codiceFiscaleAdattato, 9, 2);
            $this->monthBirth = $this->listDecMonth[substr($codiceFiscaleAdattato, 8, 1)];

            // get day birth if sex is women
            if (self::CHR_WOMEN === $this->sex) {
                $this->dayBirth = (string) ((int) $this->dayBirth - 40);

                if (1 === strlen($this->dayBirth)) {
                    $this->dayBirth = '0' . $this->dayBirth;
                }
            }

            // End verify
            $this->isValid = true;
            $this->error = null;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->isValid = false;
        }

        return $this->isValid;
    }

    /**
     * Reset Class Properties.
     */
    private function resetProperties(): void
    {
        $this->isValid = false;
        $this->sex = null;
        $this->countryBirth = null;
        $this->dayBirth = null;
        $this->monthBirth = null;
        $this->yearBirth = null;
        $this->error = null;
    }

    /**
     * Raise Exception.
     *
     * @throws \Exception
     */
    private function raiseException(int $errorNum): never
    {
        $errMessage = $this->listError[$errorNum] ?? 'Unknown Exception';

        throw new \Exception($errMessage, $errorNum);
    }
}
