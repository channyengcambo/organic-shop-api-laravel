<?php

namespace App\Http\Controllers\Api\V1\FrontEndPath;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminPath\NavigationMenuItemRequests\CreatNavigationMenuItemRequest;
use App\Http\Requests\FrontEndPath\NavigationMenuItemRequests\CreateNavigationMenuItemRequest;
use App\Http\Requests\FrontEndPath\NavigationMenuItemRequests\UpdateNavigationMenuItemRequest;
use App\Services\FrontEndPath\NavigationMenuItemService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Cache;

class NavigationMenuItemsController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected NavigationMenuItemService $navigationMenuItemService
    )
    {
    }

    public function index()
    {
        $menus = $this->navigationMenuItemService->getMenusForUser(auth()->user());

        return $this->successResponse(
            $menus,
            'Navigation menu items loaded successfully!'
        );
    }

    public function store(CreateNavigationMenuItemRequest $request)
    {
        $menu = $this->navigationMenuItemService->create($request->validated());

        return $this->successResponse(
            $menu,
            'Navigation menu item created successfully!',
            201
        );
    }

    public function storeChild(CreateNavigationMenuItemRequest $request, int $parentId)
    {
        $child = $this->navigationMenuItemService->createChild(
            $parentId,
            $request->validated()
        );

        return $this->successResponse($child, 'Child menu created successfully!', 201);
    }

    public function update(UpdateNavigationMenuItemRequest $request, int $id)
    {
        $menu = $this->navigationMenuItemService->update($id, $request->validated());

        return $this->successResponse(
            $menu,
            'Menu updated successfully'
        );
    }

    public function destroy(int $id)
    {
        $this->navigationMenuItemService->delete($id);

        return $this->successResponseNoData(
            'Menu deleted successfully'
        );
    }
}
