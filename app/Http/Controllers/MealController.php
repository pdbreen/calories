<?php

namespace App\Http\Controllers;

use App\Http\Requests\MealIndexRequest;
use App\Http\Requests\StoreMealRequest;
use App\Http\Requests\UpdateMealRequest;
use App\Http\Resources\MealResource;
use App\Meal;
use Illuminate\Http\Request;

class MealController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Fetch meals, optionally filtered by start/end date & time
     *
     * @param MealIndexRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(MealIndexRequest $request)
    {
        return MealResource::collection(
            Meal::query()
                ->byUser(auth()->user())
                ->dateTimeFiltered($request->validated())
                ->withDailyCalorieTotals()
                ->orderBy('eaten_at', 'desc')
                ->paginate(10)
        );
    }

    /**
     * Get meal details
     *
     * @param Meal $meal
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Meal $meal)
    {
        $this->authorize('view', $meal);
        return (new MealResource($meal));
    }

    /**
     * Add a new meal
     *
     * @param StoreMealRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreMealRequest $request)
    {
        $this->authorize('create', Meal::class);
        $meal = Meal::query()->create($request->validated());
        return (new MealResource($meal))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Update existing meal
     *
     * @param UpdateMealRequest $request
     * @param Meal $meal
     * @return MealResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateMealRequest $request, Meal $meal)
    {
        $this->authorize('update', $meal);
        $meal->update($request->validated());
        return (new MealResource($meal));
    }

    /**
     * Delete a meal
     *
     * @param Meal $meal
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Meal $meal)
    {
        $this->authorize('delete', $meal);
        $meal->delete();
        return response()->json(null, 204);
    }
}
