Traitor bundle
==============

[![Packagist](https://img.shields.io/packagist/v/RunOpenCode/traitor-bundle.svg)](https://packagist.org/packages/runopencode/traitor-bundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/RunOpenCode/traitor-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/RunOpenCode/traitor-bundle/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/RunOpenCode/traitor-bundle/badges/build.png?b=master)](https://scrutinizer-ci.com/g/RunOpenCode/traitor-bundle/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/RunOpenCode/traitor-bundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/RunOpenCode/traitor-bundle/?branch=master)
[![Build Status](https://travis-ci.org/RunOpenCode/traitor-bundle.svg?branch=master)](https://travis-ci.org/RunOpenCode/traitor-bundle)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/3a4f81a7-6227-40c5-89f5-638203d9e7e6/big.png)](https://insight.sensiolabs.com/projects/3a4f81a7-6227-40c5-89f5-638203d9e7e6)

*Symfony service container compiler pass which defines additional
setter injection of services/parameters/values based on used traits in 
service classes.

**Before you start using this bundle** please read following article (if you 
have not already): [http://symfony.com/doc/current/components/dependency_injection/types.html](http://symfony.com/doc/current/components/dependency_injection/types.html), 
it is imperative that you fully understand types of injection because this
bundle promotes bad practice in order to gain higher level of productivity.

**We would like you to read carefully this "readme" file as well and understand
what this bundle does, with all its benefits and drawbacks, before using it, 
especially if you are not experienced developer with proficiency in computer
science.**
 
## How this bundle works?

**In one sentence**: If your class is registered as service in service container
and if that class uses traits like, per example, `\Psr\Log\LoggerAwareTrait`, 
this bundle will redefine your service adding a method call in its definition,
injecting appropriate service, as per example, a `logger`. 
 
So, instead of defining setter injection for your service, it is sufficient
enough to use some kind of provided, or user defined, `*AwareTrait` and this
bundle will provide appropriate injection (if it is configured properly, of 
course).

### Why the hell you would even consider such travesty? 

Consider that you already have defined services in your service container
which requires additional injection of, per example, services that will be
used in your code.
 
Per example: 
 
    class MyService 
    {
        public function __construct() {  }
    }
    
And your `services.yml`:

    services:
        my_service
            class: MyService
        
In order to inject, per example, a `logger` service into your service, 
you would have to modify your class, with, per example, a constructor 
injection:
        
    class MyService 
    {
        protected $logger
    
        public function __construct(\Psr\Log\LoggerInterface $logger) 
        {
            $this->logger = $logger;
        }
    }        

And you would have to modify `services.yml` as well:

    services:
        my_service
            class: MyService
            argumets: [ '@logger' ]
            
In example above, a good practice of service injection is exercised. However,
sometimes, this good practice can not be satisfied within a reasonable amount 
of time and effort. 

#### Traitor bundle in the rescue

Instead of doing as in example stated above, how about to just use some
trait, and some kind of *"magic"* will to the rest
of it? Our previous example would be as easy as doing the following:

    class MyService 
    {
        use \Psr\Log\LoggerAwareTrait;
    
        public function __construct() {  }
    }


And this is just good enough - you just use appropriate trait, and compiler
pass within this bundle will provide your service class with appropriate setter
injection.

#### Motivation for developing this bundle

Best example, which was a motivation to build this kind of bundle, is
GeneratorBundle: [https://github.com/symfony2admingenerator/GeneratorBundle](https://github.com/symfony2admingenerator/GeneratorBundle).

Namely, GeneratorBundle generates all CRUD bits for you, which includes a
form types as well, which are (as per courtesy of authors of this awesome 
library) already properly registered in service container with tag `form.type`, 
and, as good as it is, sometimes can be an issue to inject additional required
services, which happens quite often.

In order to do such injection, you would have to identify the name of service
under which your form type has been registered, and modify that service by 
either overriding its definition via configuration file in, per example, 
`app/config/services.yml` (thanks to configuration cascade) or through compiler
pass.

However, we have figure out that either of that approaches just takes too 
much time when we worked on project in which there were app. 50 form types 
in need of injection of additional services.

So, we needed a solution that will handle this more elegantly, with less effort.

## Why this bundle should be used carefully?

- Setter injection should be used for optional injections only. However, this
solution proposes injection of required services as well via setter injection.
That is not as bad as, per example, Laravel's Facade, but if it is used without
understanding types of injection, it can be dangerous, mostly for developer
who develops such practice without understanding the difference.
- Symfony proposes certain convention of defining services for sake of 
the productivity and maintainability of the project. This bundle breaks
that convention, and if developers are not familiar with usage of this 
bundle, they could have difficulty to understand how certain services
got their required dependencies.
- Compiler pass at first run can have impact on performances, considering
that this bundle analyses all services and all traits used by their classes,
as well as inheritance map of classes, as well as related usage of traits.
However, trough configuration, you can narrow down a scope of search and 
increase performances of compiler pass.

Having in mind all dangers stated above, this bundle can help you 
a lot in certain use cases, giving you a flexibility and productivity at the
cost of good coding practice, and you should use this bundle in such 
occurrences.

Please note that this bundle **should not be used for redistributable Symfony
bundles**, for obvious reasons.

## Configuration

Require this bundle via composer, `composer require runopencode/traitor-bundle`
and register it in your `AppKernel.php`:

    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = [
            
                ...
                
                new RunOpenCode\Bundle\Traitor\TraitorBundle()
                
                ...
                
            ];
            return $bundles;
        }
    
    }

and configure it, example of full configuration is given bellow:

    run_open_code_traitor:
        use_common_traits: false             
        inject:
            'Full\Qualified\Trait\Namespace': [ 'setterMethodName', [ '@service_name', 'or_parameter_value' ]]
        filters:
            tags: [ 'form.type', 'other_tag' ]
            namespaces: [ '\Namespace\Prefix1', 'Namespace\Prefix2' ]
        exclude:
            services: [ 'my.service.to_exclude' ]
            classes: [ '\Exclude\Services\ThatUsesThisClass', '\Or\Uses\ThisClass' ]
            namespaces: [ '\Exclude\AllServices\WithClasses\WithinThisNamespace\' ]
            


- `use_common_traits`: Optional. This bundle provides you with common traits
within `\RunOpenCode\Bundle\Traitor\Traits` namespace to boost up
your productivity. If you are using them, set this parameter to `true`,
it will add those traits to injection map. Full list of traits which 
you can use *out-of-the-box* are given bellow.
- `inject`: Optional. Associative array of traits which should be checked for usage,
and if they are used in service class, trait map defines setter injection
in same way as Symfony `calls` definition does, an array, with first parameter
that defines a method to call, and second parameter as array of arguments to
pass to that method call.
- `filters`: Optional. In order not to scan all service classes, you can
narrow down the subset to certain namespaces and/or service tags. Determining 
which class uses which trait can be expensive, this bundle checks both inheritance
as well as related traits usage (e.g. if trait uses trait).
- `exclude`: Optional. You can exclude certain services, service classes and 
namespaces to be even considered for this kind of injection.

### Provided common traits

As previously mentioned, this bundle provides you with common traits
which you may or may not use, however, if you are using them, make sure
that in your configuration set `use_common_traits` to true, and `inject`
map will be appended with following definitions as well:

    'Symfony\Component\DependencyInjection\ContainerAwareTrait': ['setContainer', ['@service_container']]
    'Psr\Log\LoggerAwareTrait': ['setLogger', ['@logger']]
    'RunOpenCode\Bundle\Traitor\Traits\DoctrineAwareTrait': ['setDoctrine', ['@doctrine']]
    'RunOpenCode\Bundle\Traitor\Traits\EventDispatcherAwareTrait': ['setEventDispatcher', ['@event_dispatcher']]
    'RunOpenCode\Bundle\Traitor\Traits\FilesystemAwareTrait': ['setFilesystem', ['@filesystem']]
    'RunOpenCode\Bundle\Traitor\Traits\KernelAwareTrait': ['setKernel', ['@kernel']]
    'RunOpenCode\Bundle\Traitor\Traits\MailerAwareInterface': ['setMailer', ['@mailer']]
    'RunOpenCode\Bundle\Traitor\Traits\PropertyAccessorAwareTrait': ['setPropertyAccessor', ['@property_accessor']]
    'RunOpenCode\Bundle\Traitor\Traits\RequestStackAwareTrait': ['setRequestStack', ['@request_stack']]
    'RunOpenCode\Bundle\Traitor\Traits\RouterAwareTrait': ['setRouter', ['@router']]
    'RunOpenCode\Bundle\Traitor\Traits\AuthorizationCheckerAwareTrait': ['setAuthorizationChecker', ['@security.authorization_checker']]
    'RunOpenCode\Bundle\Traitor\Traits\SessionAwareTrait': ['setSession', ['@session']]
    'RunOpenCode\Bundle\Traitor\Traits\TwigAwareTrait': ['setTwig', ['@twig']]
    'RunOpenCode\Bundle\Traitor\Traits\TranslatorAwareTrait': ['setTranslator', ['@translator']]
    'RunOpenCode\Bundle\Traitor\Traits\ValidatorAwareTrait': ['setValidator', ['@validator']]
    'RunOpenCode\Bundle\Traitor\Traits\TokenStorageAwareTrait': ['setTokenStorage', ['@security.token_storage']]
    
In general, common traits will help you to boost your productivity when 
injecting following services:

- `service_container`
- `logger`
- `doctrine`
- `event_dispatcher`
- `filesystem`
- `kernel`
- `mailer`
- `property_accessor`
- `request_stack`
- `router`
- `security.authorization_checker`
- `session`
- `twig`
- `translator`
- `validator`
- `security.token_storage`
