<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 15.10.18
 * Time: 11:48.
 */

namespace App\Http\Controllers;

use App\Guild;
use App\Notification\Notification;
use App\Notification\System\DiscordSystem;
use App\Notification\System\SlackSystem;
use App\Rules\DiscordHookRule;
use App\Rules\SlackHookRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function messageTypeSelectView(string $slug)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        return view('guild.notification.message_select', compact('guild'));
    }

    public function systemTypeSelectView(string $slug, int $message_type)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        return view('guild.notification.system_select', compact('message_type', 'guild'));
    }

    public function createView(string $slug, int $message_type, int $system_type)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        return view('guild.notification.create', compact('message_type', 'guild', 'system_type'));
    }

    public function updateView(string $slug, int $notification_id)
    {
        $guild        = Guild::query()->where('slug', '=', $slug)->first();
        $notification = Notification::query()->find($notification_id);

        return view('guild.notification.update', compact('notification', 'guild'));
    }

    public function sendTestMessage(string $slug, int $notification_id)
    {
        $notification = Notification::query()->find($notification_id);
        $notification->sendTest();

        return redirect(route('guildSettingsView', ['slug' => $slug]));
    }

    public function create(Request $request, string $slug, int $message_type, int $system_type)
    {
        $request->validate([
            'name' => 'required',
        ]);
        if (DiscordSystem::SYSTEM_ID === $system_type) {
            $request->validate([
                'url' => new DiscordHookRule(),
            ]);
        }
        if (SlackSystem::SYSTEM_ID === $system_type) {
            $request->validate([
                'url' => new SlackHookRule(),
            ]);
        }
        $guild                   = Guild::query()->where('slug', '=', $slug)->first();
        $notification            = new Notification();
        $notification->call_type = $message_type;
        $notification->type      = $system_type;
        $notification->guild_id  = $guild->id;
        $notification            = $this->buildFromRequest($request->all(), $notification);
        $notification->save();

        return redirect(route('guildSettingsView', ['slug' => $slug]));
    }

    public function update(Request $request, string $slug, int $notification_id)
    {
        $request->validate([
            'name' => 'required',
        ]);
        $notification = Notification::query()->find($notification_id);
        if (DiscordSystem::SYSTEM_ID === $notification->type) {
            $request->validate([
                'url' => new DiscordHookRule(),
            ]);
        }
        if (SlackSystem::SYSTEM_ID === $notification->type) {
            $request->validate([
                'url' => new SlackHookRule(),
            ]);
        }
        $notification = $this->buildFromRequest($request->all(), $notification);
        $notification->save();

        return redirect(route('guildSettingsView', ['slug' => $slug]));
    }

    public function delete(string $slug, int $notification_id)
    {
        $notification = Notification::query()->find($notification_id);
        $notification->delete();

        return redirect(route('guildSettingsView', ['slug' => $slug]));
    }

    private function buildFromRequest(array $all, Notification $notification = null): Notification
    {
        if (empty($notification)) {
            $notification = new Notification();
        }
        if (!empty($all['call_time_diff'])) {
            $all['call_time_diff'] = 60 * $all['call_time_diff'];
        }
        if (!empty($all['hour']) && !empty($all['minute'])) {
            if (12 === Auth::user()->clock) {
                $t = $all['hour'].':'.$all['minute'].' '.$all['meridiem'];
            } else {
                $t = $all['hour'].':'.$all['minute'];
            }
            $notification->setDailyTriggerTime($t, Auth::user()->timezone);
            unset($all['hour'], $all['minute'], $all['meridiem']);
        }
        if (!empty($all['timezones'])) {
            $notification->timezones = json_encode($all['timezones']);
            unset($all['timezones']);
        }
        $notification->setTagsFromString($all['tags'] ?? '');
        unset($all['tags']);
        $notification->has_embeds = $all['has_embeds'] ?? 0;
        unset($all['has_embeds']);

        $notification->fill($all);

        return $notification;
    }
}
