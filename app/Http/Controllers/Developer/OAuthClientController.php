<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Developer\StoreOAuthClientRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;

class OAuthClientController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected ClientRepository $clientRepository
    ) {}

    /**
     * Display the list of OAuth clients for the authenticated user.
     */
    public function index(Request $request): Response
    {
        $clients = $request->user()
            ->oauthApps()
            ->where('revoked', false)
            ->orderBy('name')
            ->get()
            ->map(fn (Client $client) => [
                'id' => $client->id,
                'name' => $client->name,
                'redirect_uris' => $client->redirect_uris,
                'created_at' => $client->created_at->toISOString(),
            ]);

        return Inertia::render('Developer', [
            'clients' => $clients,
        ]);
    }

    /**
     * Store a newly created OAuth client.
     */
    public function store(StoreOAuthClientRequest $request): RedirectResponse
    {
        $client = $this->clientRepository->createAuthorizationCodeGrantClient(
            name: $request->validated('name'),
            redirectUris: [$request->validated('redirect_url')],
            confidential: true,
            user: $request->user(),
        );

        return to_route('developer.index')->with('client', [
            'id' => $client->id,
            'name' => $client->name,
            'secret' => $client->plainSecret,
            'redirect_uris' => $client->redirect_uris,
        ]);
    }

    /**
     * Revoke the specified OAuth client.
     */
    public function destroy(Request $request, string $clientId): RedirectResponse
    {
        $client = $request->user()
            ->oauthApps()
            ->where('revoked', false)
            ->findOrFail($clientId);

        $client->tokens()->each(function ($token) {
            $token->refreshToken?->revoke();
            $token->revoke();
        });

        $client->forceFill(['revoked' => true])->save();

        return to_route('developer.index');
    }
}
