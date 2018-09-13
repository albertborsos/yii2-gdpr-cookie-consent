<?php

namespace albertborsos\cookieconsent\interfaces;

interface CookieComponentInterface
{
    public function isAllowedCategory($category);

    public function isAllowedType($type);

    public function registerWidget($config = []);

    /**
     * @return CookieComponentInterface
     */
    public function getComponent();
}
