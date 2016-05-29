<?php
/*
 * This file is part of the  TraitorBundle, an RunOpenCode project.
 *
 * (c) 2016 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
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


