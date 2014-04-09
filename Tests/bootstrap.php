<?php

if (!is_file($autoloadFile = __DIR__.'/../vendor/autoload.php')) {
    throw new \LogicException('Could not find autoload.php in vendor/. Did you run "composer install --dev"?');
}

$loader = require $autoloadFile;
$loader->addPsr4('Bazinga\\OAuthServerBundle\\Tests\\', __DIR__);

if (class_exists('Propel')) {
    set_include_path(__DIR__ . '/../vendor/phing/phing/classes' . PATH_SEPARATOR . get_include_path());

    $class   = new \ReflectionClass('TypehintableBehavior');
    $builder = new \PropelQuickBuilder();
    $builder->getConfig()->setBuildProperty('behavior.typehintable.class', $class->getFileName());
    $builder->setSchema(file_get_contents(__DIR__.'/../Resources/config/propel/schema.xml'));
    $builder->setClassTargets(array('tablemap', 'peer', 'object', 'query', 'peerstub'));
    $builder->build();
}
