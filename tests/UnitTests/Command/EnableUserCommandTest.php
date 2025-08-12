<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Command;

use App\Command\EnableUserCommand;
use App\Entity\User;
use App\User\UserService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EnableUserCommandTest extends TestCase
{
    use MatchesSnapshots;

    private UserService&MockObject $userServiceMock;
    private InputInterface&MockObject $inputMock;
    private OutputInterface&MockObject $outputMock;

    protected function setUp(): void
    {
        $this->userServiceMock = $this->createMock(UserService::class);
        $this->inputMock       = $this->createMock(InputInterface::class);
        $this->outputMock      = $this->createMock(OutputInterface::class);
    }

    public function testConfigure(): void
    {
        $cmd = $this->getCommand();

        $arguments = $cmd->getDefinition()->getArguments();
        self::assertCount(
            1,
            $arguments,
        );

        $argData = [];

        foreach ($arguments as $argument) {
            $argData[] = [
                'name'        => $argument->getName(),
                'is_required' => $argument->isRequired(),
                'default'     => $argument->getDefault(),
                'description' => $argument->getDescription(),
            ];
        }

        self::assertMatchesJsonSnapshot($argData);

        $synopsis = $cmd->getSynopsis();
        self::assertSame(
            'opencal:user:enable <email>',
            $synopsis,
        );
    }

    public function testExecuteWithInvalidEmail(): void
    {
        $cmd      = $this->getCommand();
        $refClass = new \ReflectionClass($cmd);
        $method   = $refClass->getMethod('execute');

        $this->inputMock
            ->method('getArgument')
            ->willReturn('invalid-email');

        $result = $method->invokeArgs($cmd, [
            $this->inputMock,
            $this->outputMock,
        ]);

        self::assertSame(
            Command::FAILURE,
            $result,
        );
    }

    public function testExecuteUserNotFound(): void
    {
        $this->userServiceMock
            ->method('findOneByEmail')
            ->willReturn(null);

        $cmd      = $this->getCommand();
        $refClass = new \ReflectionClass($cmd);
        $method   = $refClass->getMethod('execute');

        $this->inputMock
            ->method('getArgument')
            ->willReturn('valid-email@unit-test.com');

        $result = $method->invokeArgs($cmd, [
            $this->inputMock,
            $this->outputMock,
        ]);

        self::assertSame(
            Command::FAILURE,
            $result,
        );
    }

    public function testExecuteUserFoundSucceeds(): void
    {
        $userMock = $this->createMock(User::class);

        $this->userServiceMock
            ->method('findOneByEmail')
            ->willReturn($userMock);
        $this->userServiceMock
            ->expects(self::once())
            ->method('enableUser');
        $this->userServiceMock
            ->expects(self::once())
            ->method('saveUser');

        $cmd      = $this->getCommand();
        $refClass = new \ReflectionClass($cmd);
        $method   = $refClass->getMethod('execute');

        $this->inputMock
            ->method('getArgument')
            ->willReturn('valid-email@unit-test.com');

        $result = $method->invokeArgs($cmd, [
            $this->inputMock,
            $this->outputMock,
        ]);

        self::assertSame(
            Command::SUCCESS,
            $result,
        );
    }

    private function getCommand(): EnableUserCommand
    {
        return new EnableUserCommand(
            $this->userServiceMock,
        );
    }
}
