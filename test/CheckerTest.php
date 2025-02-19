<?php

require_once __DIR__ . '/../vendor/autoload.php';

class CheckerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string[]
     */
    protected $codiciFiscaliOk;

    /**
     * @var string[]
     */
    protected $codiciFiscaliKo;

    /**
     * @var string[]
     */
    protected $omocodie;

    public function setUp(): void
    {
        $this->codiciFiscaliOk = [
            "VRDGPP13R10B293P",
            "CHRVRD74S53L219F",
            "VRDGPP13R10B29PL",
        ];

        $this->codiciFiscaliKo = [
            "SLLNDR91C06F205",
            "SXLNDQ67CS8Z210L",
            "XSD91S67CS8Z210L",
        ];

        $this->omocodie = [
            'BNZVCN32S10E57PV',
            'BNZVCNPNSMLERTPX',
            'CCHGNN67R05H1S3I',
        ];
    }

    public function testCorrettezzaFormaleCodiceFiscale(): void
    {
        $checker = new \CodiceFiscale\Checker();

        foreach ($this->codiciFiscaliOk as $cf) {
            $this->assertTrue($checker->isFormallyCorrect($cf));
        }

        foreach ($this->codiciFiscaliKo as $cf) {
            $this->assertFalse($checker->isFormallyCorrect($cf));
        }

        foreach ($this->omocodie as $cf) {
            $this->assertTrue($checker->isFormallyCorrect($cf));
        }
    }
}
