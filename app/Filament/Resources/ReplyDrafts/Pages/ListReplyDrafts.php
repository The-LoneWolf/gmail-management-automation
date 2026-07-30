<?php

namespace App\Filament\Resources\ReplyDrafts\Pages;

use App\Filament\Resources\ReplyDrafts\ReplyDraftResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReplyDrafts extends ListRecords
{
    protected static string $resource = ReplyDraftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
