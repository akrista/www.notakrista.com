<?php

declare(strict_types=1);

namespace App\Services;

final class TodoticketCalculator
{
    /**
     * Calculate optimal withdrawal and commission for Todoticket vouchers.
     *
     * @return array{retiroOptimo: float, comision: float, restante: float}
     */
    public static function calculate(float $total): array
    {
        if ($total <= 0) {
            return [
                'retiroOptimo' => 0.0,
                'comision' => 0.0,
                'restante' => 0.0,
            ];
        }

        $retiroOptimo = floor(($total / 1.006) * 100) / 100;

        $iterations = 0;
        $maxIterations = 10;

        while ($iterations < $maxIterations) {
            $comision = round($retiroOptimo * 0.006 * 100) / 100;
            $restante = $total - ($comision + $retiroOptimo);
            $restante = round($restante * 100) / 100;

            if ($restante <= 0 || abs($restante) < 0.001) {
                $restante = 0.0;

                break;
            }

            $nuevoRetiroOptimo = $retiroOptimo + $restante;
            $nuevoRetiroOptimoRedondeado = floor($nuevoRetiroOptimo * 100) / 100;

            if ($nuevoRetiroOptimoRedondeado <= $retiroOptimo && $restante > 0) {
                $nuevoRetiroOptimoRedondeado = ceil($nuevoRetiroOptimo * 100) / 100;
            }

            $retiroOptimo = $nuevoRetiroOptimoRedondeado;
            $iterations++;
        }

        $comision = round($retiroOptimo * 0.006 * 100) / 100;
        $restante = $total - ($comision + $retiroOptimo);
        $restante = round($restante * 100) / 100;

        if ($retiroOptimo + $comision > $total) {
            $retiroOptimo = round(($retiroOptimo - 0.01) * 100) / 100;
            $comision = round($retiroOptimo * 0.006 * 100) / 100;
            $restante = $total - ($comision + $retiroOptimo);
            $restante = round($restante * 100) / 100;
        }

        if ($restante <= 0 || abs($restante) < 0.001) {
            $restante = 0.0;
        }

        return [
            'retiroOptimo' => (float) $retiroOptimo,
            'comision' => (float) $comision,
            'restante' => (float) $restante,
        ];
    }
}
