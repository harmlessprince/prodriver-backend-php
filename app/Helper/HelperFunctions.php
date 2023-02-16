<?php

use Carbon\Carbon;

function fileExistsRule(\App\Models\User $user)
{
};

function generateTripID(): string
{
    return 'TR-' . Carbon::now()->timestamp;
}
