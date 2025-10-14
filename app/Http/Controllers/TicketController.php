<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Traits\ServiceNowStateMapper;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class TicketController extends Controller
{
    use ServiceNowStateMapper;

    private Client $client;
    private string $serviceNowUrl;

    public function __construct()
    {
        $this->serviceNowUrl = env('SERVICE_NOW_URL');
        $username = env('SERVICE_NOW_USERNAME');
        $password = env('SERVICE_NOW_PASSWORD');

        if (!$this->serviceNowUrl || !$username || !$password) {
            throw new \Exception('ServiceNow environment variables are not properly configured.');
        }

        $this->client = new Client([
            'base_uri' => $this->serviceNowUrl,
            'auth' => [$username, $password],
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ]);
    }

    /**
     * Show Create Ticket Form
     */
    public function create()
    {
        return view('tickets.create');
    }

    /**
     * Store Ticket to DB and optionally sync with ServiceNow
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sync_option' => ['required', Rule::in(['synced', 'not_synced'])],
        ]);

        $ticket = Ticket::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'syncing_status' => $validatedData['sync_option'],
            'status' => 'New',
        ]);

        if ($ticket->syncing_status === 'synced') {
            return $this->syncAndRedirect($ticket, 'Ticket created and synced successfully!');
        }

        return redirect()->route('dashboard')
            ->with('success', 'Ticket created locally without syncing.');
    }

    /**
     * Sync All Local Tickets to ServiceNow
     */
    public function syncAllTickets()
    {
        set_time_limit(0); 

        $tickets = Ticket::where('syncing_status', 'not_synced')->get();
        $syncResults = [];

        foreach ($tickets as $ticket) {
            $result = $this->syncTicketToServiceNow($ticket);
            $syncResults[] = $result['message'];
        }

        return redirect()->route('dashboard')->with('sync_results', $syncResults);
    }

    /**
     * Sync a Single Ticket (Manual Sync Button)
     */
    public function syncSingleTicket(Ticket $ticket)
    {
        set_time_limit(0); 
        
        if ($ticket->syncing_status === 'synced') {
            return redirect()->route('dashboard')
                ->with('error', "Ticket #{$ticket->id} is already synced.");
        }

        return $this->syncAndRedirect($ticket, "Ticket #{$ticket->id} synced successfully!");
    }

    /**
     * Handle Sync and Redirect Response
     */
    private function syncAndRedirect(Ticket $ticket, string $successMessage)
    {
        $result = $this->syncTicketToServiceNow($ticket);

        if ($result['success']) {
            $ticket->update([
                'syncing_status' => 'synced',
                'status' => $result['status'],
            ]);

            return redirect()->route('dashboard')
                ->with('success', $successMessage);
        }

        return redirect()->route('dashboard')
            ->with('error', "Ticket #{$ticket->id} failed to sync: " . $result['message']);
    }

    /**
     * Core Logic: Sync Ticket with ServiceNow
     */
    private function syncTicketToServiceNow(Ticket $ticket): array
    {
        try {
            $payload = [
                'short_description' => $ticket->title,
                'description' => $ticket->description,
            ];

            if (!$ticket->service_now_id) {
                // New Ticket - POST
                $response = $this->client->post('/api/now/table/incident', ['json' => $payload]);
            } else {
                // Existing Ticket - PATCH
                $response = $this->client->patch('/api/now/table/incident/' . $ticket->service_now_id, ['json' => $payload]);
            }

            $body = json_decode($response->getBody()->getContents());

            // Check if API response is valid before accessing properties
            if (!isset($body->result->state) || !isset($body->result->sys_id)) {
                Log::error("ServiceNow API response missing expected fields.", ['response' => $body]);
                return [
                    'success' => false,
                    'status' => $ticket->status,
                    'message' => 'ServiceNow API response format is invalid or incomplete.'
                ];
            }

            $mappedStatus = $this->mapServiceNowState($body->result->state);

            $ticket->update([
                'service_now_id' => $body->result->sys_id,
                'status' => $mappedStatus,
            ]);

            return [
                'success' => true,
                'status' => $mappedStatus,
                'message' => "Ticket #{$ticket->id} synced successfully (ServiceNow ID: {$body->result->number})."
            ];
        } catch (ClientException $e) {
            $errorBody = json_decode($e->getResponse()->getBody()->getContents());
            $errorMessage = $errorBody->error->message ?? $e->getMessage();
            Log::error("Client error syncing ticket #{$ticket->id}", ['error' => $errorMessage]);

            return [
                'success' => false,
                'status' => $ticket->status,
                'message' => "Client error: " . $errorMessage
            ];
        } catch (RequestException $e) {
            Log::error("Failed to sync ticket #{$ticket->id} with ServiceNow", ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'status' => $ticket->status,
                'message' => "Network or connection error: " . $e->getMessage()
            ];
        }
    }
}