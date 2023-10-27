<?php

namespace Tests\Controllers;

use App\Entities\User;
use App\Models\NotificationMutedModel;
use App\Models\NotificationSettingModel;
use App\Models\UserDeleteModel;
use App\Models\UserModel;
use CodeIgniter\Events\Events;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\I18n\Time;
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

    public function testCancelAccountDeleteUserDoesNotExist()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The userId field must contain a previously existing value in the database.');

        $url = $this->convertToPath(signedurl()->urlTo('action-cancel-account-delete', 12345));
        $this->get($url);
    }

    public function testCancelAccountDeleteUserScheduleExpired()
    {
        model(UserDeleteModel::class)->insert([
            'user_id' => 1, 'scheduled_at' => Time::now()->subDays(1),
        ]);
        $this->expectException(PageNotFoundException::class);
        $this->expectExceptionMessage('This account is not scheduled to delete or the time frame to restore it has passed.');

        $url = $this->convertToPath(signedurl()->urlTo('action-cancel-account-delete', 1));
        $this->get($url);
    }

    public function testCancelAccountDeleteUserIsAlreadyRestored()
    {
        model(UserDeleteModel::class)->insert([
            'user_id' => 1, 'scheduled_at' => Time::now()->addDays(1),
        ]);
        $this->expectException(PageNotFoundException::class);
        $this->expectExceptionMessage('This account has already been restored.');

        $url = $this->convertToPath(signedurl()->urlTo('action-cancel-account-delete', 1));
        $this->get($url);
    }

    public function testCancelAccountDelete()
    {
        model(UserModel::class)->delete(1);
        Events::trigger('account-deleted', new User(['id' => 1]));

        $this->seeInDatabase('users', ['id' => 1, 'thread_count' => 0, 'post_count' => 0]);
        $this->seeInDatabase('users', ['id' => 2, 'thread_count' => 0, 'post_count' => 0]);
        $this->seeInDatabase('users_delete', ['user_id' => 1]);

        $url      = $this->convertToPath(signedurl()->urlTo('action-cancel-account-delete', 1));
        $response = $this->get($url);

        $response->assertOK();
        $response->assertRedirect();

        $this->seeInDatabase('users', ['id' => 1, 'thread_count' => 2, 'post_count' => 6]);
        $this->seeInDatabase('users', ['id' => 2, 'thread_count' => 0, 'post_count' => 2]);
        $this->dontSeeInDatabase('users_delete', ['user_id' => 1]);
    }
}
