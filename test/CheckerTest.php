<?php

use CodiceFiscale\Checker;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * @internal
 *
 * @coversNothing
 */
class CheckerTest extends TestCase
{
    /**
     * @var string[]
     */
    protected array $codiciFiscaliOk;

    /**
     * @var string[]
     */
    protected array $codiciFiscaliKo;

    /**
     * @var string[]
     */
    protected array $omocodie;

    public function setUp(): void
    {
        $this->codiciFiscaliOk = [
            'VRDGPP13R10B293P',
            'CHRVRD74S53L219F',
            'VRDGPP13R10B29PL',
        ];

        $this->codiciFiscaliKo = [
            'SLLNDR91C06F205',
            'SXLNDQ67CS8Z210L',
            'XSD91S67CS8Z210L',
        ];

        $this->omocodie = [
            'BNZVCN32S10E57PV',
            'BNZVCNPNSMLERTPX',
            'CCHGNN67R05H1S3I',
        ];
    }

    public function testCorrettezzaFormaleCodiceFiscale(): void
    {
        $checker = new Checker();

        foreach ($this->codiciFiscaliOk as $cf) {
            self::assertTrue($checker->isFormallyCorrect($cf));
        }

        foreach ($this->codiciFiscaliKo as $cf) {
            self::assertFalse($checker->isFormallyCorrect($cf));
        }

        foreach ($this->omocodie as $cf) {
            self::assertTrue($checker->isFormallyCorrect($cf));
        }
    }
}
