<?php

namespace App\Services\FrontEndPath;

use App\Http\DTO\FrondEndPathMainHero\CreateMainHeroData;
use App\Http\DTO\FrondEndPathMainHero\UpdateMainHeroData;
use App\Http\Responses\FrontEndPath\MainHeroResponse;
use App\Models\FrontEndPath\MainHero;
use App\Models\User;
use App\Services\Media\Contracts\FileUploaderInterface;
use App\Services\Media\SingleFileUploader;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

readonly class MainHeroService
{
    public function __construct(
        protected SingleFileUploader $singleFileUploader,
    )
    {
    }

    /**
     * Create a new main hero.
     * @throws \Throwable
     */
    public function createNewMainHero(CreateMainHeroData $data, FileUploaderInterface $uploader): MainHeroResponse
    {
        return DB::transaction(function () use ($data, $uploader) {
            $imagePath = null;

            if ($data->image) {
                $imagePath = $uploader->upload($data->image, 'heroes');
            }

            $hero = MainHero::create([
                'title' => $data->title,
                'has_discount' => $data->hasDiscount,
                'discount_label' => $data->discountLabel,
                'discount_content' => $data->discountContent,
                'discount_value' => $data->discountValue,
                'discount_subtitle' => $data->discountSubtitle,
                'order_index' => $data->orderIndex,
                'is_active' => $data->isActive,
                'description' => $data->description,
                'target' => $data->target ?? '_self',
                'image' => $imagePath,
                'button_action' => $data->buttonAction,
                'total_items' => $data->totalItems,
            ]);

            return MainHeroResponse::fromModel($hero);
        });
    }

    /*
     * Get public main hero
     */
    public function getMainHeroes(?User $user): Collection
    {
        $roleNames = ['PUBLIC'];

        if ($user && $user->roles) {
            $roleNames = array_unique(array_merge(
                $roleNames,
                $user->roles->pluck('name')->toArray()
            ));
        }

        return MainHero::query()
            ->where('is_active', true)
            ->orderBy('order_index')
            ->get()
            ->map(fn($hero) => MainHeroResponse::fromModel($hero));
    }

    /*
     * Update main hero
     */
    public function updateMainHero(
        int                   $id,
        UpdateMainHeroData    $data,
        FileUploaderInterface $uploader
    ): MainHeroResponse
    {
        Log::info('UpdateMainHeroData', [
            'title' => $data->title,
            'hasDiscount' => $data->hasDiscount,
        ]);

        return DB::transaction(function () use ($id, $data, $uploader) {

            $hero = MainHero::findOrFail($id);

            $hero->fill(array_filter([
                'title' => $data->title,
                'has_discount' => $data->hasDiscount,
                'discount_label' => $data->discountLabel,
                'discount_content' => $data->discountContent,
                'discount_value' => $data->discountValue,
                'discount_subtitle' => $data->discountSubtitle,
                'order_index' => $data->orderIndex,
                'is_active' => $data->isActive,
                'description' => $data->description,
                'target' => $data->target,
                'button_action' => $data->buttonAction,
                'total_items' => $data->totalItems,
            ], fn($v) => $v !== null));

            if ($data->image instanceof UploadedFile) {
                $hero->image = $uploader->upload($data->image, 'heroes');
            }

            $hero->save();

            return MainHeroResponse::fromModel($hero);
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $menu = MainHero::findOrFail($id);

            $menu->roles()->detach();
            $menu->delete();
        });
    }
}
