<?php
class Transformer
{
    public static function transform ($array, $definition)
    {
        $r = array();
    }
}
$definition = array(
    'httpMethod' => '/$HTTP_VERB',
    'notes' => '/$HTTP_VERB/$URI/metadata/description',
    'nickName' => array('/$HTTP_VERB/$URI/metadata/className',':','/$HTTP_VERB/$URI/metadata/methodName'),
);

