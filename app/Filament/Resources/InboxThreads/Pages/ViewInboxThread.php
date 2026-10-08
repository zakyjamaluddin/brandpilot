<?php

namespace App\Filament\Resources\InboxThreads\Pages;


use App\Filament\Resources\InboxThreads\InboxThreadResource;
use App\Models\InboxMessage;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewInboxThread extends ViewRecord
{
    protected static string $resource = InboxThreadResource::class;
    protected string $view = 'filament.resources.inbox.view-thread';

    public ?string $manualReplyText = '';

    public function sendManualReply(): void
    {
        $this->validate([
            'manualReplyText' => 'required|min:1',
        ]);

        // Simpan balasan manual dari user
        InboxMessage::create([
            'inbox_thread_id' => $this->record->id,
            'sender_type' => 'user',
            'message' => $this->manualReplyText,
            'sent_at' => now(),
        ]);

        $this->record->update([
            'last_snippet' => 'Anda: ' . $this->manualReplyText,
            'last_interaction_at' => now(),
        ]);

        $this->manualReplyText = '';

        Notification::make()
            ->title('Pesan Terkirim!')
            ->body('Balasan manual berhasil dikirim ke platform.')
            ->success()
            ->send();
    }
}