<?php

namespace App\Services\EmailIntelligence;

use App\Models\EmailMessage;
use App\Models\State;
use App\Models\Topic;
use Illuminate\Support\Collection;

interface EmailIntelligenceService
{
    /**
     * @param  Collection<int, Topic>  $topics
     * @param  Collection<int, State>  $states
     */
    public function classify(EmailMessage $message, Collection $topics, Collection $states): EmailClassificationResult;
}
