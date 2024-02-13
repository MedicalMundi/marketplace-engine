<?php declare(strict_types=1);

/*
 * This file is part of the medicalmundi/marketplace-engine
 *
 * @copyright (c) 2023 MedicalMundi
 *
 * This software consists of voluntary contributions made by many individuals
 * {@link https://github.com/medicalmundi/marketplace-engine/graphs/contributors developer} and is licensed under the MIT license.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * @license https://github.com/MedicalMundi/marketplace-engine/blob/main/LICENSE MIT
 */

namespace BffWeb\Tests\Functional\WebRateLimiter;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

#[CoversNothing]
class RateLimiterTest extends WebTestCase
{
    /**
     * @var KernelBrowser|null
     */
    protected $client = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    #[Test]
    public function throttlingIsActiveOnLogin(): void
    {
        self::markTestSkipped('Check throttling on post request with data');
        $this->client->request('GET', '/login');
        $this->client->request('GET', '/login');
        $this->client->request('GET', '/login');
        $this->client->request('GET', '/login');
        $this->client->request('GET', '/login');
        $this->client->request('GET', '/login');

        self::assertEquals(200, (int) $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('Too many failed login attempts, please try again in 1 minute.', (string) $this->client->getResponse()->getContent());
    }

    #[Test]
    public function throttlingIsActiveOnContact(): void
    {
        $this->client->request('GET', '/contact');
        $this->client->request('GET', '/contact');
        $this->client->request('GET', '/contact');

        self::assertEquals(429, (int) $this->client->getResponse()->getStatusCode());
    }
}
