<?php

namespace App\Filament\Resources\ReplyDrafts\Pages;

use App\Filament\Resources\ReplyDrafts\ReplyDraftResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditReplyDraft extends EditRecord
{
    protected static string $resource = ReplyDraftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
