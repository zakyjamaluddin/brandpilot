<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\Checkbox;
use Filament\Schemas\Schema;

class Login extends BaseLogin
{
    public function form(Schema $schema): Schema
    {
        $remember = $this->getRememberFormComponent();

        if ($remember instanceof Checkbox) {
            $remember->default(true);
        }

        return $schema->components([
            $this->getEmailFormComponent(),
            $this->getPasswordFormComponent(),
            $remember,
        ]);
    }
}
