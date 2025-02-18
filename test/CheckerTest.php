<?php
require_once __DIR__ . '/../vendor/autoload.php';

class CheckerTest extends \PHPUnit\Framework\TestCase
{
    protected $codiciFiscaliOk;
    protected $codiciFiscaliKo;

    public function setUp(): void
    {
        $this->codiciFiscaliOk = [
            "VRDGPP13R10B293P",
            "CHRVRD74S53L219F",
            "VRDGPP13R10B29PL"
        ];

        $this->codiciFiscaliKo = [
            "SLLNDR91C06F205",
            "SXLNDQ67CS8Z210L",
            "XSD91S67CS8Z210L"
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
    }
}
