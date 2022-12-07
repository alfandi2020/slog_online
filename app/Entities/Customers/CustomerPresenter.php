<?php

namespace App\Entities\Customers;

use Laracasts\Presenter\Presenter;

class CustomerPresenter extends Presenter
{
    public function numberLink()
    {
        return $this->customerLink($this->account_no);
    }

    public function nameLink()
    {
        return $this->customerLink($this->name);
    }

    public function numberName()
    {
        return $this->account_no.' - '.$this->name;
    }

    public function numberNameLink()
    {
        return $this->customerLink($this->numberName());
    }

    public function addresses()
    {
        return implode(', ', $this->address);
    }

    private function customerLink(string $label)
    {
        return link_to_route('customers.show', $label, [$this->id], ['title' => 'Lihat detail customer '.$this->numberName()]);
    }
}
