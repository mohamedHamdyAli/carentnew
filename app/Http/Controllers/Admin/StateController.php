<?php

namespace App\Http\Controllers\Admin;

use App\Models\State;
use App\Helpers\CacheHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateStateRequest;
use App\Http\Requests\UpdateStateRequest;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit\TextUI\XmlConfiguration\Logging\TeamCity;

class StateController extends Controller
{
    //create city
    public function createState(CreateStateRequest $request)
    {
        $state = State::create($request->validated());

        cache()->tags(['vehicles', 'states'])->flush();

        return response($state, Response::HTTP_CREATED);
    }

    public function getSingleState($id)
    {
        $data = cache()->tags(['states'])->remember(CacheHelper::makeKey('states_'.$id), 600, function () use ($id) {
            return DB::table('states as s')->where('s.id', $id)
            ->select('s.id', 's.name_en', 's.name_ar', 's.country_id', 's.active','countries.name_en as country_name_en','countries.name_ar as country_name_ar')
            ->join('countries', 'countries.id', '=', 's.country_id')
            ->first();
        });

        return $data;
    }

    public function updateState(UpdateStateRequest $request, $id)
    {
        $state = State::whereId($id)->firstOrFail();
        $state->update($request->validated());

        cache()->tags(['vehicles', 'states'])->flush();

        return response($state, Response::HTTP_OK);
    }

}
