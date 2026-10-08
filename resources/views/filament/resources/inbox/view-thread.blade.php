<x-filament-panels::page>
    <div style="background: linear-gradient(135deg, #2D0713 0%, #1A040B 100%); border: 1px solid rgba(183, 110, 121, 0.35); border-radius: 16px; padding: 24px;">
        {{-- Header Thread --}}
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 16px; margin-bottom: 20px;">
            <div>
                <h2 style="font-size: 18px; font-weight: 700; color: #fff; margin: 0;">{{ $record->sender_name }}</h2>
                <span style="font-size: 12px; color: #C58F72;">Platform: {{ ucfirst($record->platform) }} ({{ strtoupper($record->type) }})</span>
            </div>
            <span style="font-size: 12px; color: #9CA3AF;">ID: {{ $record->external_id }}</span>
        </div>

        {{-- Bubble Percakapan --}}
        <div style="display: flex; flex-direction: column; gap: 14px; min-height: 250px; max-height: 450px; overflow-y: auto; padding: 10px;">
            @foreach($record->messages as $msg)
                @if($msg->sender_type === 'customer')
                    {{-- Pesan Masuk dari Customer --}}
                    <div style="align-self: flex-start; max-width: 75%; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: 14px 14px 14px 2px; padding: 12px 16px; color: #fff;">
                        <p style="margin: 0; font-size: 14px; line-height: 1.4;">{{ $msg->message }}</p>
                        <span style="font-size: 10px; color: #9CA3AF; margin-top: 4px; display: block;">{{ $msg->sent_at->format('H:i') }} • Customer</span>
                    </div>
                @elseif($msg->sender_type === 'ai')
                    {{-- Balasan Otomatis dari Gemini AI --}}
                    <div style="align-self: flex-end; max-width: 75%; background: linear-gradient(135deg, #7c2a41 0%, #5b0e2d 100%); border: 1px solid #B76E79; border-radius: 14px 14px 2px 14px; padding: 12px 16px; color: #fff; box-shadow: 0 4px 12px rgba(91,14,45,0.3);">
                        <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 4px;">
                            <span style="font-size: 10px; background: rgba(255,255,255,0.2); padding: 1px 6px; border-radius: 4px; color: #E7D6C8; font-weight: bold;">Gemini AI</span>
                        </div>
                        <p style="margin: 0; font-size: 14px; line-height: 1.4;">{{ $msg->message }}</p>
                        <span style="font-size: 10px; color: #E7D6C8; margin-top: 4px; display: block; text-align: right;">{{ $msg->sent_at->format('H:i') }}</span>
                    </div>
                @else
                    {{-- Balasan Manual dari User --}}
                    <div style="align-self: flex-end; max-width: 75%; background: #3B82F6; border-radius: 14px 14px 2px 14px; padding: 12px 16px; color: #fff;">
                        <p style="margin: 0; font-size: 14px; line-height: 1.4;">{{ $msg->message }}</p>
                        <span style="font-size: 10px; color: #DBEAFE; margin-top: 4px; display: block; text-align: right;">{{ $msg->sent_at->format('H:i') }} • Manual</span>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- Form Input Balas Manual --}}
        <div style="margin-top: 24px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 16px;">
            <div style="display: flex; gap: 12px;">
                <input type="text" 
                       wire:model="manualReplyText" 
                       wire:keydown.enter="sendManualReply"
                       placeholder="Ketik balasan manual ke customer..." 
                       style="flex: 1; background: rgba(0,0,0,0.4); border: 1px solid rgba(183, 110, 121, 0.4); border-radius: 10px; padding: 12px 16px; color: #fff; font-size: 14px; outline: none;">
                
                <button wire:click="sendManualReply" 
                        style="background: linear-gradient(135deg, #7c2a41 0%, #5b0e2d 100%); color: #fff; border: 1px solid #B76E79; border-radius: 10px; padding: 0 24px; font-weight: 600; cursor: pointer;">
                    Kirim
                </button>
            </div>
        </div>
    </div>
</x-filament-panels::page>