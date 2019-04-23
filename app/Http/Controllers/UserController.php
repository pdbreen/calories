<?php

namespace App\Http\Controllers;

use App\Http\Requests\MealIndexRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\MealResource;
use App\Http\Resources\UserResource;
use App\Meal;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['register', 'login']);
    }

    /**
     * Handles Registration Request
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        /** @var User $user */
        $user = User::create($request->validated());
        return response()->json(['token' => $user->access_token], 200);
    }

    /**
     * Handles Login Request
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request)
    {
        if (auth()->attempt($request->validated())) {
            return response()->json(['token' => auth()->user()->access_token], 200);
        }

        return response()->json(['error' => 'Unauthorised'], 401);
    }

    /**
     * Returns Authenticated User Details
     *
     * @return UserResource
     */
    public function details()
    {
        return (new UserResource(auth()->user()));
    }

    /**
     * Fetch list of meals for given user
     *
     * @param MealIndexRequest $request
     * @param User $user
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function meals(MealIndexRequest $request, User $user)
    {
        $authUser = auth()->user();
        if ($authUser->is_admin || $authUser->id === $user->id) {
            return MealResource::collection(
                Meal::query()
                    ->byUser($user)
                    ->dateTimeFiltered($request->validated())
                    ->withDailyCalorieTotals()
                    ->orderBy('eaten_at', 'desc')
                    ->paginate(10)
            );
        }

        abort(403);
    }

    /**
     * Fetch list of users
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user->is_admin || $user->is_user_manager) {
            return UserResource::collection(
                User::query()->paginate(10)
            );
        }

        abort(403);
    }

    /**
     * Show user detail
     *
     * @param User $user
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);
        return (new UserResource($user));
    }

    /**
     * Create new user
     *
     * @param StoreUserRequest $request
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);
        $model = User::query()->create($request->validated());
        return (new UserResource($model))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Update existing user
     *
     * @param UpdateUserRequest $request
     * @param User $user
     * @return UserResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);
        $user->fill($request->validated());
        $user->save();
        return (new UserResource($user));
    }

    /**
     * Delete existing user
     *
     * @param User $user
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        $user->delete();

        return response()->json(null, 204);
    }
}
