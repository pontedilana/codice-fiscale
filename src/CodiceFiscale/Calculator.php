<?php

namespace CodiceFiscale;

class Calculator
{
    /**
     * @var string[]
     */
    private array $mesi = ['A', 'B', 'C', 'D', 'E', 'H', 'L', 'M', 'P', 'R', 'S', 'T'];

    /**
     * @var string[]
     */
    private array $vocali = ['A', 'E', 'I', 'O', 'U'];

    /**
     * @var string[]
     */
    private array $consonanti = ['B', 'C', 'D', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'V', 'W', 'X', 'Y', 'Z'];

    /**
     * @var string[]
     */
    private array $numeri = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    /**
     * @var string[]
     */
    private array $alfabeto = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

    /**
     * @var array<int|string, int>
     */
    private array $matriceCodiceControllo = ['01' => 1, '00' => 0, '11' => 0, '10' => 1, '21' => 5, '20' => 2, '31' => 7, '30' => 3, '41' => 9, '40' => 4, '51' => 13, '50' => 5, '61' => 15, '60' => 6, '71' => 17, '70' => 7, '81' => 19, '80' => 8, '91' => 21, '90' => 9, '101' => 1, '100' => 0, '111' => 0, '110' => 1, '121' => 5, '120' => 2, '131' => 7, '130' => 3, '141' => 9, '140' => 4, '151' => 13, '150' => 5, '161' => 15, '160' => 6, '171' => 17, '170' => 7, '181' => 19, '180' => 8, '191' => 21, '190' => 9, '201' => 2, '200' => 10, '211' => 4, '210' => 11, '221' => 18, '220' => 12, '231' => 20, '230' => 13, '241' => 11, '240' => 14, '251' => 3, '250' => 15, '261' => 6, '260' => 16, '271' => 8, '270' => 17, '281' => 12, '280' => 18, '291' => 14, '290' => 19, '301' => 16, '300' => 20, '311' => 10, '310' => 21, '321' => 22, '320' => 22, '331' => 25, '330' => 23, '341' => 24, '340' => 24, '351' => 23, '350' => 25];

    public function calcola(
        string $nome,
        string $cognome,
        string $sesso,
        \DateTime $dataNascita,
        string $codiceComune,
    ): string {
        $nome = $this->sanitizeString($nome);
        $cognome = $this->sanitizeString($cognome);
        $sesso = $this->sanitizeString($sesso);

        $giorno = $dataNascita->format('d');
        $mese = $dataNascita->format('n');
        $anno = $dataNascita->format('y');

        // inizia con il calcolo dei primi sei caratteri corrispondenti al nome e cognome
        $codiceFiscale = $this->calcolaCognome($cognome) . $this->calcolaNome($nome);

        // calcola i dati corrispondenti alla data di nascita
        if ('F' === $sesso) {
            $giorno += 40;
        }
        $codiceFiscale .= $anno . $this->mesi[$mese - 1] . $giorno;

        // aggiunge il codice del comune
        $codiceFiscale .= $codiceComune;

        // e finalmente calcola il codice controllo
        $codiceControllo = 0;
        $alfanumerico = array_merge($this->numeri, $this->alfabeto);
        for ($i = 0; $i < 15; ++$i) {
            $codiceControllo += $this->matriceCodiceControllo[array_search($codiceFiscale[$i], $alfanumerico, true) . (($i + 1) % 2)];
        }

        return $codiceFiscale . $this->alfabeto[$codiceControllo % 26];
    }

    private function calcolaNome(string $string): string
    {
        $i = 0;
        $res = '';
        $cons = '';
        while (strlen($cons) < 4 && ($i + 1 <= strlen($string))) {
            if (in_array($string[$i], $this->consonanti, true)) {
                $cons .= $string[$i];
            }
            ++$i;
        }

        if (strlen($cons) > 3) {
            return $cons[0] . $cons[2] . $cons[3];
        }

        $res = $cons;

        // Se non bastano prendo le vocali
        $i = 0;
        while (strlen($res) < 3 && ($i + 1 <= strlen($string))) {
            if (in_array($string[$i], $this->vocali, true)) {
                $res .= $string[$i];
            }
            ++$i;
        }
        $res .= 'XXX';

        return substr($res, 0, 3);
    }

    private function calcolaCognome(string $string): string
    {
        $res = '';
        $i = 0;
        while (strlen($res) < 3 && ($i + 1 <= strlen($string))) {
            if (in_array($string[$i], $this->consonanti, true)) {
                $res .= $string[$i];
            }
            ++$i;
        }

        // Se non bastano le consonanti, prendo le vocali
        $i = 0;
        while (strlen($res) < 3 && ($i + 1 <= strlen($string))) {
            if (in_array($string[$i], $this->vocali, true)) {
                $res .= $string[$i];
            }
            ++$i;
        }

        $res .= 'XXX';

        return substr($res, 0, 3);
    }

    private function sanitizeString(string $string): string
    {
        $string = trim($string);

        // Converte caratteri speciali in ASCII, mantenendo solo lettere latine
        $string = transliterator_transliterate('Any-Latin; Latin-ASCII; [\u0080-\u7fff] remove', $string);

        if (false === $string) {
            throw new \RuntimeException('Error during string sanitization');
        }

        // Rimuove qualsiasi carattere non appartenente all'alfabeto latino e ai numeri
        $string = preg_replace('/[^A-Za-z0-9]/u', '', $string);

        // Converte in maiuscolo
        return mb_strtoupper((string) $string);
    }
}
