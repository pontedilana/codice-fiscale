<?php

use CodiceFiscale\Checker;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * @phpstan-type CodiceFiscaleData array{sex: 'M'|'F', countryBirth: string, yearBirth: string, monthBirth: string, dayBirth: string}
 */
#[CoversClass(Checker::class)]
class CheckerTest extends TestCase
{
    private Checker $checker;

    protected function setUp(): void
    {
        $this->checker = new Checker();
    }

    /**
     * Test per codici fiscali validi.
     *
     * @param CodiceFiscaleData $expectedFields
     */
    #[DataProvider('validCodiceFiscaleProvider')]
    public function testValidCodiceFiscale(string $validCodiceFiscale, array $expectedFields): void
    {
        $isValid = $this->checker->isFormallyCorrect($validCodiceFiscale);

        self::assertTrue($isValid, "Codice fiscale valido ma considerato non valido: $validCodiceFiscale");
        self::assertTrue($this->checker->getIsValid(), "Codice fiscale valido ma considerato non valido: $validCodiceFiscale");

        // Verifica dei campi estratti
        self::assertSame($expectedFields['sex'], $this->checker->getSex(), "Errore sul campo sex per: $validCodiceFiscale");
        self::assertSame($expectedFields['countryBirth'], $this->checker->getCountryBirth(), "Errore sul campo countryBirth per: $validCodiceFiscale");
        self::assertSame($expectedFields['yearBirth'], $this->checker->getYearBirth(), "Errore sul campo yearBirth per: $validCodiceFiscale");
        self::assertSame($expectedFields['monthBirth'], $this->checker->getMonthBirth(), "Errore sul campo monthBirth per: $validCodiceFiscale");
        self::assertSame($expectedFields['dayBirth'], $this->checker->getDayBirth(), "Errore sul campo dayBirth per: $validCodiceFiscale");
    }

    /**
     * @return array<int, array{
     *     0: string,
     *     1: CodiceFiscaleData
     * }>
     */
    public static function validCodiceFiscaleProvider(): array
    {
        return [
            [
                'VRDGPP13R10B293P',
                [
                    'sex' => 'M',
                    'countryBirth' => 'B293',
                    'yearBirth' => '13',
                    'monthBirth' => '10',
                    'dayBirth' => '10',
                ],
            ],
            [
                'STRMRA00A41B293J',
                [
                    'sex' => 'F',
                    'countryBirth' => 'B293',
                    'yearBirth' => '00',
                    'monthBirth' => '01',
                    'dayBirth' => '01',
                ],
            ],
            [
                'BNZVCN32S10E573Z',
                [
                    'sex' => 'M',
                    'countryBirth' => 'E573',
                    'yearBirth' => '32',
                    'monthBirth' => '11',
                    'dayBirth' => '10',
                ],
            ],
            [
                'CCHGNN65R05H163U',
                [
                    'sex' => 'M',
                    'countryBirth' => 'H163',
                    'yearBirth' => '65',
                    'monthBirth' => '10',
                    'dayBirth' => '05',
                ],
            ],
            // Codici fiscali validi con omocodia
            [
                'BNZVCN32S10E57PV',
                [
                    'sex' => 'M',
                    'countryBirth' => 'E573',
                    'yearBirth' => '32',
                    'monthBirth' => '11',
                    'dayBirth' => '10',
                ],
            ],
            [
                'BNZVCNPNSMLERTPX',
                [
                    'sex' => 'M',
                    'countryBirth' => 'E573',
                    'yearBirth' => '32',
                    'monthBirth' => '11',
                    'dayBirth' => '10',
                ],
            ],
            [
                'CCHGNN67R05H1S3I',
                [
                    'sex' => 'M',
                    'countryBirth' => 'H163',
                    'yearBirth' => '67',
                    'monthBirth' => '10',
                    'dayBirth' => '05',
                ],
            ],
        ];
    }


    /**
     * Test per l'errore 0: Empty code
     */
    public function testEmptyCodeError(): void
    {
        $codiceFiscale = '';
        $isValid = $this->checker->isFormallyCorrect($codiceFiscale);

        self::assertFalse($isValid, "Empty code should be invalid.");
        self::assertSame('Empty code', $this->checker->getError());
    }

    /**
     * Test per l'errore 1: Length error
     */
    public function testLengthError(): void
    {
        $codiceFiscale = 'ABCDEF12345'; // Meno di 16 caratteri
        $isValid = $this->checker->isFormallyCorrect($codiceFiscale);

        self::assertFalse($isValid, "Codice fiscale troppo corto dovrebbe essere invalido.");
        self::assertSame('Length error', $this->checker->getError());

        $codiceFiscale = 'ABCDEF123456789012345'; // Più di 16 caratteri
        $isValid = $this->checker->isFormallyCorrect($codiceFiscale);

        self::assertFalse($isValid, "Codice fiscale troppo lungo dovrebbe essere invalido.");
        self::assertSame('Length error', $this->checker->getError());
    }

    /**
     * Test per l'errore 2: Code with wrong char
     */
    public function testWrongCharError(): void
    {
        // Usare un codice fiscale lungo 16 caratteri con un carattere vietato (es. carattere speciale)
        $codiceFiscale = 'RSSMRA85M0@H501Z';
        $isValid = $this->checker->isFormallyCorrect($codiceFiscale);

        self::assertFalse($isValid, "Codice fiscale con caratteri speciali dovrebbe essere invalido.");
        self::assertSame('Code with wrong char', $this->checker->getError());

        // Usare un codice fiscale lungo 16 caratteri ma composto solo da lettere (pattern errato)
        $codiceFiscale = 'AAAAAAAAAAAAAAAA';
        $isValid = $this->checker->isFormallyCorrect($codiceFiscale);

        self::assertFalse($isValid, "Codice fiscale composto solo da lettere dovrebbe essere invalido.");
        self::assertSame('Code with wrong char', $this->checker->getError());
    }

    /**
     * Test per l'errore 3: Code with wrong char in omocodia
     */
    //    public function testWrongCharOmocodiaError(): void
    //    {
    //        // Usare un carattere non permesso in una posizione di omocodia
    //        $codiceFiscale = 'BNZVCN32S10E57PV'; // Ultimo carattere non valido per omocodia
    //        $isValid = $this->checker->isFormallyCorrect($codiceFiscale);
    //
    //        self::assertFalse($isValid, "Codice fiscale con omocodia errata dovrebbe essere invalido.");
    //        self::assertSame('Code with wrong char in omocodia', $this->checker->getError());
    //    }

    /**
     * Test per l'errore 4: Wrong code
     */
    public function testWrongCodeError(): void
    {
        $codiceFiscale = 'RSSMRA85M01H501X'; // Codice fiscale formalmente corretto ma con checksum errato
        $isValid = $this->checker->isFormallyCorrect($codiceFiscale);

        self::assertFalse($isValid, "Codice fiscale con checksum errato dovrebbe essere invalido.");
        self::assertSame('Wrong code', $this->checker->getError());
    }

    //    /**
    //     * @var string[]
    //     */
    //    protected array $codiciFiscaliOk;
    //
    //    /**
    //     * @var string[]
    //     */
    //    protected array $codiciFiscaliKo;
    //
    //    /**
    //     * @var string[]
    //     */
    //    protected array $omocodie;
    //
    //    public function setUp(): void
    //    {
    //        $this->codiciFiscaliOk = [
    //            'VRDGPP13R10B293P',
    //            'CHRVRD74S53L219F',
    //            'VRDGPP13R10B29PL',
    //        ];
    //
    //        $this->codiciFiscaliKo = [
    //            'SLLNDR91C06F205',
    //            'SXLNDQ67CS8Z210L',
    //            'XSD91S67CS8Z210L',
    //        ];
    //
    //        $this->omocodie = [
    //            'BNZVCN32S10E57PV',
    //            'BNZVCNPNSMLERTPX',
    //            'CCHGNN67R05H1S3I',
    //        ];
    //    }
    //
    //    public function testCorrettezzaFormaleCodiceFiscale(): void
    //    {
    //        $checker = new Checker();
    //
    //        foreach ($this->codiciFiscaliOk as $cf) {
    //            self::assertTrue($checker->isFormallyCorrect($cf));
    //            self::assertTrue($checker->getIsValid());
    //        }
    //
    //        foreach ($this->codiciFiscaliKo as $cf) {
    //            self::assertFalse($checker->isFormallyCorrect($cf));
    //            self::assertFalse($checker->getIsValid());
    //        }
    //
    //        foreach ($this->omocodie as $cf) {
    //            self::assertTrue($checker->isFormallyCorrect($cf));
    //            self::assertTrue($checker->getIsValid());
    //        }
    //    }
    //
    //    public function testIsFormallyCorrectRaisesException0(): void
    //    {
    //        $checker = new \CodiceFiscale\Checker();
    //
    //        // Test for empty code
    //        self::assertFalse($checker->isFormallyCorrect(''));
    //        self::assertFalse($checker->getIsValid());
    //        self::assertSame('Empty code', $checker->getError());
    //    }
    //
    //    public function testIsFormallyCorrectRaisesException1(): void
    //    {
    //        $checker = new \CodiceFiscale\Checker();
    //        // Test for length error
    //        self::assertFalse($checker->isFormallyCorrect('short_code'));
    //        self::assertFalse($checker->getIsValid());
    //        self::assertSame('Length error', $checker->getError());
    //    }
    //
    //    public function testIsFormallyCorrectRaisesException2(): void
    //    {
    //        $checker = new \CodiceFiscale\Checker();
    //        // Test for wrong char in code
    //        self::assertFalse($checker->isFormallyCorrect('1234567890123456'));
    //        self::assertFalse($checker->getIsValid());
    //        self::assertSame('Code with wrong char', $checker->getError());
    //    }
    //
    //    public function testIsFormallyCorrectRaisesException3(): void
    //    {
    //        $checker = new \CodiceFiscale\Checker();
    //        // Test for wrong char in omocodia
    //        self::assertFalse($checker->isFormallyCorrect('BNZVCN32S10E57PZ'));
    //        self::assertFalse($checker->getIsValid());
    //        self::assertSame('Code with wrong char in omocodia', $checker->getError());
    //    }
    //
    //    public function testIsFormallyCorrectRaisesException4(): void
    //    {
    //        $checker = new \CodiceFiscale\Checker();
    //        // Test for wrong code
    //        self::assertFalse($checker->isFormallyCorrect('wrong_code'));
    //        self::assertFalse($checker->getIsValid());
    //        self::assertSame('Wrong code', $checker->getError());
    //        $this->expectException(\InvalidArgumentException::class);
    //    }



}
