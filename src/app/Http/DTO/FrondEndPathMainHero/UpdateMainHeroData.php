<?php

namespace App\Http\DTO\FrondEndPathMainHero;

use App\Http\Requests\FrontEndPath\MainHero\UpdateMainHeroRequest;

class UpdateMainHeroData
{
    public function __construct(
        public ?string $title,
        public ?bool   $hasDiscount,
        public ?string $discountLabel,
        public ?string $discountContent,
        public ?float  $discountValue,
        public ?string $discountSubtitle,
        public ?int    $orderIndex,
        public ?bool   $isActive,
        public ?string $description,
        public ?string $target,
        public ?string $buttonAction,
        public ?int    $totalItems,
        public         $image,
    )
    {
    }

    public static function fromRequest(UpdateMainHeroRequest $request): self
    {
        return new self(
            title: $request->input('title'),
            hasDiscount: $request->input('has_discount'),
            discountLabel: $request->input('discount_label'),
            discountContent: $request->input('discount_content'),
            discountValue: $request->input('discount_value'),
            discountSubtitle: $request->input('discount_subtitle'),
            orderIndex: $request->input('order_index'),
            isActive: $request->input('is_active'),
            description: $request->input('description'),
            target: $request->input('target'),
            buttonAction: $request->input('button_action'),
            totalItems: $request->input('total_items'),
            image: $request->file('image'),
        );
    }

}
