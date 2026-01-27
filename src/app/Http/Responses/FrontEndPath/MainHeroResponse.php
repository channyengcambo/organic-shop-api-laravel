<?php

namespace App\Http\Responses\FrontEndPath;

use App\Models\FrontEndPath\MainHero;
use Illuminate\Support\Facades\Storage;

class MainHeroResponse
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public int     $id,
        public string  $title,
        public bool    $hasDiscount,
        public ?string $discountLabel,
        public ?string $discountContent,
        public ?float  $discountValue,
        public ?string $discountSubtitle,
        public int     $orderIndex,
        public bool    $isActive,
        public ?string $description,
        public ?string $target,
        public ?string $image,
        public ?string $buttonAction,
    )
    {
    }

    public static function fromModel(MainHero $hero): self
    {
        return new self(
            id: $hero->id,
            title: $hero->title,
            hasDiscount: $hero->has_discount,
            discountLabel: $hero->discount_label,
            discountContent: $hero->discount_content,
            discountValue: $hero->discount_value,
            discountSubtitle: $hero->discount_subtitle,
            orderIndex: $hero->order_index,
            isActive: $hero->is_active,
            description: $hero->description,
            target: $hero->target,
            image: $hero->image
                ? Storage::url($hero->image)
                : null,
            buttonAction: $hero->button_action,
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
