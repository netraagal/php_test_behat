<?php
/**
 * (c) 2017 Meti Distribution
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author madubois <madubois@meti.fr>
 */

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeFeatureScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Mink\Element\NodeElement;
use Behat\MinkExtension\Context\MinkContext;
// use Meti\Osiris\Server\SshExtension;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext implements SnippetAcceptingContext
{
    protected $config;

    /**
     * @var ArticleManager
     */
    protected $article_manager;

    /**
     * @var BrandManager
     */
    protected $brand_manager;

    /**
     * @var SshExtension
     */
    protected $ssh;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $filename = dirname(dirname(__DIR__))."/config/config.php";

        if (!is_readable($filename)) {

            throw new RuntimeException(
                sprintf(
                    "Configuration file '%s' not found or not readable.",
                    $filename
                )
            );
        }

        $this->config          = include $filename;
        // $this->ssh             = new SshExtension($this->config);
    }

    /**
     * prepareForTheFeature
     *
     * This method is execute before anyone else method in this Context.
     * Be careful, you haven't access to context himself.
     * More over, the behat parameters are not yet instantiated.
     * For instance, you can make action on Database (clean, trunk, …)
     *
     * @BeforeFeature
     *
     * @param   BeforeFeatureScope   $scope
     */
    public static function prepareForTheFeature(BeforeFeatureScope $scope)
    {
        // What I want
    }

    /**
     * before
     *
     * This method is execute before all the scenarios that contain this context.
     * In my case, I set base_url mink goutte for each feature.
     * base_url is set from annotation(tag) at start of each feature file
     *
     * @BeforeScenario
     *
     * @param   BeforeScenarioScope   $scope
     */
    public function before(BeforeScenarioScope $scope)
    {
        if (!$scope->getFeature()->hasTags())
            return;

        $tags = $scope->getFeature()->getTags();
        foreach ($tags as $tag) {
            $this->setMinkParameter("base_url", $this->config[$tag]);
        }

        return $this;
    }

    /**
     * iRunACommand
     *
     * @Given /^I run "([^"]*)" command$/
     *
     * @param   string      $command
     * @return  FeatureContext
     */
    public function iRunACommand(string $command): self
    {
        $this->ssh->run($command);

        return $this;
    }

    /**
     * theElementWithXpathShouldContain
     *
     * Check that an element found with an xpath contain a value.
     *
     * @Then the element with xpath :xpath should contain :value
     *
     * @param   string      $xpath
     * @param   string      $value
     * @return  FeatureContext
     * @throws  Exception
     */
    public function theElementWithXpathShouldContain(string $xpath, string $value): self
    {
        eval(\Psy\sh());
        echo "dump";
        $element = $this->getXpathElement($xpath);

        if (!(strpos($element->getText(), $value) !== false)) {

            throw new Exception(
                sprintf(
                    "Error on test of xpath %s element value.
                    \r\nExpected value must be '%s',but '%s' given",
                    $xpath,
                    $value,
                    $element->getText()
                )
            );
        }

        return $this;
    }

    /**
     * ifillInXpathWith
     *
     * @When I fill in xpath :xpath with :value
     *
     * @param   string  $xpath
     * @param   string  $value
     * @return  FeatureContext
     */
    public function ifillInXpathWith(string $xpath, string $value): self
    {
        require '/home/devteam/bin/psysh'; eval(\Psy\sh());
        $element = $this->getXpathElement($xpath);
        require '/home/devteam/bin/psysh'; eval(\Psy\sh());
        $element->setValue($value);

        return $this;
    }

    /**
     * theElementWithXpathShouldContainXNumberofUnderElement
     *
     * @Then the element with xpath :xpath contain at least one of subelement with :xpathsubelement
     *
     * @param   string  $xpath
     * @param   string  $xpathsubelement
     * @return  FeatureContext
     */
    public function theElementWithXpathContainAtLeastOneXNumberofsubElement(string $xpath, string $xpathsubelement): self
    {
        $element = $this->getXpathElement($xpath);

        if (count($element->findAll(
                'xpath',
                $xpathsubelement
            )) > 0
        ) {

            return $this;
        } else {

            throw new Exception(
                sprintf("Error on test, wrong xpath or no subElement founded.")
            );
        }
    }

    /**
     * theElementWithSrcReturnResponseCodeGiven
     *
     * @Then the src of element with xpath :xpath return Response :code
     *
     * @param   string  $xpath
     * @param   int     $code
     * @return  FeatureContext
     */
    public function theElementWithSrcReturnResponseCodeGiven(string $xpath, int $code): self
    {
        $element = $this->getXpathElement($xpath);
        $src_image = $element->getAttribute('src');
        $base_url = $this->getMinkParameter('base_url');
        $complete_url = $base_url . $src_image;

        $this->visit($complete_url);
        $this->assertResponseStatus($code);

        return $this;
    }

    /**
     * thisXpathExistAndIsValid
     *
     * @Then this xpath :xpath exist and is valid
     *
     * @param   string $xpath
     * @return  \self
     */
    public function thisXpathExistAndIsValid(string $xpath): self
    {
        $this->getXpathElement($xpath);

        return $this;
    }

    /**
     * valueOfInputShouldBe
     *
     * Compare the value of input of xpath with given value
     *
     * @Then the value of input with xpath :xpath should be :value
     *
     * @param   string $xpath
     * @param   string $value
     * @return  \self
     * @throws  Exception
     */
    public function valueOfInputShouldBe(string $xpath, string $value): self
    {
        $element = $this->getXpathElement($xpath);
        $element_value = $element->getValue();
        if($element_value !== $value) {

            throw new Exception(
                sprintf("\"%s\" is not equal with given value: %s",
                    $element_value,
                    $value)
            );
        }

        return $this;
    }

    /**
     * getXpathElement
     *
     * Get the element from given xpath. Verify if element exist and is valid.
     *
     * @param   string  $xpath
     * @return  NodeElement
     * @throws  InvalidArgumentException
     * @throws  Exception
     */
    protected function getXpathElement(string $xpath): NodeElement
    {
        $session = $this->getSession();
        $element = $session->getPage()->find(
            'xpath',
            $session->getSelectorsHandler()->selectorToXpath('xpath', $xpath)
        );

        if ($element === null) {

            throw new InvalidArgumentException(
                sprintf(
                    'Could not evaluate XPath: "%s"',
                    $xpath
                )
            );
        }
        if (!$element->isValid()) {

            throw new Exception(
                sprintf(
                    'Element from xpath("%s") isn\'t valid',
                    $xpath
                )
            );
        }

        return $element;
    }
}
