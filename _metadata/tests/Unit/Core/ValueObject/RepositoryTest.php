<?php declare(strict_types=1);

/**
 * This file is part of the medicalmundi/marketplace-engine
 *
 * @copyright (c) 2024 MedicalMundi
 *
 * This software consists of voluntary contributions made by many individuals
 * {@link https://github.com/medicalmundi/marketplace-engine/graphs/contributors developer} and is licensed under the MIT license.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * @license https://github.com/MedicalMundi/marketplace-engine/blob/main/LICENSE MIT
 */

namespace MetadataTests\Unit\Core\ValueObject;

use Metadata\Core\ValueObject\Repository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Repository::class)]
class RepositoryTest extends TestCase
{
    public function testItShouldCreateFromRepositoryUrl(): void
    {
        $repository = Repository::createFromRepositoryUrl('https://github.com/username/repository');

        self::assertEquals('github.com', $repository->getSource());
        self::assertEquals('username', $repository->getUsername());
        self::assertEquals('repository', $repository->getName());
    }

    public function testItThrowExceptionIfUrlNotValid(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Impossible to fetch package by "https://google.it" repository.');

        Repository::createFromRepositoryUrl('https://google.it');
    }

    public function testItShouldCreateRepository(): void
    {
        $repository = Repository::create('github.com', 'username', 'repository');

        self::assertEquals('github.com', $repository->getSource());
        self::assertEquals('username', $repository->getUsername());
        self::assertEquals('repository', $repository->getName());
    }

    public function testItDetectGitHubAsSourceProvider(): void
    {
        $repository = Repository::createFromRepositoryUrl('https://github.com/username/repository');

        self::assertTrue($repository->isGitHub());
    }

    public function testGitHubShouldNotdetectedAsSourceProvider(): void
    {
        $repository = Repository::createFromRepositoryUrl('https://fake-provider.com/username/repository');

        self::assertFalse($repository->isGitHub());
    }

    public function testItSupportGitHubAsSourceProvider(): void
    {
        $repository = Repository::createFromRepositoryUrl('https://github.com/username/repository');

        self::assertTrue($repository->isSupported());
    }

    public function testItDetectBitbucketAsSourceProvider(): void
    {
        $repository = Repository::createFromRepositoryUrl('https://bitbucket.org/username/repository');

        self::assertTrue($repository->isBitbucket());
    }

    public function testBitbucketShouldNotdetectedAsSourceProvider(): void
    {
        $repository = Repository::createFromRepositoryUrl('https://fake-provider.com/username/repository');

        self::assertFalse($repository->isBitbucket());
    }

    public function testItSupportBitbucketAsSourceProvider(): void
    {
        $repository = Repository::createFromRepositoryUrl('https://bitbucket.org/username/repository');

        self::assertTrue($repository->isSupported());
    }

    /**
     * @dataProvider unsupportedRepositorySourceProvider
     */
    public function testItDetectUnsupportedSourceProvider(string $sourceProviderUrl): void
    {
        $repository = Repository::createFromRepositoryUrl($sourceProviderUrl);

        self::assertFalse($repository->isSupported());
    }

    public function testItDetectGitLabAsSourceProvider(): void
    {
        $repository = Repository::createFromRepositoryUrl('https://gitlab.com/username/repository');

        self::assertTrue($repository->isGitLab());
    }

    /**
     * @return \Generator<array<string>>
     */
    public static function unsupportedRepositorySourceProvider(): \Generator
    {
        yield ['https://www.gitlab.com/username/repository'];
        yield ['https://www.my-self-hosted-git.com/acme/foo'];
        yield ['https://www.fake-provider.com/foo/bar'];
    }
}
