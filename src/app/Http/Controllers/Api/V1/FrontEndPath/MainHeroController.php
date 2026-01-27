<?php

namespace App\Http\Controllers\Api\V1\FrontEndPath;

use App\Http\Controllers\Controller;
use App\Http\DTO\FrondEndPathMainHero\CreateMainHeroData;
use App\Http\DTO\FrondEndPathMainHero\UpdateMainHeroData;
use App\Http\Requests\FrontEndPath\MainHero\CreateMainHeroRequest;
use App\Http\Requests\FrontEndPath\MainHero\UpdateMainHeroRequest;
use App\Services\FrontEndPath\MainHeroService;
use App\Services\Media\Contracts\FileUploaderInterface;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

class MainHeroController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected MainHeroService $mainHeroService
    )
    {
    }

    /**
     * @throws \Throwable
     */
    public function store(CreateMainHeroRequest $request, FileUploaderInterface $uploader)
    {
        $data = CreateMainHeroData::fromRequest($request);

        $hero = $this->mainHeroService->createNewMainHero($data, $uploader);

        return $this->successResponse(
            $hero->toArray(),
            'Hero created successfully',
            201
        );
    }

    public function getAllPublicMainHeroes()
    {
        $heroes = $this->mainHeroService->getMainHeroes(Auth::user());
        return $this->successResponse(
            $heroes->map->toArray(),
            'Main heroes loaded'
        );

    }

    public function updateMainHero(
        int                   $id,
        UpdateMainHeroRequest $request,
        FileUploaderInterface $uploader
    )
    {
        $data = UpdateMainHeroData::fromRequest($request);

        $hero = $this->mainHeroService->updateMainHero($id, $data, $uploader);

        return $this->successResponse(
            $hero->toArray(),
            'Main hero edited successfully'
        );
    }

    public function destroy(int $id)
    {
        $this->mainHeroService->delete($id);
        return $this->successResponse(
            "Main hero deleted!"
        );
    }
}
