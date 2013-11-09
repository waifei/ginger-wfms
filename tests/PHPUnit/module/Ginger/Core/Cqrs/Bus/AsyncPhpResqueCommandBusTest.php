<?php
namespace Ginger\Core\Cqrs\Bus;

use Ginger\Test\Mock\Core\CommandHandler;
use Ginger\Test\Mock\Core\CheckCommandInvocationCommand;
use Ginger\Test\Mock\Core\AsyncPhpResqueCommandBus;
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-11-09 at 21:11:16.
 */
class AsyncPhpResqueCommandBusTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var AsyncPhpResqueCommandBus
     */
    protected $object;
    
    protected $serviceManager;
    
    
    protected $worker;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->serviceManager = \Ginger\Core\Bootstrap::getServiceManager();
        
        $gate = $this->serviceManager->get('cqrs.gate');
        
        $asyncBus = $gate->getBus(\Ginger\Core\Definition::ASYNC_COMMAND_BUS);
        
        $gate->detach($asyncBus);
        
        //Attach mocked async bus to make sure that Ginger\Core is bootstraped with
        //activateTestEnv flag set to true, when the background work invokes the bus
        $mockedAsyncBus = new AsyncPhpResqueCommandBus();
        
        $gate->attach($mockedAsyncBus);
        
        $this->worker = new \Resque_Worker(array(\Ginger\Core\Definition::ASYNC_COMMAND_QUEUE));
        
        CommandHandler::reset();
    }

    /**
     * @covers Ginger\Core\Cqrs\Bus\AsyncPhpResqueCommandBus::invokeCommand
     */
    public function testInvokeCommand()
    {
        $checkPayload = array('check args');
        $checkId = 1;
        $checkTime = 1000000;
        $checkVersion = 1.2;
        
        $command = new CheckCommandInvocationCommand($checkPayload, $checkId, $checkTime, $checkVersion);

        $commandHandler = new CommandHandler();
        
        $this->serviceManager->get('cqrs.gate')
            ->getBus(\Ginger\Core\Definition::ASYNC_COMMAND_BUS)
            ->invokeCommand($command);
        
        $this->worker->work(0);
        
        $this->assertTrue(
            $commandHandler->isCommandInvoked(), 
            'tests/PHPUnit/Ginger/Test/Mock/Core/touch-file.txt is not removed. Maybe the command args did not match?'
        );
    }

}
