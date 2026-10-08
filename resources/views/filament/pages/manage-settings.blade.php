<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
            <button type="submit" 
                    style="background: linear-gradient(135deg, #7c2a41 0%, #5b0e2d 100%); color: #fff; border: 1px solid #B76E79; padding: 10px 24px; border-radius: 10px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 15px rgba(91,14,45,0.4);">
                Simpan Perubahan
            </button>
        </div>
    </form>
</x-filament-panels::page>