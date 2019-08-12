<?php

declare(strict_types=1);

namespace AbterPhp\PropellerAdmin\Events\Listeners;

use AbterPhp\Framework\Events\NavigationReady;
use AbterPhp\Framework\Navigation\Navigation;
use AbterPhp\PropellerAdmin\Decorator\General as GeneralDecorator;
use AbterPhp\PropellerAdmin\Decorator\Navigation\Navbar as NavbarDecorator;
use AbterPhp\PropellerAdmin\Decorator\Navigation\Primary as PrimaryDecorator;
use AbterPhp\PropellerAdmin\Events\Listeners\NavigationBuilder as Listener;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NavigationBuilderTest extends TestCase
{
    /** @var Listener - System Under Test */
    protected $sut;

    /** @var PrimaryDecorator|MockObject */
    protected $primaryDecoratorMock;

    /** @var NavbarDecorator|MockObject */
    protected $navbarDecoratorMock;

    /** @var GeneralDecorator|MockObject */
    protected $generalDecoratorMock;

    public function setUp(): void
    {
        $this->primaryDecoratorMock = $this->getMockBuilder(PrimaryDecorator::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['init', 'decorate'])
            ->getMock();

        $this->navbarDecoratorMock = $this->getMockBuilder(NavbarDecorator::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['init', 'decorate'])
            ->getMock();

        $this->generalDecoratorMock = $this->getMockBuilder(GeneralDecorator::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['init', 'decorate'])
            ->getMock();

        $this->primaryDecoratorMock->expects($this->any())->method('init')->willReturnSelf();
        $this->navbarDecoratorMock->expects($this->any())->method('init')->willReturnSelf();
        $this->generalDecoratorMock->expects($this->any())->method('init')->willReturnSelf();

        $this->sut = new Listener($this->primaryDecoratorMock, $this->navbarDecoratorMock, $this->generalDecoratorMock);

        parent::setUp();
    }

    public function testHandleCallsGeneralDecorator()
    {
        /** @var Navigation|MockObject $navigationMock */
        $navigationMock = $this->getMockBuilder(Navigation::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getExtendedDescendantNodes', 'hasIntent'])
            ->getMock();

        $navigationMock->expects($this->any())->method('getExtendedDescendantNodes')->willReturn([]);
        $navigationMock
            ->expects($this->at(1))
            ->method('hasIntent')
            ->with(Navigation::INTENT_PRIMARY)
            ->willReturn(false);
        $navigationMock
            ->expects($this->at(2))
            ->method('hasIntent')
            ->with(Navigation::INTENT_NAVBAR)
            ->willReturn(false);

        $navigationReady = new NavigationReady($navigationMock);

        $this->generalDecoratorMock->expects($this->atLeastOnce())->method('decorate');
        $this->primaryDecoratorMock->expects($this->never())->method('decorate');
        $this->navbarDecoratorMock->expects($this->never())->method('decorate');

        $this->sut->handle($navigationReady);
    }

    public function testHandleCallsPrimaryDecoratorOnPrimary()
    {
        /** @var Navigation|MockObject $navigationMock */
        $navigationMock = $this->getMockBuilder(Navigation::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getExtendedDescendantNodes', 'hasIntent'])
            ->getMock();

        $navigationMock
            ->expects($this->any())
            ->method('getExtendedDescendantNodes')
            ->willReturn([]);
        $navigationMock
            ->expects($this->at(1))
            ->method('hasIntent')
            ->with(Navigation::INTENT_PRIMARY)
            ->willReturn(true);

        $navigationReady = new NavigationReady($navigationMock);

        $this->generalDecoratorMock->expects($this->atLeastOnce())->method('decorate');
        $this->primaryDecoratorMock->expects($this->atLeastOnce())->method('decorate');
        $this->navbarDecoratorMock->expects($this->never())->method('decorate');

        $this->sut->handle($navigationReady);
    }

    public function testHandleCallsNavbarDecoratorOnNavbar()
    {
        /** @var Navigation|MockObject $navigationMock */
        $navigationMock = $this->getMockBuilder(Navigation::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getExtendedDescendantNodes', 'hasIntent'])
            ->getMock();

        $navigationMock->expects($this->any())->method('getExtendedDescendantNodes')->willReturn([]);
        $navigationMock
            ->expects($this->at(1))
            ->method('hasIntent')
            ->with(Navigation::INTENT_PRIMARY)
            ->willReturn(false);
        $navigationMock
            ->expects($this->at(2))
            ->method('hasIntent')
            ->with(Navigation::INTENT_NAVBAR)
            ->willReturn(true);

        $navigationReady = new NavigationReady($navigationMock);

        $this->generalDecoratorMock->expects($this->atLeastOnce())->method('decorate');
        $this->primaryDecoratorMock->expects($this->never())->method('decorate');
        $this->navbarDecoratorMock->expects($this->atLeastOnce())->method('decorate');

        $this->sut->handle($navigationReady);
    }
}
