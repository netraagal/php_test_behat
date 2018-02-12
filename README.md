# Osiris

## Introduction
Osiris verifies that the product satisfies the expectations of the user (conformity with requirements and specifications).
<br />
The product is verified as a whole, with realistic use scenarios.
> "Behat is an open source Behavior Driven Development framework for PHP 5.3+. 
What’s behavior driven development, you ask? 
It’s a way to develop software through a constant communication with stakeholders in form of examples."
[Behat documentation]

## Setup
1. The package comes with a `composer.json` file. Just launch `composer.phar install` and it will download and setup all dependencies.
2. Create config file in `config/config.php` from `config/config.php.dist`
3. You can test your installation Behat with: `php bin/behat -V`

#### Note
You can configure the base_url for goutte mink extension, for all feature tests.
At start of each feature file, add an annotation `[@dev.metiwebstore.b2b or @dev.metiwebstore.b2c or etc...]`.
And configure the url for each tag in config file.
In this way, the host definition is placed at one site.

#### Optional
You can configue your hosts file like that:
```
192.168.0.1	dev.metiwebstore.b2b
192.168.0.2	dev.metiwebstore.b2c
```
Otherwise you must modify the host in feature file.

## Usage

#### 1/ Write features
Write yours use scenarios in Gherkin Syntax.
Complete or create new feature files in `features folder`, the files are automatically load.
Exemple:
```YAML
Scenario: Some description of the scenario
  Given some context
  When some event
  Then outcome
```
<br />
More info: [Writing features]

Notes: Each features which use goutte extension, you should put the annotation at start file. The annotation allow to choose the host with the help of config file.


#### 2/ Defining Steps
How does Behat know what to do ?
<br />
You write PHP code inside your context class and tell Behat that this code represents a specific scenario step (via an annotation with a pattern)

#### 2/ Test
For run your test, just:
    `php bin/behat`
If you want run specific feature add this argument: `--name "[element of feature]"`
### Libraries
A class named 'LegacyManager.php', give you some help for query SQL statement and call stock procedure.
 - `function query(string $sql, array $arguments = array())`
 - `function callStoreProcedureInBO(string $name, array $arguments = array())`
<br />
Warning! Pay attention to the order of your variables.
<br />
A class named 'SshExtension', allow you to run console command for host defined in config file
 - `run(string $command)`


[Behat documentation]: <http://docs.behat.org/en/latest/>
[Writing features]: <http://behat.org/en/latest/user_guide/gherkin.html>