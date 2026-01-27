<?php

namespace App\Http\DTO\FrondEndPathMainHero;

use App\Http\Requests\FrontEndPath\MainHero\CreateMainHeroRequest;
use Illuminate\Http\UploadedFile;

readonly class CreateMainHeroData
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public string        $title,
        public bool          $hasDiscount,
        public ?string       $discountLabel,
        public ?string       $discountContent,
        public ?float        $discountValue,
        public ?string       $discountSubtitle,
        public int           $orderIndex,
        public bool          $isActive,
        public ?string       $description,
        public ?string       $target,
        public ?UploadedFile $image,
        public ?string       $buttonAction,
        public int           $totalItems,
    )
    {
    }

    public static function fromRequest(CreateMainHeroRequest $request): self
    {
        return new self(
            title: $request->title,
            hasDiscount: $request->has_discount,
            discountLabel: $request->discount_label,
            discountContent: $request->discount_content,
            discountValue: $request->discount_value,
            discountSubtitle: $request->discount_subtitle,
            orderIndex: $request->order_index,
            isActive: $request->is_active,
            description: $request->description,
            target: $request->target,
            image: $request->image,
            buttonAction: $request->button_action,
            totalItems: $request->total_items ?? 0,
        );
    }
}
