<?php

use CodiceFiscale\Calculator;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * @internal
 *
 * @coversNothing
 */
class CalculatorTest extends TestCase
{
    /**
     * @var array<array-key, array{
     *     nome: string,
     *     cognome: string,
     *     sesso: 'm'|'f'|'M'|'F',
     *     dataNascita: \DateTime,
     *     codiceComune: string,
     *     expected: string
     * }>
     */
    protected array $persons;

    public function setUp(): void
    {
        $this->persons = [
            [
                'nome' => 'andrea',
                'cognome' => 'usuelli',
                'sesso' => 'm',
                'dataNascita' => new DateTime('1991-01-05'),
                'codiceComune' => 'F205',
                'expected' => 'SLLNDR91A05F205T',
            ],
            [
                'nome' => 'chiara',
                'cognome' => 'nònlòsò',
                'sesso' => 'f',
                'dataNascita' => new DateTime('1992-03-06'),
                'codiceComune' => 'F205',
                'expected' => 'NNLCHR92C46F205N',
            ],
            [
                'nome' => 'hu',
                'cognome' => 'hu',
                'sesso' => 'm',
                'dataNascita' => new DateTime('1956-09-30'),
                'codiceComune' => 'Z210',
                'expected' => 'HUXHUX56P30Z210K',
            ],
            [
                'nome' => 'hu',
                'cognome' => 'hu',
                'sesso' => 'f',
                'dataNascita' => new DateTime('1956-09-30'),
                'codiceComune' => 'Z210',
                'expected' => 'HUXHUX56P70Z210O',
            ],
            [
                'nome' => 'luca marco giovanni',
                'cognome' => "d'abate spigna maria",
                'sesso' => 'm',
                'dataNascita' => new DateTime('1968-05-26'),
                'codiceComune' => 'C926',
                'expected' => 'DBTLMR68E26C926B',
            ],
            [
                'nome' => "l'arnalda",
                'cognome' => "d'annunzio",
                'sesso' => 'F',
                'dataNascita' => new DateTime('1983-12-31'),
                'codiceComune' => 'D856',
                'expected' => 'DNNLNL83T71D856L',
            ],
        ];
    }

    public function testCalcoloCodiceFiscale(): void
    {
        $cf = new Calculator();

        foreach ($this->persons as $person) {
            self::assertSame($person['expected'], $cf->calcola($person['nome'], $person['cognome'], $person['sesso'], $person['dataNascita'], $person['codiceComune']));
        }
    }
}
