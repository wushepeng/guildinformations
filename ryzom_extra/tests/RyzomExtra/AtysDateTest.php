<?php

namespace RyzomExtra;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-09-19 at 12:21:11.
 */
class AtysDateTest extends \PHPUnit_Framework_TestCase
{
    protected $shardName = 'atys';
    protected $shortShardName = 'atys';
    // todo: 64bit numbers here, might break on 32bit
    protected $shardTick = 279979598;
    protected $shardSync = 1363958744;
    //
    protected $dateString = 'Prima, Mystia 01, 2nd AC 2572';
    protected $dateYear = 2572;
    protected $dateCycle = 2;
    protected $dateSeason = 4;
    protected $dateMonth = 11;
    protected $dateWeek = 1;
	protected $dateDay = 1;
    //
    protected $seasonName = 'Winter';
    protected $monthName = 'Mystia';
    protected $dayName = 'Prima';
    //
    protected $timeHour = 0;
    protected $timeMinutes = 13;
	protected $timeStringHour = '00h';
	protected $timeStringHourMin = '00:13';

    /**
     * @var AtysDateTime
     */
    protected $atysDate;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        // set mock function return values
        //time($this->shardSync);
        //ryzom_time_tick($this->shortShardName, $this->shardTick);

        // initialize
        $this->atysDate = new AtysDateTime('en');
        $this->atysDate->setGameCycle($this->shardTick);
    }

    /**
     * @covers \RyzomExtra\AtysDateTime::setLanguage
     */
    public function testSetLanguage()
    {
        $this->assertEquals('Winter', $this->atysDate->getSeasonName());

        $this->atysDate->setLanguage('fr');
        $this->assertEquals('en Hiver', $this->atysDate->getSeasonName());
    }

    /**
     * @covers \RyzomExtra\AtysDateTime::setGameCycle
     */
    public function testSetTick()
    {
        $this->atysDate->setGameCycle(1234);
        $this->assertEquals(1234, $this->atysDate->getGameCycle());
    }

    /**
     * @covers       \RyzomExtra\AtysDateTime::formatDate
     * @dataProvider fixtureFormatDate
     */
    public function testFormatDate($showHour, $showMin, $expected)
    {
        $this->assertEquals($expected, $this->atysDate->formatDate($showHour, $showMin));
    }

    /**
     * @return array
     */
    public function fixtureFormatDate()
    {
        return array(
            array(true, true, sprintf('%02d:%02d - %s', $this->timeHour, $this->timeMinutes, $this->dateString)),
            array(true, false, sprintf('%02dh - %s', $this->timeHour, $this->dateString)),
            array(false, true, $this->dateString),
            array(false, false, $this->dateString),
        );
    }

    /**
     * @covers \RyzomExtra\AtysDateTime::getYear
     */
    public function testGetYear()
    {
        $this->assertEquals($this->dateYear, $this->atysDate->getYear());
    }

    /**
     * @covers \RyzomExtra\AtysDateTime::getCycle
     */
    public function testGetCycle()
    {
        $this->assertEquals($this->dateCycle, $this->atysDate->getCycle());
    }

    /**
     * @covers \RyzomExtra\AtysDateTime::getSeason
     */
    public function testGetSeason()
    {
        $this->assertEquals($this->dateSeason, $this->atysDate->getSeason());
        $this->assertEquals($this->seasonName, $this->atysDate->getSeasonName());
    }

    /**
     * @covers \RyzomExtra\AtysDateTime::getMonth
     */
    public function testGetMonth()
    {
        $this->assertEquals($this->dateMonth, $this->atysDate->getMonth());
        $this->assertEquals($this->monthName, $this->atysDate->getMonthName());
    }

    /**
     * @covers \RyzomExtra\AtysDateTime::getWeek
     */
    public function testGetWeek()
    {
        $this->assertEquals($this->dateWeek, $this->atysDate->getWeek());
    }

    /**
     * @covers \RyzomExtra\AtysDateTime::getDate
     */
    public function testGetDate()
    {
        $this->assertEquals($this->dateDay, $this->atysDate->getDate());
    }

    /**
     * @covers \RyzomExtra\AtysDateTime::getDay
     */
    public function testGetDay()
    {
        $this->assertEquals($this->dateDay, $this->atysDate->getDay());
        $this->assertEquals($this->dayName, $this->atysDate->getDayName());
    }

    /**
     * @covers \RyzomExtra\AtysDateTime::getHours
     */
    public function testGetHours()
    {
        $this->assertEquals($this->timeHour, $this->atysDate->getHours());
    }

    /**
     * @covers \RyzomExtra\AtysDateTime::getMinutes
     */
    public function testGetMinutes()
    {
        $this->assertEquals($this->timeMinutes, $this->atysDate->getMinutes());
    }

    /**
     * @covers \RyzomExtra\AtysDateTime::toDateString
     */
    public function testToDateString()
    {
        $this->assertEquals($this->dateString, $this->atysDate->toDateString());
    }

    /**
     * @covers \RyzomExtra\AtysDateTime
     */
    public function testMagicToString()
    {
        $this->assertEquals($this->dateString, (string) $this->atysDate);
    }


    /**
     * @covers \RyzomExtra\AtysDateTime::toTimeString
     */
    public function testToTimeString()
    {
        $this->assertEquals($this->timeStringHour, $this->atysDate->toTimeString(false));
        $this->assertEquals($this->timeStringHourMin, $this->atysDate->toTimeString(true));
    }

    /**
     * @covers \RyzomExtra\AtysDateTime::parse
     */
    public function testParse()
    {
        $this->setExpectedException('\RuntimeException', 'Not implemented');
        $this->atysDate->parse($this->dateString);
    }

    /**
     * @dataProvider seasonMonthNameDataProvider
     */
    public function testGetSeasonMonthName($season, $month, $expected)
    {
        $got = $this->atysDate->getSeasonMonthName($season, $month);
        $this->assertEquals($expected, $got);
    }

    /**
     * @return array
     */
    public function seasonMonthNameDataProvider()
    {
        return array(
            // Spring
            array(1, 1, 'Winderly'),
            array(1, 2, 'Germinally'),
            array(1, 3, 'Folially'),
            // Summer
            array(2, 1, 'Floris'),
            array(2, 2, 'Medis'),
            array(2, 3, 'Thermis'),
            // Autumn
            array(3, 1, 'Harvestor'),
            array(3, 2, 'Frutor'),
            array(3, 3, 'Fallenor'),
            // Winter
            array(4, 1, 'Pluvia'),
            array(4, 2, 'Mystia'),
            array(4, 3, 'Nivia'),
        );
    }
}
