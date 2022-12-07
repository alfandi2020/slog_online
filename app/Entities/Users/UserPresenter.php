<?php

namespace App\Entities\Users;

use Html;
use Laracasts\Presenter\Presenter;

class UserPresenter extends Presenter
{
    public function networkName()
    {
        return $this->network_id ? $this->network->name : '';
    }

    public function showLink($data = [])
    {
        $linkOptions = array_merge(['id' => 'show-user-' . $this->id, 'icon' => 'search', 'title' => trans('user.show')], $data);
        return html_link_to_route('admin.users.show', '', [$this->id], $linkOptions);
    }

    public function editLink($data = [])
    {
        $linkOptions = array_merge(['id' => 'edit-user-' . $this->id, 'icon' => 'edit', 'title' => trans('user.edit')], $data);
        return html_link_to_route('admin.users.edit', '', [$this->id], $linkOptions);
    }

    public function deleteLink($data = [])
    {
        $linkOptions = array_merge(['id' => 'del-user-' . $this->entity->id, 'class' => 'btn btn-danger pull-right'], $data);
        return html_link_to_route('admin.users.edit', trans('user.delete'), [$this->entity->id, 'action' => 'delete'], $linkOptions);
    }
}