<?php

namespace App\EditoraFront\Performance;

class TicTac
{
    private $timings=[];

    public function tic(string $tag): void
    {
        if (config('omatech.timings')) {
            $this->timings[$tag]['start']=microtime(true);
        }
    }

    public function tac(string $tag): void
    {
        if (config('omatech.timings')) {
            assert(isset($this->timings[$tag]['start']));
            $this->timings[$tag]['end']=microtime(true);
        }
    }

    public function getAll(): array
    {
        $res=[];
        if (config('omatech.timings')) {
            foreach ($this->timings as $tag=>$timing) {
                if (isset($timing['start']) && isset($timing['end'])) {
                    $res[$tag]=$timing['end']-$timing['start'];
                }
            }
        }
        return $res;
    }
}
