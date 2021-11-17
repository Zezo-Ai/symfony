<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bridge\Doctrine\Tests\SchemaListener;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\SchemaListener\PdoCacheAdapterDoctrineSchemaSubscriber;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
use Symfony\Component\Cache\Adapter\PdoAdapter;

/**
 * @group legacy
 */
class PdoCacheAdapterDoctrineSchemaSubscriberTest extends TestCase
{
    use ExpectDeprecationTrait;

    public function testPostGenerateSchema()
    {
        $schema = new Schema();
        $dbalConnection = $this->createMock(Connection::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->any())
            ->method('getConnection')
            ->willReturn($dbalConnection);

        $event = new GenerateSchemaEventArgs($entityManager, $schema);

        $pdoAdapter = $this->createMock(PdoAdapter::class);
        $pdoAdapter->expects($this->once())
            ->method('configureSchema')
            ->with($event->getSchema(), $event->getEntityManager()->getConnection());

        $this->expectDeprecation('Since symfony/doctrine-bridge 5.4: The "Symfony\Bridge\Doctrine\SchemaListener\PdoCacheAdapterDoctrineSchemaSubscriber" class is deprecated, use "Symfony\Bridge\Doctrine\SchemaListener\DoctrineDbalCacheAdapterSchemaSubscriber" instead.');

        $subscriber = new PdoCacheAdapterDoctrineSchemaSubscriber([$pdoAdapter]);
        $subscriber->postGenerateSchema($event);
    }
}
