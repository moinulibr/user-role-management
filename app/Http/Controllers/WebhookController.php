<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Traits\ServiceNowStateMapper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    use ServiceNowStateMapper;

    /**
     * Handle incoming webhooks from ServiceNow to update ticket status.
     */
    public function handleServiceNowWebhook(Request $request)
    {
        Log::info('Webhook received from ServiceNow', $request->all());

        $payload = $request->all();

        // Check for the correct keys: sys_id and status
        if (!isset($payload['sys_id']) || !isset($payload['status'])) {
            Log::warning('Webhook payload is missing sys_id or status.', $payload);
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $serviceNowSysId = $payload['sys_id'];
        $serviceNowStatus = $payload['status'];

        $ticket = Ticket::where('service_now_id', $serviceNowSysId)->first();

        if (!$ticket) {
            Log::warning('Ticket not found in local database for sys_id: ' . $serviceNowSysId);
            return response()->json(['message' => 'Ticket not found'], 404);
        }

        // Use the trait method for mapping the state
        $mappedState = $this->mapServiceNowState($serviceNowStatus);

        $ticket->status = $mappedState;
        $ticket->save();

        Log::info("Ticket #{$ticket->id} updated to status: {$mappedState} (ServiceNow ID: {$serviceNowSysId})");

        return response()->json(['message' => 'Successfully updated ticket'], 200);
    }
}