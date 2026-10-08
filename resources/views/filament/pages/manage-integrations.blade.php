<x-filament-panels::page>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        @foreach(['facebook' => ['name' => 'Facebook Page', 'color' => '#1877F2', 'desc' => 'Auto repost video ke postingan Page FB Anda.'],
                  'instagram' => ['name' => 'Instagram Reels', 'color' => '#E1306C', 'desc' => 'Auto publish ke Instagram Reels & feed.'],
                  'youtube'   => ['name' => 'YouTube Shorts', 'color' => '#FF0000', 'desc' => 'Auto upload video ke YouTube Shorts channel.']] as $key => $meta)
            
            @php $status = $integrations[$key] ?? ['is_connected' => false, 'account_name' => 'Belum Terhubung']; @endphp

            <div style="background: linear-gradient(135deg, #2D0713 0%, #1A040B 100%); border: 1px solid rgba(183, 110, 121, 0.35); border-radius: 16px; padding: 24px; position: relative;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 44px; height: 44px; border-radius: 10px; background: {{ $meta['color'] }}20; border: 1px solid {{ $meta['color'] }}60; display: flex; align-items: center; justify-content: center; font-weight: bold; color: {{ $meta['color'] }}; font-size: 18px;">
                            {{ strtoupper(substr($key, 0, 2)) }}
                        </div>
                        <div>
                            <h3 style="font-size: 16px; font-weight: 700; color: #fff; margin: 0;">{{ $meta['name'] }}</h3>
                            <span style="font-size: 12px; color: {{ $status['is_connected'] ? '#34d399' : '#9CA3AF' }};">
                                {{ $status['is_connected'] ? '● Terhubung' : '○ Terputus' }}
                            </span>
                        </div>
                    </div>
                </div>

                <p style="font-size: 13px; color: #D1D5DB; margin-bottom: 20px; line-height: 1.4;">{{ $meta['desc'] }}</p>

                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 16px;">
                    <span style="font-size: 12px; color: #C58F72;">{{ $status['account_name'] }}</span>

                    @if($status['is_connected'])
                        <button wire:click="disconnectPlatform('{{ $key }}')"
                                style="background: rgba(239, 68, 68, 0.2); color: #fca5a5; border: 1px solid rgba(239, 68, 68, 0.4); padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer;">
                            Disconnect
                        </button>
                    @else
                        <button wire:click="connectPlatform('{{ $key }}')"
                                style="background: linear-gradient(135deg, #7c2a41 0%, #5b0e2d 100%); color: #fff; border: 1px solid #B76E79; padding: 6px 16px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(91,14,45,0.4);">
                            Hubungkan via Composio
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>