<?php

use CodiceFiscale\Calculator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../vendor/autoload.php';

#[CoversClass(Calculator::class)]
class CalculatorTest extends TestCase
{
    /**
     * DataProvider per testCalcoloCodiceFiscale.
     *
     * @return iterable<string, array{
     *     nome: string,
     *     cognome: string,
     *     sesso: 'm'|'f'|'M'|'F',
     *     dataNascita: \DateTime,
     *     codiceComune: string,
     *     expected: string
     * }>
     */
    public static function codiceFiscaleProvider(): iterable
    {
        return [
            'andrea usuelli' => [
                'nome' => 'andrea',
                'cognome' => 'usuelli',
                'sesso' => 'm',
                'dataNascita' => new DateTime('1991-01-05'),
                'codiceComune' => 'F205',
                'expected' => 'SLLNDR91A05F205T',
            ],
            'chiara nònlòsò' => [
                'nome' => 'chiara',
                'cognome' => 'nònlòsò',
                'sesso' => 'f',
                'dataNascita' => new DateTime('1992-03-06'),
                'codiceComune' => 'F205',
                'expected' => 'NNLCHR92C46F205N',
            ],
            'hu hu (M)' => [
                'nome' => 'hu',
                'cognome' => 'hu',
                'sesso' => 'm',
                'dataNascita' => new DateTime('1956-09-30'),
                'codiceComune' => 'Z210',
                'expected' => 'HUXHUX56P30Z210K',
            ],
            'hu hu (F)' => [
                'nome' => 'hu',
                'cognome' => 'hu',
                'sesso' => 'f',
                'dataNascita' => new DateTime('1956-09-30'),
                'codiceComune' => 'Z210',
                'expected' => 'HUXHUX56P70Z210O',
            ],
            'luca marco giovanni d\'abate spigna maria' => [
                'nome' => 'luca marco giovanni',
                'cognome' => "d'abate spigna maria",
                'sesso' => 'm',
                'dataNascita' => new DateTime('1968-05-26'),
                'codiceComune' => 'C926',
                'expected' => 'DBTLMR68E26C926B',
            ],
            'l\'arnalda d\'annunzio' => [
                'nome' => "l'arnalda",
                'cognome' => "d'annunzio",
                'sesso' => 'F',
                'dataNascita' => new DateTime('1983-12-31'),
                'codiceComune' => 'D856',
                'expected' => 'DNNLNL83T71D856L',
            ],
            'mario rossi (codice comune minuscolo)' => [
                'nome' => 'mario',
                'cognome' => 'rossi',
                'sesso' => 'M',
                'dataNascita' => new DateTime('1979-11-18'),
                'codiceComune' => ' f205 ',
                'expected' => 'RSSMRA79S18F205J',
            ],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidSessoProvider(): iterable
    {
        return [
            'empty' => [''],
            'unknown letter' => ['X'],
            'full word' => ['femmina'],
        ];
    }

    #[DataProvider('invalidSessoProvider')]
    public function testInvalidSesso(string $sesso): void
    {
        $cf = new Calculator();

        $this->expectException(\InvalidArgumentException::class);
        $cf->calcola('mario', 'rossi', $sesso, new DateTime('1979-11-18'), 'F205');
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidCodiceComuneProvider(): iterable
    {
        return [
            'empty' => [''],
            'too short' => ['F20'],
            'too long' => ['F2055'],
            'digit first' => ['1205'],
            'letter in number' => ['F2O5'],
        ];
    }

    #[DataProvider('invalidCodiceComuneProvider')]
    public function testInvalidCodiceComune(string $codiceComune): void
    {
        $cf = new Calculator();

        $this->expectException(\InvalidArgumentException::class);
        $cf->calcola('mario', 'rossi', 'M', new DateTime('1979-11-18'), $codiceComune);
    }

    #[DataProvider('codiceFiscaleProvider')]
    public function testCalcoloCodiceFiscale(
        string $nome,
        string $cognome,
        string $sesso,
        DateTime $dataNascita,
        string $codiceComune,
        string $expected,
    ): void {
        $cf = new Calculator();
        self::assertSame($expected, $cf->calcola($nome, $cognome, $sesso, $dataNascita, $codiceComune));
    }

    /**
     * DataProvider per testSanitizeString.
     *
     * @return array<string, array{string, string}>
     */
    public static function sanitizeStringProvider(): iterable
    {
        return [
            'basic lowercase' => ['andrea', 'ANDREA'],
            'basic uppercase' => ['USUELLI', 'USUELLI'],
            'trim spaces' => ['  chiara  ', 'CHIARA'],
            'remove accents' => ['nònlòsò', 'NONLOSO'],
            'keep simple chars' => ['HU', 'HU'],
            'strip special characters' => ['luca@marco#giovanni', 'LUCAMARCOGIOVANNI'],
            'handle apostrophes' => ["d'abate", "DABATE"],
            'mixed spaces' => ['L U M É', 'LUME'],
            'international chars' => ['résumé', 'RESUME'],
            'Japanese characters' => ['こんにちは', 'KONNICHIHA'],
            'Chinese characters' => ['你好', 'NIHAO'],
            'Cyrillic characters' => ['Здравей', 'ZDRAVEJ'],
            'symbols and numbers' => ['123 abc!', '123ABC'],
        ];
    }

    #[DataProvider('sanitizeStringProvider')]
    public function testSanitizeString(string $input, string $expected): void
    {
        $cf = new Calculator();

        // Accedi al metodo privato sanitizeString tramite Reflection
        $reflection = new \ReflectionClass($cf);
        $method = $reflection->getMethod('sanitizeString');

        // Esegui il metodo su Calculator
        $result = $method->invoke($cf, $input);

        self::assertSame($expected, $result, "Errore su sanitizeString per input: '$input'");
    }
}
