<?php

use App\Libraries\Alerts;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Services;

/**
 * @internal
 */
final class AlertsTest extends CIUnitTestCase
{
    public function testInstance()
    {
        $alerts = new Alerts(Services::session());
        $this->assertInstanceOf(Alerts::class, $alerts);
    }

    public function testSetGetClear()
    {
        $alerts = new Alerts(Services::session());
        $alerts
            ->set('success', 'message1')
            ->set('error', 'message2');

        $this->assertSame(['message1'], $alerts->get('success'));
        $this->assertSame(['success' => ['message1'], 'error' => ['message2']], $alerts->get());

        $alerts->clear('error');
        $this->assertSame([], $alerts->get('error'));
        $this->assertSame(['success' => ['message1']], $alerts->get());

        $alerts->clear();
        $this->assertSame([], $alerts->get());
    }

    public function testInline()
    {
        $alerts = new Alerts(Services::session());
        $alerts->set('success', 'message1');
        $this->assertStringContainsString('hx-swap-oob="beforeend:#alerts-container"', $alerts->inline());
        $this->assertStringContainsString('alert-success', $alerts->inline());
        $this->assertStringContainsString('message1', $alerts->inline());
    }

    public function testInlineEmpty()
    {
        $alerts = new Alerts(Services::session());
        $this->assertSame('', $alerts->inline());
    }

    //    public function testSession()
    //    {
    //        $alerts = new Alerts(Services::session());
    //        $alerts->set('success', 'message1')->session();
    //        $this->assertStringContainsString('alert-success', view('_alerts/alerts'));
    //        $this->assertStringContainsString('message1', view('_alerts/alerts'));
    //    }

    public function testContainer()
    {
        $alerts = new Alerts(Services::session());
        $this->assertStringContainsString('id="alerts-container"', $alerts->container());
    }
}
