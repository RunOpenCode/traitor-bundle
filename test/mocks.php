<?php

namespace Test\NamespacePrefix\One {

    use Psr\Log\LoggerAwareTrait;

    class ServiceClass1 {

        use LoggerAwareTrait;

    }
}

namespace Test\NamespacePrefix\Two {

    use Psr\Log\LoggerAwareTrait;

    class ServiceClass2 {

        use LoggerAwareTrait;

    }
}

namespace Test\Deeper\NamespacePrefix\Three {

    use Psr\Log\LoggerAwareTrait;

    class ServiceClass3 {

        use LoggerAwareTrait;

    }
}


