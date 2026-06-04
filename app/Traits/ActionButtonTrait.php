<?php



namespace App\Traits;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;

trait ActionButtonTrait
{
    public function generateActionButtons($row, $routes = [], $permissions = [], $icons = [], $colors = [])
    {
        $id = Crypt::encrypt($row->id);
        $buttons = [];
        $icons = [
            ...$this->getAllIcons() ?? [],
            ...$icons
        ];

        $colors = [
            ...$this->getAllColors() ?? [],
            ...$colors
        ];

        
        foreach ($routes as $action => $route) {
            if (isset($permissions[$action]) && Auth::user()->hasPermission($permissions[$action])) {
                $icon = $icons[$action] ?? 'fas fa-question'; // Default icon if not specified
                $color = $colors[$action] ?? 'secondary'; // Default color if not specified
                $buttons[] = "<a href='" . route($route, ['id' => $id]) . "' class='btn btn-$color btn-sm'><i class='$icon'></i></a>";
            }
        }

        return $buttons;
    }

    private function getButtonClass($action)
    {
        $classes = [
            'view' => 'warning',
            'edit' => 'primary',
            'delete' => 'danger',
        ];

        return $classes[$action] ?? 'secondary';
    }

    private function getAllColors()
    {
        return  [
            'view' => 'warning',
            'edit' => 'primary',
            'delete' => 'danger',
        ] ?? [];
    }

    private function getButtonIcon($action)
    {

        return $icons[$action] ?? 'fa-question';
    }

    private function getAllIcons()
    {
        return [
            'view' => 'fas fa-eye',
            'edit' => 'fas fa-edit',
            'delete' => 'fas fa-trash-alt',
        ] ?? [];
    }
}
