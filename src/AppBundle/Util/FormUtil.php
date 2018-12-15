<?php

namespace AppBundle\Util;

use Symfony\Component\Form\FormEvent;

class FormUtil
{
    /**
     * @param FormEvent $event
     * @param $dataKey
     */
    public static function removeWhiteSpaces(FormEvent $event, $dataKey)
    {
        $data = $event->getData();
        $data[$dataKey] = preg_replace('/\s+/', ' ', $data[$dataKey]);
        $event->setData($data);
    }
}
