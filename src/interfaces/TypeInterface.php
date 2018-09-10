<?php

namespace albertborsos\cookieconsent\interfaces;

interface TypeInterface
{
    const TYPE_FACEBOOK       = 'facebook';
    const TYPE_FACEBOOK_APP   = 'facebook-app';
    const TYPE_FACEBOOK_PIXEL = 'facebook-pixel';

    const TYPE_GOOGLE_ANALYTICS        = 'google-analytics';
    const TYPE_GOOGLE_TAG_MANAGER      = 'google-tag-manager';
    const TYPE_GOOGLE_MAPS             = 'google-maps';

    const TYPE_HOTJAR = 'hotjar';
}
