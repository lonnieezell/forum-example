<?php

namespace Tests\Controllers;

use App\Models\NotificationMutedModel;
use App\Models\NotificationSettingModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use InvalidArgumentException;
use Tests\Support\Database\Seeds\TestDataSeeder;

/**
 * @internal
 */
final class ActionControllerTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace;
    protected $seed = TestDataSeeder::class;

    /**
     * Convert full URL to path.
     */
    private function convertToPath(string $url): string
    {
        return str_replace(site_url(), '', $url);
    }

    public function testMuteNotification()
    {
        model(NotificationSettingModel::class)->update(1, ['email_thread' => 1]);

        $url      = $this->convertToPath(signedurl()->urlTo('action-thread-notifications', 1, 1, 'mute'));
        $response = $this->get($url);

        $response->assertOK();
        $response->assertRedirect();
        $this->seeInDatabase('notification_muted', ['user_id' => '1', 'thread_id' => 1]);
    }

    public function testUnmuteNotification()
    {
        model(NotificationSettingModel::class)->update(1, ['email_thread' => 1]);
        model(NotificationMutedModel::class)->insert(1, 1);

        $url      = $this->convertToPath(signedurl()->urlTo('action-thread-notifications', 1, 1, 'unmute'));
        $response = $this->get($url);

        $response->assertOK();
        $response->assertRedirect();
        $this->dontSeeInDatabase('notification_muted', ['user_id' => '1', 'thread_id' => 1]);
    }

    public function testMuteNotificationWithHtmxRequest()
    {
        model(NotificationSettingModel::class)->update(1, ['email_thread' => 1]);

        $url      = $this->convertToPath(signedurl()->urlTo('action-thread-notifications', 1, 1, 'mute'));
        $response = $this->withHeaders([
            'HX-Request' => 'true',
        ])->get($url);

        $response->assertOK();
        $response->assertSee('Unmute notification');
        $this->seeInDatabase('notification_muted', ['user_id' => '1', 'thread_id' => 1]);
    }

    public function testUnmuteNotificationWithHtmxRequest()
    {
        model(NotificationSettingModel::class)->update(1, ['email_thread' => 1]);
        model(NotificationMutedModel::class)->insert(1, 1);

        $url      = $this->convertToPath(signedurl()->urlTo('action-thread-notifications', 1, 1, 'unmute'));
        $response = $this->withHeaders([
            'HX-Request' => 'true',
        ])->get($url);

        $response->assertOK();
        $response->assertSee('Mute notification');
        $this->dontSeeInDatabase('notification_muted', ['user_id' => '1', 'thread_id' => 1]);
    }

    public function testIncorrectActionForNotification()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The action field must be one of: mute,unmute.');

        model(NotificationSettingModel::class)->update(1, ['email_thread' => 1]);

        $url = $this->convertToPath(signedurl()->urlTo('action-thread-notifications', 1, 1, 'incorrect'));
        $this->get($url);
    }

    public function testMuteNotificationWhenThreadDoesNotExist()
    {
        $this->expectException(PageNotFoundException::class);
        $this->expectExceptionMessage('This thread does not exist.');

        model(NotificationSettingModel::class)->update(1, ['email_thread' => 1]);

        $url = $this->convertToPath(signedurl()->urlTo('action-thread-notifications', 1, 99999, 'mute'));
        $this->get($url);
    }

    public function testMuteNotificationWhenGlobalNotificationsAreDisabled()
    {
        $this->expectException(PageNotFoundException::class);
        $this->expectExceptionMessage('All your notifications are already disabled.');

        $url = $this->convertToPath(signedurl()->urlTo('action-thread-notifications', 1, 1, 'mute'));
        $this->get($url);
    }

    public function testMuteNotificationWhenGlobalNotificationsAreDisabledWithHtmxRequest()
    {
        $url      = $this->convertToPath(signedurl()->urlTo('action-thread-notifications', 1, 1, 'mute'));
        $response = $this->withHeaders([
            'HX-Request' => 'true',
        ])->get($url);

        $this->assertEmpty($response->response()->getBody());
    }
}
