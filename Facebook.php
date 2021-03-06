<?php
/**
 * Created by PhpStorm.
 * User: Findwayinsea
 * Date: 2016/9/3
 * Time: 14:57
 */

require __DIR__.'/vendor/autoload.php';

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\WebDriverExpectedCondition;


class Facebook extends PHPUnit_Framework_TestCase {

    /**
     * @var \RemoteWebDriver
     */
    protected $webDriver;
    protected $url = 'https://github.com';

    public function setUp()
    {
        $host = 'http://localhost:4444/wd/hub';
        //$host = 'http://localhost:4444/wd/hub';
        $desired_capabilities = DesiredCapabilities::chrome();
        $desired_capabilities->setCapability('acceptSslCerts', false);
        $this->webDriver = RemoteWebDriver::create($host, $desired_capabilities);
    }

    public function testGitHubHome()
    {
        $this->webDriver->get($this->url);
        // checking that page title contains word 'GitHub'
        $this->assertContains('GitHub', $this->webDriver->getTitle());
    }

    public function testSearch()
    {
        $this->webDriver->get($this->url);
        // find search field by its id
        $search = $this->webDriver->findElement(WebDriverBy::cssSelector('.header-search-input'));
        $search->click();
        $search->sendKeys('microsoft');
        $search->submit();

    }

    public function testLogin()
    {
        $this->webDriver->get($this->url);

        $this->webDriver->findElement(WebDriverBy::cssSelector('a.btn:nth-child(2)'))->click();

        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::titleContains('Sign in')
        );

        $this->webDriver->findElement(WebDriverBy::cssSelector('#login_field'))->sendKeys('github account email');
        $pass = $this->webDriver->findElement(WebDriverBy::cssSelector('#password'));
        $pass->sendKeys('password');
        $pass->submit();

    }


    public function testSearch2()
    {
        $this->webDriver->get($this->url);

        // find search field by its id
        $search = $this->webDriver->findElement(WebDriverBy::cssSelector('.header-search-input'));
        $search->click();
        $search->sendKeys('php-webdriver');
        $search->sendKeys(WebDriverKeys::ENTER);

        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::titleContains('Search')
        );

        $firstResult = $this->webDriver->findElement(WebDriverBy::cssSelector('li.repo-list-item:nth-child(1) > h3:nth-child(2) > a:nth-child(1)'));
        $firstResult->click();

        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::not(WebDriverExpectedCondition::titleContains('Search'))
        );

        // we expect that facebook/php-webdriver was the first result
        $this->assertContains("php-webdriver",$this->webDriver->getTitle());

        // checking current url
        $this->assertEquals(
            'https://github.com/facebook/php-webdriver',
            $this->webDriver->getCurrentURL()
        );
    }

}
?>
