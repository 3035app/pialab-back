<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\Remote\RemoteWebDriver;

/**
 * Inherited Methods.
 *
 * @method void                    wantToTest($text)
 * @method void                    wantTo($text)
 * @method void                    execute($callable)
 * @method void                    expectTo($prediction)
 * @method void                    expect($prediction)
 * @method void                    amGoingTo($argumentation)
 * @method void                    am($role)
 * @method void                    lookForwardTo($achieveValue)
 * @method void                    comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
 */
class WebGuy extends \Codeception\Actor
{
    use _generated\WebGuyActions {
        click as protected clickOriginal;
    }

    /**
     * Define custom actions here.
     */
    private $baseurl = null;

    private $isLoggedIn = false;

    private $user = null;
    private $password = null;

    public function getBaseUrl()
    {
        if (!isset($this->baseurl)) {
            $this->baseurl = '';
            if (getenv('TEST_URL') !== false) {
                $this->baseurl = getenv('TEST_URL');
            }
        }

        return  $this->baseurl;
    }

    public function getUser()
    {
        if (!isset($this->user)) {
            $this->user = 'ci@pialab.io';
            if (getenv('TEST_USER')) {
                $this->user = getenv('TEST_USER');
            }
        }

        return  $this->user;
    }

    public function getPassword()
    {
        if (!isset($this->password)) {
            $this->password = '42';
            if (getenv('TEST_PASSWORD')) {
                $this->password = getenv('TEST_PASSWORD');
            }
        }

        return  $this->password;
    }

    public function login(?string $username = null, ?string $password = null)
    {
        if (!$this->isLoggedIn) {
            $this->wantTo('Access_login');
            $this->amOnPage($this->getBaseUrl() . '/login');
            $this->fillField('_username', $username ?? $this->getUser());
            $this->fillField('_password', $password ?? $this->getPassword());
            $this->click('[type="submit"]');
            $this->waitForElement('.current.username');
            $this->isLoggedIn = true;
        }
    }

    public function logout()
    {
        if ($this->isLoggedIn) {
            $this->wantTo('Access_logout');
            $this->amOnPage($this->getBaseUrl() . '/logout');
            $this->waitForElement('input[name="_username"]');
            $this->isLoggedIn = false;
        }
    }

    public function checkPage($pageWant, $pageUrl, $pageText)
    {
        $this->login();
        $this->wantTo($pageWant);
        $this->amOnPage($this->getBaseUrl() . $pageUrl);
        $this->cantSee('Symfony Exception');
        $this->cantSee('Une erreur est survenue');
        $this->waitForText($pageText, 5);
    }

    public function checkSUIOption($selector)
    {
        return $this->executeJS('$("' . str_replace('"', '\\"', $selector) . '").parent().find(\'label\').click()');
    }

    public function isElementVisible($element): bool
    {
        $value = false;
        $this->executeInSelenium(function (RemoteWebDriver $webDriver) use ($element, &$value) {
            try {
                $element = $webDriver->findElement(WebDriverBy::cssSelector($element));
                $value = $element instanceof RemoteWebElement && $element->isDisplayed();
            } catch (\Exception $e) {
                // Swallow exception silently
            }
        });

        return $value;
    }

    public function click($element)
    {
        // Scroll to element if it is not visible before clicking it
        if (!$this->isElementVisible($element)) {
            // $this->scrollTo($element); // This does not work with Firefox 52 ESR (It works with firefox 60 ESR)
            $this->executeJS('let elem = document.evaluate(arguments[0], document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue; if (elem !== null) elem.scrollIntoView(true);', [$element]);
        }

        return $this->clickOriginal($element);
    }

    public function selectOptionFromSUISelect($selectName, $optionLabel, bool $blur = true)
    {
        $this->click('//select[@name="' . $selectName . '"]/ancestor::div[contains(@class,"ui dropdown")]');
        $this->click('//select[@name="' . $selectName . '"]/ancestor::div[contains(@class,"ui dropdown")]/div[contains(@class, "menu")]/div[contains(@class, "item")][contains(text(), "' . str_replace('"', '\"', $optionLabel) . '")]');
        if ($blur) {
            $this->click('body'); // Trigger blur event after selecting option
        }
    }

    public function dontSeeNavMenuWithHref($href)
    {
        return $this->dontSeeElement('//body/div[contains(@class, "ui menu")]/descendant::a[contains(@href,"' . $href . '")]');
    }
}
