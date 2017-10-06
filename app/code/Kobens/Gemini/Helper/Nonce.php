<?php

namespace Kobens\Gemini\Helper;

class Nonce
{
    public function getNonce()
    {
        $comps = explode(' ', microtime());
        return sprintf('%d%03d', $comps[1], $comps[0] * 1000);
    }

}
