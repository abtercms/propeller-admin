<?php

declare(strict_types=1);

namespace AbterPhp\PropellerAdmin\Events\Listeners;

use AbterPhp\Admin\Events\AdminReady;
use AbterPhp\Framework\Assets\AssetManager;
use Opulence\Views\View;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AdminDecoratorTest extends TestCase
{
    public const HEADER = 'foo';
    public const FOOTER = 'bar';

    /** @var AdminDecorator - System Under Test */
    protected $sut;

    /** @var AssetManager|MockObject */
    protected $assetManagerMock;

    public function setUp(): void
    {
        $this->assetManagerMock = $this->getMockBuilder(AssetManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['addCss', 'addJs'])
            ->getMock();

        $this->sut = new AdminDecorator($this->assetManagerMock, static::HEADER, static::FOOTER);

        parent::setUp();
    }

    public function testHandle()
    {
        /** @var View|MockObject $viewMock */
        $viewMock = $this->getMockBuilder(View::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['hasVar', 'getVar', 'setVar'])
            ->getMock();

        $viewMock->expects($this->atLeastOnce())->method('setVar');

        $adminReady = new AdminReady($viewMock);

        $this->assetManagerMock->expects($this->atLeastOnce())->method('addCss');
        $this->assetManagerMock->expects($this->atLeastOnce())->method('addJs');

        $this->sut->handle($adminReady);
    }
}
