<?php

namespace Pterodactyl\Http\Controllers\Api\Client;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Pterodactyl\Models\Announcement;
use Pterodactyl\Models\AnnouncementRead;

class AnnouncementsController extends ClientApiController
{
    /**
     * Retrieve all active announcements for the user.
     */
    public function index(Request $request): array
    {
        $user = $request->user();
        
        $announcements = Announcement::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', Carbon::now());
            })
            ->whereNotIn('id', function($query) use ($user) {
                $query->select('announcement_id')
                      ->from('announcement_reads')
                      ->where('user_id', $user->id);
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'object' => 'list',
            'data' => $announcements->map(function ($item) {
                return [
                    'object' => Announcement::RESOURCE_NAME,
                    'attributes' => [
                        'id' => $item->id,
                        'title' => $item->title,
                        'content' => $item->content,
                        'type' => $item->type,
                        'priority' => $item->priority,
                        'target_display' => $item->target_display,
                    ]
                ];
            })->toArray()
        ];
    }

    /**
     * Mark an announcement as read.
     */
    public function markRead(Request $request, int $id): JsonResponse
    {
        $announcement = Announcement::findOrFail($id);

        AnnouncementRead::firstOrCreate([
            'user_id' => $request->user()->id,
            'announcement_id' => $announcement->id,
        ]);

        return new JsonResponse([], JsonResponse::HTTP_NO_CONTENT);
    }
}
