<?php

declare(strict_types=1);

namespace AbterPhp\PropellerAdmin\Events\Listeners;

use AbterPhp\Admin\Events\AdminReady;
use AbterPhp\Framework\Assets\AssetManager;
use Opulence\Views\View;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LoginDecoratorTest extends TestCase
{
    /** @var LoginDecorator - System Under Test */
    protected $sut;

    /** @var AssetManager|MockObject */
    protected $assetManagerMock;

    const HEADER = 'foo';
    const FOOTER = 'bar';

    public function setUp()
    {
        $this->assetManagerMock = $this->getMockBuilder(AssetManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['addCss', 'addJs'])
            ->getMock();

        $this->sut = new LoginDecorator($this->assetManagerMock, static::HEADER, static::FOOTER);

        parent::setUp();
    }

    public function testHandle()
    {
        /** @var View|MockObject $viewMock */
        $viewMock = $this->getMockBuilder(View::class)
            ->disableOriginalConstructor()
            ->setMethods(['hasVar', 'getVar', 'setVar'])
            ->getMock();

        $viewMock->expects($this->atLeastOnce())->method('setVar');

        $adminReady = new AdminReady($viewMock);

        $this->assetManagerMock->expects($this->atLeastOnce())->method('addCss');
        $this->assetManagerMock->expects($this->atLeastOnce())->method('addJs');

        $this->sut->handle($adminReady);
    }
}
